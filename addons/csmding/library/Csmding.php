<?php
namespace addons\csmding\library;

use addons\csmding\library\dingtalk\DingTalkClient;
use addons\csmding\library\dingtalk\DingTalkConstant;
use addons\csmding\library\dingtalk\OapiGettokenRequest;
use addons\csmding\library\dingtalk\OapiMediaUploadRequest;
use addons\csmding\library\dingtalk\OapiGetJsapiTicketRequest;
use addons\csmding\library\dingtalkisv\Isvdemo;

class Csmding
{

    public static function assertconfig()
    {
        $config = get_addon_config("csmding");
        $ddcorpId = $config["ddcorpId"];
        $ddappid = $config["ddh5appid"];
        $ddappsecret = $config["ddh5appsecret"];
        if ($ddcorpId == null || $ddcorpId == '' || $ddappid == null || $ddappid == '' || $ddappsecret == null || $ddappsecret == '') {
            return false;
        } else {
            return true;
        }
    }

    public static function getAccessToken()
    {
        date_default_timezone_set('Asia/Shanghai');

        $config = get_addon_config("csmding");
        $ddappid = $config["ddh5appid"];
        $ddappsecret = $config["ddh5appsecret"];

        $access_token = null;
        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET, DingTalkConstant::$FORMAT_JSON);
        $req = new OapiGettokenRequest();
        $req->setAppkey($ddappid);
        $req->setAppsecret($ddappsecret);
        $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/gettoken");
        trace($resp);
        if ($resp != null && $resp->errcode == '0') {

            return $resp->access_token;
        }
        return null;
    }

    public static function getMediaByFile($fileLocation)
    {
        $access_token = static::getAccessToken();

        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_POST, DingTalkConstant::$FORMAT_JSON);
        $req = new OapiMediaUploadRequest();
        $req->setType("image");
        $req->setMedia($fileLocation);
        $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/media/upload");
        trace($resp);
        if ($resp != null) {
            return $resp->media_id;
        } else {
            return null;
        }
    }

    public static function getJsapiTicket()
    {
        $access_token = static::getAccessToken();
        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET, DingTalkConstant::$FORMAT_JSON);
        $req = new OapiGetJsapiTicketRequest();
        $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/get_jsapi_ticket");
        trace($resp);
        return $resp->ticket;
    }

    public static function getJsapiParam($url = null)
    {
        $url = ($url == null || $url == '') ? Isvdemo::curPageURL() : $url;
        $config = get_addon_config("csmding");
        $jsapiTicket = static::getJsapiTicket();

        $agentId = $config["ddh5agentid"];
        $corpId = $config["ddcorpId"];
        $timeStamp = time();
        $nonceStr = $config["ddnonceStr"];

        $signature = Isvdemo::sign($jsapiTicket, $nonceStr, $timeStamp, $url);
        return [
            $agentId,
            $corpId,
            $timeStamp,
            $nonceStr,
            $signature
        ];
    }

}
