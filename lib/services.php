<?php

namespace lib;

class Services {

    public static function services() {
        global $ssh;

        $result = array();

        $servicesArray = $ssh->exec_noauth('/usr/sbin/service --status-all');

        for ($i = 0; $i < count($servicesArray); $i++) {
            $servicesArray[$i] = preg_replace('!\s+!', ' ', $servicesArray[$i]);
            preg_match_all('/\S+/', $servicesArray[$i], $serviceDetails);
            list($bracket1, $status, $bracket2, $name) = $serviceDetails[0];

            $result[$i]['name'] = $name;
            $result[$i]['status'] = $status;
        }

        return $result;
    }

    public static function servicesRunning() {
        $services = self::services();

        $result = array();

        for ($i = 0; $i < count($services); $i++) {
            if ($services[$i]['status'] == '+') {
                array_push($result, $services[$i]);
            }
        }

        return $result;
    }

}

?>