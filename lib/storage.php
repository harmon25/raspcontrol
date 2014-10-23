<?php

namespace lib;

class Storage {

    public static function hdd() {
        global $ssh;

        $result = array();

        $drivesarray = $ssh->exec_noauth('df -T | grep -vE "tmpfs|rootfs|Filesystem"');

        for ($i = 0; $i < count($drivesarray); $i++) {
            $drivesarray[$i] = preg_replace('!\s+!', ' ', $drivesarray[$i]);
            preg_match_all('/\S+/', $drivesarray[$i], $drivedetails);
            list($fs, $type, $size, $used, $available, $percentage, $mounted) = $drivedetails[0];

            $result[$i]['name'] = $mounted;
            $result[$i]['total'] = self::kConv($size);
            $result[$i]['free'] = self::kConv($available);
            $result[$i]['used'] = self::kConv($size - $available);
            $result[$i]['format'] = $type;

            $result[$i]['percentage'] = rtrim($percentage, '%');

            if ($result[$i]['percentage'] > '80')
                $result[$i]['alert'] = 'warning';
            else
                $result[$i]['alert'] = 'success';
        }

        return $result;
    }

    public static function kConv($kSize) {
        $unit = array('K', 'M', 'G', 'T');
        $i = 0;
        $size = $kSize;
        while ($i < 3 && $size > 1024) {
            $i++;
            $size = $size / 1024;
        }
        return round($size, 2) . $unit[$i];
    }

}

?>