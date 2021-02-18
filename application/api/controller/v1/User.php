<?php

namespace app\api\controller\v1; 
use app\admin\model\score\Userscore as UserscoreModel;
use app\admin\model\score\Userscorelist as UserscorelistModel;
use think\Validate; 

class User extends Base{
    
    /**
    * 管理中心账户中心个人中心
    */
    
    public function index()
    {
        $data['is_email'] = 0;
        $list = $this->auth->getUserinfo();
        $result['nickname'] = $list['nickname'];
        $result['mobile'] = $list['mobile']; 
       
        
        if ($this->auth->email) {
           $data['is_email'] = 1;
           $result['email'] = $this->auth->email;
        }
        $data['list'] = $result; 
        $company_id = $this->get_companyid();
        $company_list = \app\admin\model\companys\Company::get($company_id);
        // $data['company'] 
        $data['company']['name'] = $company_list['name'];
    //   dump();die;
       $this->success('',$data);
        
    }
    //设置邮箱
    public function setemail()
    {
        $id = $this->auth->id;
        $email = $this->request->post('email','');
        if(!$email)
        {
            $this->error('邮箱不能为空');
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('邮箱格式错误,请重新填写'));
        }
        $user = \app\common\model\User::get($id);
        $user->email     = $email;
        $user->save();
        $this->success('绑定成功');
    }
   public function score()
   {
       dump('积分排行');die;
   }
   //积分列表
   public function scorelist()
   { 
        $day  = (int)$this->request->post('day','');
        if(!empty($day) && $day > 0)
        {
          $daytime = time()-$day*24*60*60;
          $timearray =[$daytime,time()];
          $where['createtime'] = array('between',$timearray);
        }
        $page = (int)$this->request->post('page',1);
        $page_index = 0;
        $limit = 20;
        if ($page <= 0) {
           $page_index = 0;
           $page = 1;
        } 
        if($page > 1)
        {
            $page_index = ($page-1)*$limit;
        }
       //分页  排序 
      $userscorelistModel = new UserscorelistModel;
      $where['uid'] = $this->auth->id;
      $list = $userscorelistModel->field('id,log_value,score,createtime')->where($where)->order('id','desc')->limit($page_index,$limit)->select(); 
      $data['list'] = $list;
      
      
        $count =  $userscorelistModel->field('id')->where($where)->count();
        //当前总页数
        // dump(intval($count/$limit));
        // dump($count%$limit > 0 ? 1: 0);
        $allpage = (intval($count/$limit)) + ($count%$limit > 0 ? 1: 0);
        // dump($allpage);die;
        //当前页面
        $data['page']['nowpage'] = $page;
        //当前总数
        $data['page']['allnum'] = $count;
        //当前总页数
        $data['page']['allpage'] = $allpage;
        $this->success('',$data);
   }
   
   
   
   
}
 
    
   