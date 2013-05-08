<?php

namespace lib\Cmd;

Class LsCmd extends Cmd
{
    protected $dir = '/';

    public static function configure() {
        return array(
            'name'        => 'Files Explorer',
            'icon'        => 'folder-open',
            'description' => 'Explore your files',
            'runnable'    => false,
            'printcmd'    => true,
            'params'      => array(
                'dir' => null,
            )
        );
    }

    /**
     * @source http://fr2.php.net/manual/fr/function.realpath.php#71334
     */
    public function canonicalize($address)
    {
        $address = explode('/', $address);
        $keys = array_keys($address, '..');

        foreach($keys AS $keypos => $key)
        {
            array_splice($address, $key - ($keypos * 2 + 1), 2);
        }

        $address = implode('/', $address);
        $address = str_replace('./', '', $address);
        return $address;
    }

    public function parseAction($content)
    {
        $dir = $this->dir ?: '/';

        $content = preg_replace_callback('`^([drxw-]+.* )([a-zA-Z0-9_\+\.\$~-]+)$`m', function($m) use($dir) {
            $r = $this->canonicalize($dir . $m[2]);
            return $m[1] . '<a href="?page=cmd&amp;cmd=Ls&amp;param[dir]=' . urlencode($r . '/') . '">' . $m[2] . '</a>';
        }, $content);

        $content = preg_replace_callback('`^([drxw-]+.* )/(.*)$`m', function($m) use($dir) {
            return $m[1] . '<a href="?page=cmd&amp;cmd=Cat&amp;param[file]=' . urlencode('/' . $m[2]) . '" style="color: lightgreen">[FILE] ' . $m[2] . '</a>';
        }, $content);

        $content = preg_replace_callback('`^(l.* )([a-zA-Z0-9_\+\$~/\.-]+)$`m', function($m) use($dir) {
            return $m[1] . '<a href="?page=cmd&amp;cmd=Ls&amp;param[dir]=' . urlencode($m[2] . '/') . '" style="color: red">' . $m[2] . '</a>';
        }, $content);

        return parent::parseAction($content);
    }

    public function exec()
    {
        return $this->system('sudo ls -al ' . (rtrim($this->dir, '/') ?: '/') . ' 2>&1');
    }
}