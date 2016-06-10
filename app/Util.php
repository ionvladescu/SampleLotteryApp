<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Util {

    public static function generateGuid($namespace = '') {
        static $guid = '';
        $uid  = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        //        $data .= $_SERVER['LOCAL_ADDR'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-' . substr($hash, 12, 4) . '-' . substr($hash, 16, 4) . '-' . substr($hash, 20, 12);

        return $guid;
    }

    public static function makeActivationCode() {
        return strtolower(str_replace('-', '', self::generateGuid()));
    }

    public static function makeSmsActivationCode() {
        return rand(100000, 999999);
    }

    public static function valueOrNull($mixed, $key = null, $nullAsString = false) {
        if(is_array($mixed)) {
            if($key == null) return $nullAsString ? 'null' : null;
            if(!is_string($key) && !is_numeric($key)) return $nullAsString ? 'null' : null;

            return array_key_exists($key, $mixed) ? ($mixed[$key] === null && $nullAsString ? 'null' : $mixed[$key]) : ($nullAsString ? 'null' : null);
        } elseif(is_object($mixed)) {
            if($key == null) return $nullAsString ? 'null' : null;
            if(!is_string($key)) return $nullAsString ? 'null' : null;

            return property_exists($mixed, $key) ? ($mixed->$key === null && $nullAsString ? 'null' : $mixed->$key) : ($nullAsString ? 'null' : null);
        }

        return $nullAsString ? 'null' : null;
    }

    public static function valueOrNullTrim($mixed, $key = null, $nullAsString = false) {
        $val = self::valueOrNull($mixed, $key, $nullAsString);

        return is_null($val) || $val === 'null' ? $val : trim($val);
    }

    public static function valueOrFalse($mixed, $key = null) {
        $val = self::valueOrNull($mixed, $key);

        return $val == null ? false : $val;
    }

    public static function valueOrZero($mixed, $key = null) {
        $val = self::valueOrNull($mixed, $key);

        return $val == null ? 0 : $val;
    }

    public static function arrayToObject($arr) {
        if(is_array($arr)) {
            return (object)array_map(__METHOD__, $arr);
        } else {
            return $arr;
        }
    }

    public static function objectToArray($obj) {
        return json_decode(json_encode($obj), true);
    }

    public static function searchArray($arrays, $key, $search) {
        $count = 0;

        foreach($arrays as $object) {
            if(is_object($object)) {
                $object = get_object_vars($object);
            }

            if(array_key_exists($key, $object) && $object[$key] == $search) $count++;
        }

        return $count;
    }

    public static function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }

}

