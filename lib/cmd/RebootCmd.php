<?php

namespace lib\Cmd;

Class RebootCmd extends Cmd
{
    public static function configure() {
        return array(
            'name'        => 'Reboot',
            'icon'        => 'repeat',
            'description' => 'reboot the raspberry',
            'confirm'     => true,
            'refresh'     => 15,
        );
    }


    public function exec()
    {
        return $this->system('sudo reboot 2>&1');
    }
}
