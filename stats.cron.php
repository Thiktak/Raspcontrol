<?php

namespace lib;

spl_autoload_extensions('.php');
spl_autoload_register();

require 'config.php';

echo '<pre>';

$data = array();

/*
$data['uptime'] = Uptime::uptime();

$t = strtotime($data['uptime'] . ' ago');
var_dump(array(

    $data['uptime'],
    time(),
    strtotime($data['uptime']),
    time() - $t,
    (time()-$t)/3600

));
*/

$data['ram'] = Memory::ram();
$data['swap'] = Memory::swap();
$data['cpu'] = CPU::cpu();
$data['cpu_heat'] = CPU::heat();
$data['hdd'] = Storage::hdd();
$data['net_connections'] = Network::connections();
$data['net_eth'] = Network::ethernet();
$data['users'] = Users::connected();


$datas = (array) json_decode(file_get_contents(FILE_STATS));
$datas[time()] = $data;
file_put_contents(FILE_STATS, json_encode($datas));


foreach( $datas as $time => $data )
{
    if( isset($data->cpu_heat->degrees) )
        $graphs['heat'][0][$time] = (int) $data->cpu_heat->degrees;
}


print_r($data);

echo '<hr />';

/*print_r($datas);*/

?>