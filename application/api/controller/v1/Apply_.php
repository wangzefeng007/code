<?php

namespace app\api\controller\v1;

use app\admin\model\Company as CompanyModel;
use app\admin\model\apply\Department as DepartmentModel;
use app\admin\model\apply\Ddorganization as Ddorganization;
use app\admin\model\apply\Dduserlist as Dduserlist;
use app\admin\model\apply\Rolelist as Rolelist;
use app\admin\model\apply\Rolegroup as Rolegroup;


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
use addons\csmding\library\dingtalk\OapiRoleListRequest;

use addons\csmding\library\Csmding;
use think\Request;
use think\Hook;
use think\Db;

/* 
	获取get
	设置set
	删除del
	获取当前用户id $this->auth->id
	
 */
class Apply_ extends Base
{
	public function setkey(){
		/* 使用表 */
		$company = new CompanyModel;
		$department = new DepartmentModel;
		$ddorganization = new Ddorganization;
		$savedduserlist = new Dduserlist;
		$rolelist = new Rolelist;
		$rolegroup = new Rolegroup;
		/* post */
		$company_key = isset($_POST['company']) ? $_POST['company'] : '095a3f3711';
		$appkey = isset($_POST['appkey']) ? $_POST['appkey'] : 'dingqbglqzomqkbrobu9';
		$appsecret = isset($_POST['appsecret']) ? $_POST['appsecret'] : '9_8e38jL8SB3Pv72GdyX0g1ertA_khWMJgrZlKCnPTcGNhREqqqLHPZofNKkZF1t';
		/* 方法 */
		$surechange = $company->where('key',$company_key)->find();
		
		if($surechange['status'] == 0 ){
			$company_id = $surechange['id'];
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
			$departmentresp = get_object_vars($departmentresp);
// 			dump($departmentresp);
			$departmentrespinf = $departmentresp['department'];
			/* 循环拼接数据 */
			$lastinf = $this->lastinf($departmentrespinf,$company_id);
			/* 插入department表 */
			
			$suredepartment = $department->insertAll($lastinf);
			
			$countdepartment = count($departmentresp);
			/* 获取部门中所有用户并去重 */
			global $first;
			global $list;
			for($n = 0;$n<$countdepartment;$n++){
				$departmentarr = get_object_vars($departmentresp['department'][$n]);
				//组织id
				$departmentid = $departmentarr['id'];
				$userlist = $this->getuserid($access_token,$departmentid);
				
				$departid = get_object_vars($userlist);
				//归属组织的用户
				$useridlist = get_object_vars($departid['result']);
				$useridlist = $useridlist['userid_list'];
				
				// dump($useridlist);
				foreach($useridlist as $uslistkey => $uslistval){
					$inf = $this->getuserinfo($access_token,$uslistval);
					// dump($inf);
					$a['id'] = NULL;
					$a['dd_userid'] = $inf['userid'];
					$a['dd_unionid'] = $inf['unionid'];
					$a['dd_orgid'] = $departmentid;
					$a['company_id'] = $company_id;
					$a['name'] = $inf['name'];
					$list[] = $a;
				}
				/* 去重统计公司所有用户 */
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
			/* 插入dd组织表 */
			$ddorganizationinsert = $ddorganization->insertAll($list);
			
			
			foreach($lastarr as $uk => $uv){
				$dduserlist = $this->getuserinfo($access_token,$uv);
				$lastddlist['id'] = NULL;
				$lastddlist['company_id'] = $company_id;
				$lastddlist['unionid'] = $dduserlist['unionid'];
				$lastddlist['userid'] = $dduserlist['userid'];
				$lastddlist['mobile'] = $dduserlist['mobile'];
				$lastddlist['name'] = $dduserlist['name'];
				$pass = md5($dduserlist['mobile']);
				$lastddlist['password'] = $pass;
				$lastddlist['createtime'] = time();
				$userinf[] = $lastddlist;
			}
			/* 插入ddusrlist表 */
			$sureuserlist = $savedduserlist->insertAll($userinf);
			
			$roleinf = $this->getroleinf($access_token);
			$roleinf = get_object_vars($roleinf);
			$roleresult = get_object_vars($roleinf['result']);
			foreach($roleresult['list'] as $rk => $rv){
				$inf = get_object_vars($rv);
				$groupinf['groupId'] = $inf['groupId'];
				$groupinf['name'] = $inf['name'];
				$groupinf['company_id'] = $company_id;
				$groupinf['createtime'] = time();
				$lastbrole[] = $groupinf;
				foreach($inf['roles'] as $rsk => $rsv){
					$rsv = get_object_vars($rsv);
					$srole['ddid'] = $rsv['id'];
					$srole['name'] = $rsv['name'];
					$srole['company_id'] = $company_id;
					$srole['groupId'] = $inf['groupId'];
					$srole['createtime'] = time();
					$lastsrole[] = $srole;
				}
			}
			// dump($lastsrole);
			// dump($lastbrole);
			$surerolelist = $rolelist->insertAll($lastsrole);
			
			$suresrolelist = $rolegroup->insertAll($lastbrole);
		}else{
			
		}
	}
	/* 角色组中角色拼接 */
	// public function lastrole(){
		
	// }
	/* 钉钉组织结构 数组拼接 */
	public function lastinf($departmentrespinf,$company_id){
		// dump($company_id);
		foreach($departmentrespinf as $key => $value){
			$newval = get_object_vars($value);
			$last['id'] = NULL;
			$last['name'] = $newval['name'];
			$last['ddid'] = $newval['id'];
			$last['parentid'] = isset($newval['parentid']) ? $newval['parentid'] : '0';
			$last['autoAddUser'] = $newval['autoAddUser'];
			$last['createDeptGroup'] = $newval['createDeptGroup'];
			$last['company_id'] = $company_id;
			$last['createtime'] = time();
			// dump($last);
			$departmentrespinf[$key] = $last;
		}
		return $departmentrespinf;
	}
	public function getroleinf($access_token){
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_POST , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiRoleListRequest;
		$resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/topapi/role/list");
		return $resp;
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
		
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_POST , DingTalkConstant::$FORMAT_JSON);
		// dump($departmentid);
		$req = new OapiUserListidRequest;
		$req->setDeptId($departmentid);
		$resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/topapi/user/listid");
		// dump($resp);
		return $resp;
	}
	
}