<?php

namespace lib;

class Rbpi {

    public static function distribution() {
        global $ssh;
        $distroTypeRaw = $ssh->shell_exec_noauth("cat /etc/*-release | grep PRETTY_NAME=");
        $distroTypeRawEnd = str_ireplace('PRETTY_NAME="', '', $distroTypeRaw);
        $distroTypeRawEnd = str_ireplace('"', '', $distroTypeRawEnd);

        return $distroTypeRawEnd;
    }

    public static function kernel() {
        global $ssh;
        return $ssh->shell_exec_noauth("uname -mrs");
    }

    public static function firmware() {
        global $ssh;
        return $ssh->shell_exec_noauth("uname -v");
    }

    public static function hostname($full = false) {
        global $ssh;
        return $full ? $ssh->shell_exec_noauth("hostname -f") : gethostname();
    }

    public static function internalIp() {
        global $ssh;
        return $_SERVER['SERVER_ADDR'];
    }

    public static function externalIp() {
        $ip = self::loadUrl('http://whatismyip.akamai.com');
        if (filter_var($ip, FILTER_VALIDATE_IP) === false)
            $ip = self::loadUrl('http://ipecho.net/plain');
        if (filter_var($ip, FILTER_VALIDATE_IP) === false)
            return 'Unavailable';
        return $ip;
    }

    public static function webServer() {
        return$_SERVER['SERVER_SOFTWARE'];
    }

    protected static function loadUrl($url) {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $content = curl_exec($curl);
            curl_close($curl);
            return trim($content);
        } elseif (function_exists('file_get_contents')) {
            return trim(file_get_contents($url));
        } else {
            return false;
        }
    }

}

?>
