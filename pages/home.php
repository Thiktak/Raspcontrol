<?php

namespace lib;
use lib\Uptime;
use lib\Memory;
use lib\CPU;
use lib\Storage;
use lib\Network;
use lib\Rbpi;
use lib\Users;

$uptime = Uptime::uptime();
$ram = Memory::ram();
$swap = Memory::swap();
$cpu = CPU::cpu();
$cpu_heat = CPU::heat();
$hdd = Storage::hdd();
$hdd_alert = 'success';
for ($i=0; $i<sizeof($hdd); $i++) {
  if ($hdd[$i]['alert'] == 'warning')
    $hdd_alert = 'warning';
}
$network = Network::connections();
$users = sizeof(Users::connected());

function icon_alert($alert) {
  $icon = '<i class="icon-';
  switch($alert) {
    case 'success':
      $icon .= 'ok';
      break;
    case 'warning':
      $icon .= 'warning-sign';
      break;
    default:
      $icon .= 'exclamation-sign';
  }
  return $icon . ' pull-right"></i>';
}

?>

      <div class="container home">
        <div class="row-fluid infos">
          <div class="span4">
            <i class="icon-home"></i> <?php echo Rbpi::hostname(); ?>
          </div>
          <div class="span4">
            <i class="icon-map-marker"></i> <?php echo Rbpi::ip(); ?>
          </div>
          <div class="span4">
            <i class="icon-play-circle"></i> Server <?php echo Rbpi::webServer(); ?>
          </div>
        </div>

        <div class="infos">
          <div>
            <a href="<?php _url('details#check-uptime'); ?>"><i class="icon-time"></i></a> <?php echo $uptime; ?>
          </div>
        </div>

        <div class="row-fluid">
          <div class="span4 rapid-status">
            <div>
              <i class="icon-asterisk"></i> RAM <?php echo _link(icon_alert($ram['alert']), 'details#check-ram'); ?>
            </div>
            <div>
              <i class="icon-refresh"></i> Swap <?php echo _link(icon_alert($swap['alert']), 'details#check-swap'); ?>
            </div>
            <div>
              <i class="icon-tasks"></i> CPU <?php echo _link(icon_alert($cpu['alert']), 'details#check-cpu'); ?>
            </div>
            <div>
              <i class="icon-fire"></i> CPU (°C) <?php echo _link(icon_alert($cpu_heat['alert']), 'details#check-cpu-heat'); ?>
            </div>
          </div>
          <div class="span4 offset4 rapid-status">
            <div>
              <i class="icon-hdd"></i> Storage <?php echo _link(icon_alert($hdd_alert), 'details#check-storage'); ?>
            </div>
            <div>
              <i class="icon-globe"></i> Network <?php echo _link(icon_alert($network['alert']), 'details#check-network'); ?>
            </div>
            <div>
              <i class="icon-user"></i> Users <?php echo _link('<span class="badge pull-right">' . $users . '</span>', 'details#check-users'); ?>
            </div>
          </div>
        </div>

      </div>
