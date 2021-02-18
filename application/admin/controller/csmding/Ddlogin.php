<?php
namespace app\admin\controller\csmding;

use app\common\controller\Backend;
use fast\Random;
use think\Session;
use addons\csmding\library\dingtalk\DingTalkClient;
use addons\csmding\library\dingtalk\DingTalkConstant;
use addons\csmding\library\dingtalk\OapiSnsGetuserinfoBycodeRequest;
use addons\csmding\library\dingtalk\OapiUserGetuserinfoRequest;
use addons\csmding\library\dingtalk\OapiGettokenRequest;
use addons\csmding\library\dingtalk\OapiDepartmentListRequest;
use addons\csmding\library\dingtalk\OapiUserGetDeptMemberRequest;
use addons\csmding\library\dingtalk\OapiUserSimplelistRequest;
use addons\csmding\library\dingtalk\OapiUserListidRequest;
use addons\csmding\library\dingtalk\OapiUserGetRequest;
use addons\csmding\library\Csmding;
use think\Request;
use think\Hook;
use think\Db;

class Ddlogin extends Backend
{

    protected $noNeedLogin = [
        '*'
    ];

    public function _initialize()
    {
        parent::_initialize();
    }

    public function active()
    {}

    // 钉钉移动登录
    public function mobilelogin()
    {
        $url = $this->request->request("url");
        if ($this->request->isAjax()) {
            $code = $this->request->request("code");
            trace("=loginTmpCode1>{$code}");
            $respdduser = $this->getUserInfoByCode($code);
            if ($respdduser != null && $respdduser->errcode == 0) {
                $dao = new \app\admin\model\csmding\Dduser();
                $row = $dao->where('userid', '=', $respdduser->userid)->find();
                if ($row != null) {
                    trace($row);
                    $dao2 = new \app\admin\model\Admin();
                    $row2 = $dao2->where('id', '=', $row->faadmin_id)->find();
                    if ($row2 != null) {
                        $this->directLogin($row2);
                        return $this->success();
                    }
                }
            }
            return $this->error('帐号不存在！');
        }
        $config = get_addon_config("csmding");
        $ddcorpId = $config["ddcorpId"];
        $this->assignconfig('ddcorpId', $ddcorpId);
        $this->assignconfig("url", $url);
        $this->assign("helpimg", "/assets/addons/csmding/img/ding.png");
        $this->assign('title', '钉钉扫码登录');
        return $this->view->fetch();
    }

