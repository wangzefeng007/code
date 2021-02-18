<?php

namespace app\admin\controller\companys;

use app\common\controller\Backend;
use fast\Random;
use app\common\model\User;
use app\admin\model\companys\Company as CompanyModel;
use think\Exception;
use think\Db;
/**
 * 公司管理
 *
 * @icon fa fa-circle-o
 */
class Company extends Backend
{
    
    /**
     * Company模型对象
     * @var \app\admin\model\companys\Company
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\companys\Company;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("typesList", $this->model->getTypesList());
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
                    ->with(['user'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('user')->visible(['username','nickname','email','mobile','avatar']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     *修改 
     */
    public function add()
    {
        //添加内容
        if ($this->request->isAjax()){
           $admin_id = $this->auth->id;
        //   公司信息
            $company_name = $this->request->post('row.name');
            $company_types = intval($this->request->post('row.types'));
            $company_probationtime =strtotime($this->request->post('row.probationtime')) ;
            $company_params['name'] = $company_name;
            $company_params['types'] = $company_types;
            $company_params['admin_id'] = $admin_id;
            $company_params['probationtime'] = $company_probationtime;
            $company_params['key'] = substr(md5(time().rand(1000,9999).rand(1000,9999)),8,10);
        //   创建者信息引导到注册 需要用到事务这块
            $user_nickname = $this->request->post('row.nickname');
            $user_mobile = $this->request->post('row.mobile');
            $user_password = $this->request->post('row.password');
            $user_params = $this->register($user_mobile,$user_password,$user_nickname,'',$user_mobile);
            Db::startTrans();
            try {
                // 创建公司
              $company =   CompanyModel::create($company_params,true);
              $company_id =  $company->id;
                //创建用户信息
              $user_params['company_id'] = $company_id;
              $user = User::create($user_params, true);
              $uid  = $user->id;
              //把创始人的id写入到公司表中
              $user = CompanyModel::get($company_id);
              $user->uid = $uid;
              $user->save();
              Db::commit();
              $this->success('注册成功');
            }
            catch (Exception $e) {
              Db::rollback();
              return $e->getMessage();
            }
         
        }
          return $this->view->fetch();
    }
    /**
     *添加公司时候创建一个用户账号
     * 只需要创建账号即可
     */
    public function register($username, $password, $nickname,$email = '', $mobile = '', $extend = [])
    {
        $ip = request()->ip();
        $time = time();
        
        $data = [
            'username' => $username,
            'nickname' => $nickname,
            'password' => $password,
            'email'    => $email,
            'mobile'   => $mobile,
            'level'    => 1,
            'score'    => 0,
            'avatar'   => '',
        ];
        $params = array_merge($data, [
            'nickname'  => $nickname,
            'salt'      => Random::alnum(),
            'jointime'  => $time,
            'joinip'    => $ip,
            'logintime' => $time,
            'loginip'   => $ip,
            'prevtime'  => $time,
            'status'    => 'normal'
        ]);
        
        $params['password'] = $this->getEncryptPassword($password, $params['salt']);
        $params = array_merge($params, $extend);
        
        return $params;
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
    
    

}
