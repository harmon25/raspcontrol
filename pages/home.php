<?php

namespace lib;

use lib\Uptime;
use lib\Memory;
use lib\CPU;
use lib\Storage;
use lib\Network;
use lib\Rbpi;
use lib\Users;
use lib\Temp;

$uptime = Uptime::uptime();
$ram = Memory::ram();
$swap = Memory::swap();
$cpu = CPU::cpu();
$cpu_heat = CPU::heat();
$hdd = Storage::hdd();
$hdd_alert = 'success';
for ($i = 0; $i < sizeof($hdd); $i++) {
    if ($hdd[$i]['alert'] == 'warning')
        $hdd_alert = 'warning';
}
$network = Network::connections();
$users = sizeof(Users::connected());
$temp = Temp::temp();

$external_ip = Rbpi::externalIp();

function icon_alert($alert) {
    echo '<i class="icon-';
    switch ($alert) {
        case 'success':
            echo 'ok';
            break;
        case 'warning':
            echo 'warning-sign';
            break;
        default:
            echo 'exclamation-sign';
    }
    echo ' pull-right"></i>';
}
?>

<div class="container home">
    <div class="row-fluid infos">
        <div class="span4">
            <i class="icon-home"></i> <?php echo Rbpi::hostname(); ?>
        </div>
        <div class="span4">
            <i class="icon-map-marker"></i> <?php echo Rbpi::internalIp(); ?>
            <?php echo ($external_ip != 'Unavailable') ? '<br /><i class="icon-globe"></i> ' . $external_ip : ''; ?>
        </div>
        <div class="span4">
            <i class="icon-play-circle"></i> Server <?php echo Rbpi::webServer(); ?>
        </div>
    </div>

    <div class="infos">
        <div>
            <a href="<?php echo DETAILS; ?>#check-uptime"><i class="icon-time"></i></a> <?php echo $uptime; ?>		        
        </div>		
    </div>
    <div class="row-fluid">
        <div class="span4 rapid-status">
            <div>
                <i class="icon-asterisk"></i> RAM <a href="<?php echo DETAILS; ?>#check-ram"><?php echo icon_alert($ram['alert']); ?></a>
            </div>
            <div>
                <i class="icon-refresh"></i> Swap <a href="<?php echo DETAILS; ?>#check-swap"><?php echo icon_alert($swap['alert']); ?></a>
            </div>
            <div>
                <i class="icon-tasks"></i> CPU <a href="<?php echo DETAILS; ?>#check-cpu"><?php echo icon_alert($cpu['alert']); ?></a>
            </div>
            <div>
                <i class="icon-fire"></i> CPU <a href="<?php echo DETAILS; ?>#check-cpu-heat"><?php echo icon_alert($cpu_heat['alert']); ?></a>
            </div>
        </div>
        <div class="span4 offset4 rapid-status">
            <div>
                <i class="icon-hdd"></i> Storage <a href="<?php echo DETAILS; ?>#check-storage"><?php echo icon_alert($hdd_alert); ?></a>
            </div>
            <div>
                <i class="icon-globe"></i> Network <a href="<?php echo DETAILS; ?>#check-network"><?php echo icon_alert($network['alert']); ?></a>
            </div>
            <div>
                <i class="icon-user"></i> Users <a href="<?php echo DETAILS; ?>#check-users"><span class="badge pull-right"><?php echo $users; ?></span></a>
            </div>
            <div>
                <i class="icon-fire"></i> Temperature <a href="<?php echo DETAILS; ?>#check-temp"><?php echo icon_alert($temp['alert']); ?></a>
            </div>
        </div>
    </div>
</div>

</div>
