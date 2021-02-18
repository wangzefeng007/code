<?php

namespace app\api\controller\dd;

use app\common\controller\Api;
use app\common\library\Auth;
use think\Config;
use addons\csmding\library\dingtalk\DingTalkClient;
use addons\csmding\library\dingtalk\DingTalkConstant;
use addons\csmding\library\dingtalk\OapiGettokenRequest;
use addons\csmding\library\dingtalk\OapiUserGetuserinfoRequest;

use think\Db;

include_once '../extend/TopSdk.php';

class Ddprocess extends Base{
	public function index(){
		$company_id = isset($_POST['company_id']) ? $_POST['company_id'] : '2';
		$code = isset($_POST['code']) ? $_POST['code'] : '';
		$inf = Db::name('s_ddkey')->where('id',$company_id)->find();
		if($inf){
			$key = $inf['ddkey'];
			$secret = $inf['appSecret'];
			$access_token = '';
			$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
			$req = new OapiGettokenRequest;
			$req->setAppkey($key);
			$req->setAppsecret($secret);
			$token = $c->execute($req, $access_token, "https://oapi.dingtalk.com/gettoken");
			$token = \get_object_vars($token);
			// dump($token);
			$access_token = $token['access_token'];
			$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
			$req = new OapiUserGetuserinfoRequest;
			$req->setCode($code);
			$resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/user/getuserinfo");
			$this->success(__('成功'),$resp);
		}
	}
	
}