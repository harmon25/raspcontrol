<?php

namespace lib;

class Disks {

    public static function Disks() {
        global $ssh;

        $result = array();

        $disksArray = $ssh->exec_noauth('lsblk --pairs');

        for ($i = 0; $i < count($disksArray); $i++) {
            $string = $disksArray[$i];

            $index = 0;
            $pos = 0;
            $values = array();
            while (($pos = strpos($string, '"', $pos)) !== FALSE) {
                $pos2 = strpos($string, '"', $pos + 1);
                $values[$index] = substr($string, $pos + 1, $pos2 - ($pos + 1));
                $pos = $pos2 + 1;
                $index++;
            }

            $result[$i]['name'] = $values[0];
            $result[$i]['maj:min'] = $values[1];
            $result[$i]['rm'] = $values[2];
            $result[$i]['size'] = $values[3];
            $result[$i]['ro'] = $values[4];
            $result[$i]['type'] = $values[5];
            $result[$i]['mountpoint'] = $values[6];
        }

        return $result;
    }

}

?>