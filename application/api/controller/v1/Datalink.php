<?php


namespace app\api\controller\v1;

use app\admin\model\apply\Strlist as StrlistModel;
use app\admin\model\apply\Structure as StructureModel;
use addons\csmding\library\dingtalk\DingTalkClient;
use addons\csmding\library\dingtalk\DingTalkConstant;
use addons\csmding\library\dingtalk\OapiGettokenRequest;
use addons\csmding\library\dingtalk\OapiDepartmentListRequest;
use addons\csmding\library\dingtalk\OapiUserSimplelistRequest;
use app\BaseController;
use think\Db;


/**
 * 表单目录树
 */
class Datalink extends Base
{
	function getChild($data, $id)
	
	{
	    $child = array();
	
	    foreach ($data as $key => $datum) {
	        if ($datum['parentid'] == $id) {
	
	            // $datum['key'] = $datum['id'];
				
	                $datum['children'] = $this->getChild($data, $datum['id']);
	
	            $child[] = $datum;
	            unset($data[$key]);
	
	        }
	
	    }
	
	    return $child;
	
	}
	public function get_category(){
		$company_id = $this->get_companyid();
		$inf = Db::name('s_apply_category')->field('cate_name,id')->where('company_id',$company_id)->where('deletetime',0)->select();
		$this->success(__('成了~!'),$inf);
	}
	public function get_formlist(){
		$category_id = $_POST['category_id'];
		$inf = Db::name('s_apply_formlist')->field('id,name')->where('category_id',$category_id)->where('deletetime',0)->select();
		$this->success(__('成了~!'),$inf);
	}
	public function get_structure(){
		$formlist_id = $_POST['formlist_id'];
		$inf = Db::name('s_apply_structure')->field('id,str_name')->where('apply_id',$formlist_id)->where('type',1)->where('deletetime',0)->select();
		$this->success(__('成了~!'),$inf);
	}
	public function get_file(){
		$structure_id = $_POST['structure_id'];
		$inf = Db::name('s_apply_structure')->field('id,str_name')->where('str_pid',$structure_id)->where('type',2)->where('deletetime',0)->select();
		// dump(Db::name('s_apply_structure')->getlastsql());
		$this->success(__('成了~!'),$inf);
	}
	public function get_field(){
		$file_id = isset($_POST['file_id']) ? $_POST['file_id'] : '2';
		$inf = Db::name('s_apply_structure')->field('id,str_name,list')->where('id',$file_id)->where('type',2)->where('deletetime',0)->find();
		// dump($inf);
		$list = $inf['list'];
		$list = isset($list) ? $list : '';
		if($list !== ''){
			$list = json_decode($list);
			$lastarray = array();
			foreach($list as $key => $val){
				// dump($val);
				$value = get_object_vars($val);
				// dump($value);
				$vallist = $value['list'];
				// dump($list);
				$array = array();
				$gongxukey = $value['key'];
				foreach($vallist as $listkey => $listval){
					$listinf = get_object_vars($listval);
					$data['gongxukey'] = $gongxukey;
					$data['key'] = $listinf['key'];
					$data['label'] = $listinf['label'];
					array_push($lastarray,$data);
				}
				// \array_push($lastarray,$array);
			}
			$this->success(__('成功'),$lastarray);
		}else{
			$this->error(__('表单无字段'));
		}
		
	}
	/* 
		点击获取联动信息
	 */
	public function get_link_inf(){
		/* 点击的key */
		$showkey = $_POST['showkey'];
		$inf = Db::name('s_datalinklist')->where('showleft',$showkey)->find();
		$this->success(__('成功'),$inf);
	}
	
