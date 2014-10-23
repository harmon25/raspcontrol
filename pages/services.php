<?php

namespace lib;

use lib\Services;

$services = Services::services();

function label_service($status) {
    echo '<span class="label label-';
    switch ($status) {
        case '+':
            echo 'success';
            break;
        case '?':
            echo 'warning';
            break;
        default:
            echo 'important';
    }
    echo '">';
    switch ($status) {
        case '+':
            echo 'Running';
            break;
        case '?':
            echo 'Unknown';
            break;
        default:
            echo 'Stopped';
    }
    echo '</span>';
}
?>

<div class="container details">
    <table>
        <tr class="services" id="check-services">
            <td class="check" rowspan="<?php echo sizeof($services); ?>"><i class="icon-cog"></i> Services</td>
            <?php
            for ($i = 0; $i < sizeof($services); $i++) {
                echo '<td class="icon" style="padding-left: 10px;">';
                echo '<a data-rootaction="changeservicestatus" data-service-name="' . $services[$i]["name"] . '" class="rootaction" href="javascript:;">';
                echo label_service($services[$i]['status']), '</a></td>
            <td class="infos">', $services[$i]['name'], '</td>
          </tr>
          ', ($i == sizeof($hdd) - 1) ? null : '<tr class="service">';
            }
            ?>
    </table>
</div>