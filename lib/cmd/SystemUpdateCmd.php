<?php

namespace lib\Cmd;

Class SystemUpdateCmd extends Cmd
{
    public static function configure() {
        return array(
            'name'        => 'Update',
            'icon'        => 'hand-up',
            'description' => 'Update and upgrade the system',
            'runnable'    => true,
        );
    }


    public function exec()
    {
        return array(
            $this->system('sudo apt-get update 2>&1'),
            $this->system('sudo apt-get upgrade -y 2>&1')
        );
    }
}
