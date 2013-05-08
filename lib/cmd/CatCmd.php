<?php

namespace lib\Cmd;

Class CatCmd extends Cmd
{
    protected $file;

    public static function configure() {
        return array(
            'name'        => 'Cat (with file)',
            'icon'        => 'file',
            'description' => 'Read a file',
            'runnable'    => false,
            'printcmd'    => true,
            'params'      => array(
                'file' => null,
            )
        );
    }

    public function parseAction($content) {
        return parent::parseAction(htmlspecialchars($content));
    }

    public function exec()
    {
        if( $this->file )
            return $this->system('sudo cat ' . rtrim($this->file, '/') . ' 2>&1');
    }
}