    /**
     * http://127.0.0.1/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmding/ddlogin/dobydd
     * http://fatest.163fan.com/iyjHrvTU3q.php/csmding/ddlogin/dobydd
     */
    public function dobydd()
    {
        $url = $this->request->get('url');
        // if ($this->auth->isLogin()) {
        //     $this->success(__("You've logged in, do not login again"), $url);
        // }

        // 安装了csmadmin后,钉钉登录需要显示csmadmin的登录tab
        $urlmenu = $this->request->request('urlmenu');
        $urlmenuarr = [];
        if ($urlmenu != null && $urlmenu != '') {
            $urlmenuarr = json_decode($urlmenu, true);
        }
        $this->assign('ulmenu', $urlmenuarr);
        // 显示钉钉登录画面
        $getparam = ($url == null || $url == '') ? '?1=1' : '?url=' . urlencode($url);
        $ulmenu2 = [
            [
                'name' => '钉钉登录',
                'code' => 'dobydd',
                'url' => 'dobydd' . $getparam
            ]
        ];
        $this->assign('ulmenu2', $ulmenu2);

        $this->assign('title', "用户登录");
        // $this->view->engine->layout('csmadmin/layout/default');
        // copy from Csmadminapp#modifydddialog
        $config = get_addon_config('Csmding');
        $ddappid = $config['ddappid'];
        $this->assign("ddappid", $ddappid);
        $this->assign("hosturl", $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] . ( ($_SERVER["SERVER_PORT"]=='80'||$_SERVER["SERVER_PORT"]=='443')?'':':'.$_SERVER["SERVER_PORT"]));
        return $this->view->fetch();
    }

    /**
     * 用户扫码后,钉钉重定向进入完成登录
     * http://127.0.0.1/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmding/ddlogin/dobyddtologin
     */
    public function dobyddtologin()
    {
        $url = $this->request->request('url');
        $url = ($url == null || $url == '') ? url('index/index') : $url;
		$id = 4;
        // $id = $this->auth->id;
        // dump($id);
        if ($this->request->isAjax()) {
            $username = strtolower($this->request->request('username'));
            $password = $this->request->request('password');
            $captcha = $this->request->request("captcha", true);
            $captchaResult = \think\Validate::is($captcha, 'captcha');
            if (! $captchaResult) {
                $this->error('验证码不正确');
            }
            // 更新帐号username和password
            $dao = new \app\admin\model\user();
            $rowtmp = $dao->where('id', '=', $id)->find();
            // dump($rowtmp);
            if ($rowtmp) {
                $this->error('账号名存在');
            }
            $salt = Random::alnum();
            $dao->where('id', '=', $this->auth->id)->update([
                'username' => $username,
                'salt' => $salt,
                'password' => md5(md5($password) . $salt)
            ]);

            // 设置faisfilladmin状态：已经写入username
            $dao = new \app\admin\model\csmding\Dduser();
            $dao->where('faadmin_id', '=', $this->auth->id)->update([
                'faisfilladmin' => 'true'
            ]);

            $this->success();
            return;
        }

        // 用户扫码后,钉钉重定向进入
        $loginTmpCode = $this->request->request("code");
        $respdduser = $this->getUserInfoByLoginTmpCode($loginTmpCode);
		// dump($respdduser);
        if ($respdduser != null) {
            $param = [
                'unionid' => $respdduser->unionid
            ];
			
            if ($param != null) {
                /* $dao = new \app\admin\model\csmding\Dduser();
                $row = $dao->alias('t')
                    ->join('admin a', "t.faadmin_id=a.id and a.status='normal'")
                    ->field('t.id,a.username,t.faisactivie,t.faadmin_id,t.faisfilladmin')
                    ->where('unionid', '=', $param['unionid'])
                    ->find(); */
					
               // $row = Db::name('s_company')->where('uid',$id)->find();
			   $row = Db::name('s_company')
			   ->alias("a") //取一个别名
			   ->join('s_ddkey i', 'a.id = i.company_id')
			   ->where('a.uid',$id)
			   //查询
			   ->find();
			   // dump($row);
			   $appkey = $row['ddkey'];
			   $appsecret = $row['appSecret'];
				// dump($row);
				if($row){
					//是否激活
					$status = $row['status'];
					//组织id
					$company_id = $row['id'];
					if($status == 0){
						//获取key 密钥
						$ddkey = Db::name('s_ddkey')->where('company_id',$company_id)->find();
						//获取token
						
					}
				}
            }
            
            // $this->error('帐号不存在,请重新登录!');
        }
    }
	public function setkey(){
		$lastinf = array();
		$appkey = isset($_POST['appkey']) ? $_POST['appkey'] : 'dingqbglqzomqkbrobu9';
		$appsecret = isset($_POST['appsecret']) ? $_POST['appsecret'] : '9_8e38jL8SB3Pv72GdyX0g1ertA_khWMJgrZlKCnPTcGNhREqqqLHPZofNKkZF1t';
		
		date_default_timezone_set('Asia/Shanghai');
		$access_token = '';
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiGettokenRequest;
		$req->setAppkey($appkey);
		$req->setAppsecret($appsecret);
		$resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/gettoken");
		$resplist = get_object_vars($resp);
		$access_token = $resplist['access_token'];
		// $access_token = $resp;
		//获取部门列表
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$departmentreq = new OapiDepartmentListRequest;
		$departmentresp = $c->execute($departmentreq, $access_token, "https://oapi.dingtalk.com/department/list");
		// dump($departmentresp);
		$departmentresp = get_object_vars($departmentresp);
		$countdepartment = count($departmentresp['department']);
		global $first;
		for($n = 0;$n<$countdepartment;$n++){
			
			$departmentarr = get_object_vars($departmentresp['department'][$n]);
			$departmentid = $departmentarr['id'];
			$userlist = $this->getuserid($access_token,$departmentid);
			// dump($userlist);
			$departid = get_object_vars($userlist);
			$useridlist = get_object_vars($departid['result']);
			$useridlist = $useridlist['userid_list'];
			// dump($useridlist);
			if($n == 0){
				// $first = $useridlist;
				foreach($useridlist as $k => $v){
					$first[] = $v;
				}
			}else{
				foreach($useridlist as $kk => $vv){
					$first[] = $vv;
				}
			}
		}
		$lastarr = array_unique($first);
		foreach($lastarr as $uk => $uv){
			// dump($access_token);
			// dump($uv);
			$userinf[] = $this->getuserinfo($access_token,$uv);
		}
		dump($userinf);
			// $lastinf = $this->getuser($access_token,$departmentid);
	}
	public function getuserinfo($access_token,$userid){
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiUserGetRequest;
		$req->setUserid($userid);
		$resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/user/get");
		$resp = get_object_vars($resp);
		return $resp;
	}
	public function getuserid($access_token,$departmentid){
		
		// $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		// $req = new OapiUserGetDeptMemberRequest;
		// $req->setDeptId($departmentid);
		// $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/user/getDeptMember");
		
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_POST , DingTalkConstant::$FORMAT_JSON);
		// dump($departmentid);
		$req = new OapiUserListidRequest;
		$req->setDeptId($departmentid);
		$resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/topapi/user/listid");
		// dump($resp);
		return $resp;
	}
    public function sendddlogintmpcode()
    {
        $loginTmpCode = $this->csmreq("code", true);
        $row = $this->getUserInfoByLoginTmpCode($loginTmpCode);
        $this->assign("row", $row);
        return $this->view->fetch();
    }

    private static function getUserInfoByCode($code)
    {
        $access_token = Csmding::getAccessToken();

        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET, DingTalkConstant::$FORMAT_JSON);
        $req = new OapiUserGetuserinfoRequest();
        $req->setCode($code);
        $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/user/getuserinfo");

        if ($resp != null && $resp->errcode == 0) {
            return $resp;
        } else {
            return null;
        }
    }

    private static function getUserInfoByLoginTmpCode($loginTmpCode)
    {
        $config = get_addon_config("csmding");
        $ddappid = $config["ddappid"];
        $ddappsecret = $config["ddappsecret"];

        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_POST, DingTalkConstant::$FORMAT_JSON);
        $req = new OapiSnsGetuserinfoBycodeRequest();
        $req->setTmpAuthCode($loginTmpCode);
        $resp = $c->executeWithAccessKey($req, "https://oapi.dingtalk.com/sns/getuserinfo_bycode", $ddappid, $ddappsecret);
        if ($resp != null && $resp->errcode == 0) {
            return $resp->user_info;
        } else {
            return null;
        }
    }

    private function directLogin($admin, $keeptime = 86400)
    {
        // Copy from app\admin\library\Auth#login
        $admin->loginfailure = 0;
        $admin->logintime = time();
        $admin->loginip = request()->ip();
        $admin->token = Random::uuid();
        $admin->save();
        Session::set("admin", $admin->toArray());
        
        $request = Request::instance();
        Hook::listen("admin_login_after", $request);
    }
}
