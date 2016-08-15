<?php

/**
 * Carreer Category Controller
 *
 * @author      Trung Hoang - oCoder Solutions
 */

namespace Ocoder\Helper;

class String {

    /**
     * Functions Convert String to Alias
     *
     * @param 	string
     * @return 	string - alias
     */
    function convertStringToAlias($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        //$str = str_replace(array(' ~', ' !', ' @', ' #', ' $', ' %', ' ^', ' &', ' *', ' (', ' )', ' +', ' |', ' \\', ' [', ' ]', ' {', ' }', ' ;', ' :', ' "', ' \'', ' <', ' >', ' ,', ' .', ' ?', ' /', ' -'), '', $str);

        $str = str_replace(array('~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '|', '\\', '[', ']', '{', '}', ';', ':', '"', '\'', '<', '>', ',', '.', '?', '/', '-'), '', $str);
        $str = str_replace(array('  '), ' ', $str);
        $str = str_replace(" ", "-", str_replace("&*#39;", "", $str));

        return strtolower($str);
    }

    /**
     * Get n firsts characters
     *
     * @param 	string
     * @return 	string
     */
    function summary($str, $limit = 50, $break = false, $strip = false) {
        $str = ($strip == true) ? strip_tags($str) : $str;
        if (strlen($str) > $limit) {
            $str = mb_substr($str, 0, $limit, "utf-8");
            if ($break) {
                return (substr($str, 0, strrpos($str, ' ')) . '...');
            }
            return $str . '...';
        }
        return trim($str);
    }

    /**
     * Get current URL
     *
     * @return 	string
     */
    function getCurrentUrl() {
        $pageURL = 'http';
        if (array_key_exists('HTTPS', $_SERVER) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * Check current URL is exist
     *
     * @param   string - url
     * @return 	bool
     */
    function isUrlExist($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

}
