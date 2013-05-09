<?php

namespace lib;

spl_autoload_extensions('.php');
spl_autoload_register();

require 'config.php';

$data = array();

$data['ram'] = Memory::ram();
$data['swap'] = Memory::swap();
$data['cpu'] = CPU::cpu();
$data['cpu_heat'] = CPU::heat();
$data['hdd'] = Storage::hdd();
$data['net_connections'] = Network::connections();
$data['net_eth'] = Network::ethernet();
$data['users'] = Users::connected();

if( !defined('FILE_STATS') ) {
    define('FILE_STATS', dirname(__FILE__) . '/raspberry.stats');
    echo 'const FILE_STATS is not defined into condif.php', PHP_EOL;
    echo ' Add define(\'FILE_STATS\', dirname(__FILE__) . \'/raspberry.stats\');', PHP_EOL, PHP_EOL;
}


$datas = (array) json_decode(file_get_contents(FILE_STATS));
$datas[time()] = $data;
file_put_contents(FILE_STATS, json_encode($datas));

echo date('Y-m-d H:i:s'), ' into ', FILE_STATS, PHP_EOL, PHP_EOL;

foreach( $data as $type => $data )
{
  echo '> ', $type, PHP_EOL;
  foreach( $data as $key => $value ) {
    echo '  ', $key, ' : ', print_r($value, 1), PHP_EOL;
  }
  echo PHP_EOL;
}

//print_r($data);

?>