<?php

namespace app\admin\controller\companys;

use app\common\controller\Backend;
use app\admin\model\Company as CompanyModel;


use fast\Random;
use think\Exception;
use think\Hook;
use think\Db;
use app\common\model\User;
use app\common\model\UserRule;


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
/**
 * 第三方密钥
 *
 * @icon fa fa-circle-o
 */
class Ddkey extends Backend
{
    
    /**
     * Ddkey模型对象
     * @var \app\admin\model\companys\Ddkey
     */
    protected $model = null;
    protected $accessToken = '';
    //部门列表
    protected $departmentresplist = '';
    //用户列表
    protected $userList = [];
    protected $userInfolist = [];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\companys\Ddkey;
        
    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */ 
    /**
     * 查看
     */
    public function index()
    { 
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['scompany'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('scompany')->visible(['name']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    public function add()
    {
        // 查看视图
        // 1获取公司列表
        $company = new CompanyModel;
        $where['deletetime'] = null;
        $where['types'] = array('neq',0);
        $company_list = $company->where($where)->select();
        $list_company = [];
        foreach ($company_list as $key => $value)
        { 
            $list_company[$value['id']] = $value['name']; 
             
        }
        //新增模块
        if($this->request->isAjax())
        {
         $agentId = $this->request->post('agentId');
         $appsecret = $this->request->post('appSecret');
         $appkey= $this->request->post('appKey');
         $company_id = $this->request->post('company_id');
         date_default_timezone_set('Asia/Shanghai'); 
         //获取access_token
         $access_token = $this->getAccessToken($appkey,$appsecret); 
         //获取部门列表
         $departmentresplist = $this->getDepartmentList();
         foreach ($departmentresplist as $key => $value)
         {
          $this->getUserID($value['id']); 
         }
         //先进行验证用户是否重复
         $this->verifyUserlistRepetition();
         //获取用户详情
         $userinfolist = [];
         foreach($this->userList as $k => $v)
         {
             $userinfolist[] = array_merge($this->userInfolist,$this->getUserInfo($v));
              
         }  
         $this->userInfolist = $userinfolist;  
        //获取到用户信息  把用户信息放入到 用户表中 并且创建对应的字段 
        //开启注册会员信息
        $this->register($company_id);  
        $this->success(__('创建成功,可前往用户列表中查看')); 
        

        }
         $this->view->assign("list_company", $list_company);
         return $this->view->fetch();
        
    }
    // 注册会员信息
    public function register($company_id)
    { 
        $time = time();
        $ip = request()->ip();
        
       try {
            Db::startTrans();
           foreach ($this->userInfolist as $key => $value)
           {  
                 $data = [
                'username' => $value['mobile'],
                'password' => $value['mobile'],
                'email'    => '',
                'mobile'   => $value['mobile'],
                'level'    => 1,
                'score'    => 0,
                'avatar'   => '',
                'nickname'  => $value['name'],
                'company_id' => $company_id,
                'salt'      => Random::alnum(),
                'jointime'  => $time,
                'joinip'    => $ip,
                'logintime' => $time,
                'loginip'   => $ip,
                'prevtime'  => $time,
                'status'    => 'normal', 
                'ddunionid'  => $value['unionid'],
                'ddopenId'    => $value['openId'],
                'dduserid'   => $value['userid'], 
                'dddepartment'    => $value['department']
               ];       
              $datas  = [
                'company_id'    => $company_id, 
                'unionid'  =>      $value['unionid'], 
                'userid'   =>      $value['userid'],  
                'mobile'    =>     $value['mobile'],
                'name'    =>       $value['name'], 
                'password'    => '', 
                'createtime' =>$time
                  ];
               $data['password'] = $this->getEncryptPassword($value['mobile'], $data['salt']); 
               $datas['password'] = $this->getEncryptPassword($value['mobile'], $data['salt']); 
               $user = User::create($data, true);  
               Db::name('s_dduserlist')->insert($datas);
               
           } 
         Db::commit();  
       } catch (Exception $e) { 
            Db::rollback();
            $this->error($e->getMessage());  
            return false;
       }  
    }
     /**
     * 获取密码加密后的字符串
     * @param string $password 密码
     * @param string $salt     密码盐
     * @return string
     */
    public function getEncryptPassword($password, $salt = '')
    {
        return md5(md5($password) . $salt);
    }
    //获取access_token 
    public function getAccessToken($appkey,$appsecret)
    {
         $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
         $req = new OapiGettokenRequest;
	 	 $req->setAppkey($appkey);
		 $req->setAppsecret($appsecret);
		 $resp = $c->execute($req, $this->accessToken, "https://oapi.dingtalk.com/gettoken");
	     $resplist = get_object_vars($resp);
		 $access_token = $resplist['access_token'];
		 $this->accessToken = $access_token;
		 return $access_token;
    } 
    //获取公司部门信息 
    public function getDepartmentList()
    { 
        $data = [];
        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
        $departmentreq = new OapiDepartmentListRequest;
		 //部门列表
		 $departmentresp = $c->execute($departmentreq, $this->accessToken, "https://oapi.dingtalk.com/department/list");
		 $departmentresp = get_object_vars($departmentresp);
		  
		 foreach($departmentresp['department'] as $key => $value)
		 {
		     $value = get_object_vars($value); 
		     $data[$key]['id'] = $value['id'];
		     $data[$key]['name'] = $value['name'];
		     if(!isset($value['parentid']))
		     {
		         $data[$key]['parentid'] = 0;
		     }else{
		         $data[$key]['parentid'] = $value['parentid'];
		     }
		     
		 } 
		 $this->departmentresplist = $data;
		 
		 return $data;
    }
    //获取部门员工信息
    public function getUserID($id)
    {
        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_POST , DingTalkConstant::$FORMAT_JSON);
        $req = new OapiUserListidRequest;
        $req->setDeptId($id);
        $resp = $c->execute($req, $this->accessToken, "https://oapi.dingtalk.com/topapi/user/listid");
        $resp = get_object_vars($resp);
        $result = $resp['result'];
        $resultList = get_object_vars($result);
        $data = $resultList['userid_list'];
        //数组拼接  后续去重也是这
        $this->userList = array_merge($this->userList,$data);  
    } 
    //获取用户详情
    public function getUserInfo($userid)
    {
        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
		$req = new OapiUserGetRequest;
		$req->setUserid($userid);
		$resp = $c->execute($req, $this->accessToken, "https://oapi.dingtalk.com/user/get");
		$resp = get_object_vars($resp); 
		$data = [];
		$data['unionid'] = $resp['unionid'];
		$data['openId'] = $resp['openId'];
		$data['userid'] = $resp['userid'];
		$data['isBoss'] = $resp['isBoss'];
		$data['mobile'] = $resp['mobile'];
		$data['isAdmin'] = $resp['isAdmin'];
		$data['name'] = $resp['name'];
		$data['department'] = json_encode($resp['department']); 
		return $data;
	
    }
    //验证用户列表是否重复  并返回不重复的值
    public function verifyUserlistRepetition()
    {
        //第一步 重新排序用户id
        $this->userList = array_values($this->userList);
        //去重 并重新排序
        $this->userList = array_values(array_unique($this->userList));
    }
  
  
  
  
  
}