	public function get_formula(){
		$inf = Db::name('s_formula_save')->select();
		$typeinf = Db::name('s_formula_save')->field('type')->group('type')->select();
		// dump($typeinf);
		// $lastarray = array();
		foreach($typeinf as $key => $value){
			// dump($value);
			$data[$key]['type'] = $value['type'];
			foreach($inf as $k => $v){
			// dump($v);
				if($value['type'] == $v['type']){
					$data[$key]['inf'][] = $v;
				}
			}
		}
		$this->success(__('成功'),$data);
	}
	public function test(){
		
		$str = '<span key="input_1608514933969" contenteditable="false" new-el="true"> 单行文本</span> <span key="input_1608514934814" contenteditable="false" new-el="true"> 单行文本</span> `` ROUND ( `` MAX ( <span key="input_1608514933969" contenteditable="false" new-el="true"> 单行文本</span>';
		$zhengze = "/<span.*>(.*)<\/span>/isU";
		$b = preg_match_all($zhengze,$str,$a);
		dump($a);
		$span = $a[0];
		foreach($span as $key => $val){
			$strer = $val;
			$zhengzeer = '/key="(.*)"/isU';
			preg_match_all($zhengzeer,$strer,$c);
			dump($c);
		}
	}
	public function testtwo(){
		$str = "(aaa+(bbb)+cfcc-(ava))";
		$zhengze = '#\((?R)*\)#';
		$a = \preg_match_all($zhengze,$str,$c);
		dump($c);
	}
	public function get_group_list(){
		$company_id = $this->get_companyid();
		// $company_id = 3;
		$keyinf = Db::name('s_ddkey')->where('company_id',$company_id)->find();
		$key = $keyinf['ddkey'];
		$secret = $keyinf['appSecret'];
		$access_token = '';
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiGettokenRequest;
		$req->setAppkey($key);
		$req->setAppsecret($secret);
		$token = $c->execute($req, $access_token, "https://oapi.dingtalk.com/gettoken");
		$token = get_object_vars($token);
		// dump($token);
		$access_token = $token['access_token'];
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiDepartmentListRequest;
		$resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/department/list");
		// dump($resp);
		$resp = get_object_vars($resp);
		$department = $resp['department'];
		foreach($department as $key => $val){
			$department[$key] = get_object_vars($department[$key]);
			if(!isset($department[$key]['parentid'])){
				$department[$key]['parentid'] = 0;
			}
		}
		// dump($department);
		$inf = $this->getChild($department,0);
		// dump($inf);
		$this->success(__('成功'),$inf);
	}
	public function get_role_list(){
		$department_id = isset($_POST['id']) ? $_POST['id'] : '1';
		$company_id = $this->get_companyid();
		// $company_id = 3;
		$keyinf = Db::name('s_ddkey')->where('company_id',$company_id)->find();
		$key = $keyinf['ddkey'];
		$secret = $keyinf['appSecret'];
		$access_token = '';
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiGettokenRequest;
		$req->setAppkey($key);
		$req->setAppsecret($secret);
		$token = $c->execute($req, $access_token, "https://oapi.dingtalk.com/gettoken");
		$token = get_object_vars($token);
		// dump($token);
		$access_token = $token['access_token'];
		
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiUserSimplelistRequest;
		$req->setDepartmentId($department_id);
		$resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/user/simplelist");
		// dump($resp);
		$inf = get_object_vars($resp);
		$inf = $inf['userlist'];
		foreach($inf as $key => $val){
			$inf[$key] = get_object_vars($inf[$key]);
		}
		$this->success(__('成功'),$inf);
	}
	public function dd_get_group_list(){
		// $company_id = $this->get_companyid();
		$company_id = $_POST['company_id'];
		// $company_id = 3;
		$keyinf = Db::name('s_ddkey')->where('company_id',$company_id)->find();
		$key = $keyinf['ddkey'];
		$secret = $keyinf['appSecret'];
		$access_token = '';
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiGettokenRequest;
		$req->setAppkey($key);
		$req->setAppsecret($secret);
		$token = $c->execute($req, $access_token, "https://oapi.dingtalk.com/gettoken");
		$token = get_object_vars($token);
		// dump($token);
		$access_token = $token['access_token'];
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiDepartmentListRequest;
		$resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/department/list");
		// dump($resp);
		$resp = get_object_vars($resp);
		$department = $resp['department'];
		foreach($department as $key => $val){
			$department[$key] = get_object_vars($department[$key]);
			if(!isset($department[$key]['parentid'])){
				$department[$key]['parentid'] = 0;
			}
		}
		// dump($department);
		$inf = $this->getChild($department,0);
		// dump($inf);
		$this->success(__('成功'),$inf);
	}
	public function dd_get_role_list(){
		$department_id = isset($_POST['id']) ? $_POST['id'] : '1';
		// $company_id = $this->get_companyid();
		$company_id = $_POST['company_id'];
		$keyinf = Db::name('s_ddkey')->where('company_id',$company_id)->find();
		$key = $keyinf['ddkey'];
		$secret = $keyinf['appSecret'];
		$access_token = '';
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiGettokenRequest;
		$req->setAppkey($key);
		$req->setAppsecret($secret);
		$token = $c->execute($req, $access_token, "https://oapi.dingtalk.com/gettoken");
		$token = get_object_vars($token);
		// dump($token);
		$access_token = $token['access_token'];
		
		$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiUserSimplelistRequest;
		$req->setDepartmentId($department_id);
		$resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/user/simplelist");
		// dump($resp);
		$inf = get_object_vars($resp);
		$inf = $inf['userlist'];
		foreach($inf as $key => $val){
			$inf[$key] = get_object_vars($inf[$key]);
		}
		$this->success(__('成功'),$inf);
	}
}