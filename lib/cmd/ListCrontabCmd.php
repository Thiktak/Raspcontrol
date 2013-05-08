<?php

namespace lib\Cmd;

Class ListCrontabCmd extends Cmd
{
    public static function configure() {
        return array(
            'name'        => 'List cron',
            'description' => '',
            'runnable'    => false,
        );
    }


    public function exec()
    {
        return $this->system('sudo crontab -l 2>&1');
    }
}
