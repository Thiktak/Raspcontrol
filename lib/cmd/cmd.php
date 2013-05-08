<?php

namespace lib\cmd;

Class cmd
{
    public function __construct(array $params = null)
    {
        foreach( (array) $params as $key => $val )
            $this->$key = $val;
    }

    public static function create($name, array $params = null)
    {
        $name = '\\lib\\Cmd\\' . $name . 'Cmd';
        
        if( !class_exists($name) )
            throw new \Exception(sprintf('Class %s does not exists', $name));

        if( !is_subclass_of($name, '\\lib\\Cmd\\Cmd') )
            throw new \Exception(sprintf('Class %s must extends %s', $name, __CLAS__));

        return new $name($params);
    }

    protected function addPrintCmd($cmd, $after = null)
    {
        $options = self::cmdConfigure($this::configure());
        if( $options['printcmd'] )
            return '$> ' . $cmd . PHP_EOL . $after;
        return $after;
    }

    protected function system($cmd)
    {
        ob_start();
        system($cmd);
        return $this->addPrintCmd($cmd, ob_get_clean());
    }

    protected function shellExec($cmd)
    {
        return $this->addPrintCmd($cmd, shell_exec($cmd));
    }

    public function exec()
    {
        throw new \Exception('You must instance a new child class with exec() method !');
    }

    public static function cmdConfigure(array $datas = null)
    {
        return array_merge(array(
          'name'        => null,
          'description' => null,
          'icon'        => 'play',
          'runnable'    => true,
          'confirm'     => null,    // Specity if you want a mobal box in order to confirm execution
          'refresh'     => 0,       // Number of seconds before refresh
          'printcmd'    => false,
          'params'      => array(),
        ), $datas);
    }

    public static function configure() {}


    public static function getListOfCmd()
    {
        $dir = dirname(__FILE__);
        foreach( scandir($dir) as $file )
        {
            if( is_file($dir . '/' . $file) )
                include_once $dir . '/' . $file;
        }

        $cmds = array();
        foreach( get_declared_classes() as $class )
        {
            if( !is_subclass_of($class, __NAMESPACE__ . '\\cmd') )
                continue;

            $name = preg_replace('`^.*\\\\(.*)Cmd$`', '$1', $class);
            $cmds[$name] = self::cmdConfigure($class::configure());
        }

        return $cmds;
    }

    public function parseAction($content)
    {
        return $content;
    }
}
