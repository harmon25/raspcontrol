<?php

/*
 * Json API.
 * 
 * <raspcontrolURL>/api.php?username=<user>&password=<password>&data=<data>
 * 
 * - raspcontrolURL :
 *      The url of raspcontrol, typically "http://<ip>/raspcontrol".
 * 
 * - user :
 *      The user name used to log in raspcontrol.
 * 
 * - password :
 *      The password used to log in raspcontrol.
 * 
 * - data :
 *      The requested datas, the possibles values are : 
 *         all        Return all the informations (details & services).
 *         details    Return rbpi, uptime, memory, cpu, hdd, net, users informations (informations of the details page in raspcontrol).
 *         rbpi       Return the rbpi informations (hostname, distribution, kernel, firmware, internal & external ip).
 *         uptime     Return the uptime (D days H hours M minutes S seconds ).
 *         memory     Return the memory informations (ram, swap).
 *         cpu        Return the cpu informations (usage, heat).
 *         hdd        Return the hdd informations (array of disks).
 *         net        Return the net informations (number of connections, up & down).
 *         users      Return the array of ssh active users.
 *         temp       Return the current temperature (DS18B20 sensor, cpu heat)
 *         services   Return the services with their status.
 */

namespace lib;

use lib\Uptime;
use lib\Memory;
use lib\CPU;
use lib\Storage;
use lib\Network;
use lib\Rbpi;
use lib\Services;
use lib\Users;
use lib\Temp;

spl_autoload_extensions('.php');
spl_autoload_register();

require 'config.php';
require 'lib/password.php';

function build_rbpi($response) {
    $response['rbpi']['hostname'] = Rbpi::hostname(true);
    $response['rbpi']['distribution'] = Rbpi::distribution();
    $response['rbpi']['kernel'] = Rbpi::kernel();
    $response['rbpi']['firmware'] = Rbpi::firmware();
    $response['rbpi']['ip']['internal'] = Rbpi::internalIp();
    $response['rbpi']['ip']['external'] = Rbpi::externalIp();
    return $response;
}

function build_uptime($response) {
    $response['uptime'] = Uptime::uptime();
    return $response;
}

function build_memory($response) {
    $response['memory']['ram'] = Memory::ram();
    $response['memory']['swap'] = Memory::swap();
    return $response;
}

function build_cpu($response) {
    $response['cpu']['usage'] = CPU::cpu();
    $response['cpu']['heat'] = CPU::heat();
    return $response;
}

function build_hdd($response) {
    $response['hdd'] = Storage::hdd();
    return $response;
}

function build_net($response) {
    $response['net']['connections'] = Network::connections();
    $response['net']['ethernet'] = Network::ethernet();
    return $response;
}

function build_users($response) {
    $response['users'] = Users::connected();
    return $response;
}

function build_temp($response) {
    $response['temp'] = Temp::temp();
    $response['cpu']['heat'] = CPU::heat();
    return $response;
}

function build_services($response) {
    $response['services'] = Services::services();
    return $response;
}

$result = array();

try {
    $db = json_decode(file_get_contents(FILE_PASS));
    $username = $db->{'user'};
    $password = $db->{'password'};

    if (!empty($_GET['username']) && !empty($_GET['password']) && $_GET['username'] == $username && password_verify($_GET['password'], $password)) {
        //Login is ok, building full api response
        if (!empty($_GET['data'])) {
            switch ($_GET['data']) {
                case 'all':
                    $result = build_rbpi($result);
                    $result = build_uptime($result);
                    $result = build_memory($result);
                    $result = build_cpu($result);
                    $result = build_hdd($result);
                    $result = build_net($result);
                    $result = build_users($result);
                    $result = build_temp($result);
                    $result = build_services($result);
                    break;
                case 'rbpi':
                    $result = build_rbpi($result);
                    break;
                case 'uptime':
                    $result = build_uptime($result);
                    break;
                case 'memory':
                    $result = build_memory($result);
                    break;
                case 'cpu':
                    $result = build_cpu($result);
                    break;
                case 'hdd':
                    $result = build_hdd($result);
                    break;
                case 'net':
                    $result = build_net($result);
                    break;
                case 'users':
                    $result = build_users($result);
                    break;
                case 'temp':
                    $result = build_temp($result);
                    break;
                case 'services':
                    $result = build_services($result);
                    break;
                case 'details':
                    $result = build_rbpi($result);
                    $result = build_uptime($result);
                    $result = build_memory($result);
                    $result = build_cpu($result);
                    $result = build_hdd($result);
                    $result = build_net($result);
                    $result = build_users($result);
                    $result = build_temp($result);
                    break;
                default:
                    $result['error'] = 'Incorrect data request.';
            }
        } else {
            $result['error'] = 'Empty data request.';
        }
    } else {
        //Login error, api error response
        $result['error'] = 'Incorrect username or password.';
    }
} catch (Exception $e) {
    //FILE_PASS error, api error response
    $result['error'] = $e->getMessage();
}

header('Content-type: application/json');
echo json_encode($result);
?>
