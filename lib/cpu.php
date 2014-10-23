<?php

namespace lib;

class CPU {

    /**
     * The number of line which will be shown in the popover
     */
    public static $DETAIL_LINE_COUNT = 5;

    public static function cpu() {

        $result = array();

        $getLoad = sys_getloadavg();
        $cpuCurFreq = round(file_get_contents("/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq") / 1000) . "MHz";
        $cpuMinFreq = round(file_get_contents("/sys/devices/system/cpu/cpu0/cpufreq/scaling_min_freq") / 1000) . "MHz";
        $cpuMaxFreq = round(file_get_contents("/sys/devices/system/cpu/cpu0/cpufreq/scaling_max_freq") / 1000) . "MHz";
        $cpuFreqGovernor = file_get_contents("/sys/devices/system/cpu/cpu0/cpufreq/scaling_governor");

        if ($getLoad[0] > 1)
            $result['alert'] = 'danger';
        else
            $result['alert'] = 'success';

        $result['loads'] = $getLoad[0];
        $result['loads5'] = $getLoad[1];
        $result['loads15'] = $getLoad[2];
        $result['current'] = $cpuCurFreq;
        $result['min'] = $cpuMinFreq;
        $result['max'] = $cpuMaxFreq;
        $result['governor'] = substr($cpuFreqGovernor, 0, -1);

        return $result;
    }

    private static $MaxTemp = 85;

    public static function heat() {
        global $ssh;
        $result = array();

        $fh = fopen("/sys/class/thermal/thermal_zone0/temp", 'r');
        $currenttemp = fgets($fh);
        fclose($fh);

        $cpuDetails = $ssh->shell_exec_noauth('ps -e -o pcpu,user,args --sort=-pcpu | sed "/^ 0.0 /d" | head -' . self::$DETAIL_LINE_COUNT);

        $result['degrees'] = round($currenttemp / 1000);
        $result['percentage'] = round($result['degrees'] / self::$MaxTemp * 100);

        if ($result['percentage'] >= '80')
            $result['alert'] = 'danger';
        elseif ($result['percentage'] >= '60')
            $result['alert'] = 'warning';
        else
            $result['alert'] = 'success';

        $result['detail'] = $cpuDetails;

        return $result;
    }

}

?>
