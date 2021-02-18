<?php
namespace addons\csmding\library\dingtalkisv;

class Isvdemo
{

    public static function sign($ticket, $nonceStr, $timeStamp, $url)
    {
        $plain = 'jsapi_ticket=' . $ticket . '&noncestr=' . $nonceStr . '&timestamp=' . $timeStamp . '&url=' . $url;
        return sha1($plain);
    }

    public static function curPageURL()
    {
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
}
