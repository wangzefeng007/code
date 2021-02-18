<?php
namespace app\admin\controller\csmding;

use app\common\controller\Backend;
use addons\csmding\library\dingtalk\DingTalkConstant;
use addons\csmding\library\dingtalk\DingTalkClient;
use addons\csmding\library\dingtalk\OapiUserGetAdminRequest;
use addons\csmding\library\dingtalk\OapiDepartmentListRequest;

/**
 * Ding的测试类
 */
class Dingtest extends Backend
{

    protected $noNeedLogin = [
        '*'
    ];

    // http://127.0.0.1/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmding/dingtest/jstest
    // http://fatest.163fan.com/iyjHrvTU3q.php/csmding/dingtest/jstest
    public function jstest()
    {
        list ($agentId, $corpId, $timeStamp, $nonceStr, $signature) = \addons\csmding\library\Csmding::getJsapiParam();

        $this->assignconfig('agentId', $agentId);
        $this->assignconfig('corpId', $corpId);
        $this->assignconfig('timeStamp', $timeStamp);
        $this->assignconfig('nonceStr', $nonceStr);
        $this->assignconfig('signature', $signature);

        return $this->fetch();
    }

    public function webservicetest()
    {
        return $this->fetch();
    }

    public function testaccesstoken()
    {
        $access_token = \addons\csmding\library\Csmding::getAccessToken();
        $this->success(null, null, [
            'accesstoken' => $access_token
        ]);
    }

    public function testgetadmin()
    {
        $access_token = \addons\csmding\library\Csmding::getAccessToken();
        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET, DingTalkConstant::$FORMAT_JSON);
        $req = new OapiUserGetAdminRequest();
        $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/user/get_admin");
        $this->success(null, null, [
            'resp' => json_encode($resp)
        ]);
    }

    public function testdepartment()
    {
        $access_token = \addons\csmding\library\Csmding::getAccessToken();
        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET, DingTalkConstant::$FORMAT_JSON);
        $req = new OapiDepartmentListRequest;
        $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/department/list");
        $this->success(null,null,['resp'=>json_encode($resp)]);
    }
    
}
