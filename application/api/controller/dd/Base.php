<?php

namespace app\api\controller\dd;
use app\common\controller\Api;
use think\Config;
use app\admin\model\companys\Company as CompanyModel;
use app\admin\model\uapply\Cate as CateModel; 
use app\admin\model\message\Message as MessageModel;
use app\admin\model\log\Userlog as UserlogModel;
use app\admin\model\score\Userscore as UserscoreModel;
use app\admin\model\score\Userscorelist as UserscorelistModel;
use think\Db;
class Base extends Api{   
    //  当前文件夹下的所有接口都需要登录
    /*先设置不登录测试*/
     protected $noNeedLogin = ['*'];
     
     protected $noNeedRight = ['*'];
     
      public function _initialize()
     {
         parent::_initialize();
         Config::set('default_return_type', 'json');
     }
     //  根据key获取到公司的id

   
     // 获取用户id
    public function get_uid()
    {
       $mobile =  $this->request->post('mobile','');
       $company_id =  $this->request->post('company_id','');
       $where['mobile'] = $mobile; 
       $where['company_id'] = $company_id; 
       $list = \app\common\model\User::where($where)->find();
       return $list['id'];
    }
    /**
     *钉钉端数据固定缓存
     * ①公司key
     * 根据钉钉uid获取到用户id
     * ②用户手机号码
     */
     public function get_user_info()
     {
        //1获取钉钉uid 获取公司id
         $dduid = $this->request->post('dduid',''); 
         if (!$dduid) {
             $this->error('参数异常,没有获取到钉钉用户id');
         }
        $where['userid'] = $dduid;
        $dduserlist =  Db::name('s_dduserlist')->where($where)->find(); 
        $data['company_id'] = $dduserlist['company_id'];
        $data['mobile'] = $dduserlist['mobile'];
        //查看user表中是否存在dduid  如果存在就读取当前用户信息  如果不存在就找到对应的用户id 并且写入 
        return $data;
     }
    
}
     