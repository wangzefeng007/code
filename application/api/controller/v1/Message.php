<?php

namespace app\api\controller\v1;

use app\admin\model\log\Userlog as UserlogModel;
use app\admin\model\cmessage\Amessage as AmessageModel;
use app\admin\model\cmessage\Messageread as MessagereadModel;
use think\Exception;
use think\Db;
class Message extends Base{
    
    public function index()
    {
        dump(1);die;
    }
    //测试log使用
    public function getlog()
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
        $UserlogModel = new UserlogModel;
        $where['uid'] =  $this->auth->id;
        $list =  $UserlogModel->field('value,createtime')->where($where)->limit($page_index,$limit)->order('id','desc')->select();
        $count =  $UserlogModel->field('value,createtime')->where($where)->count();
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
        $data['list'] = $list;
        $this->success('',$data);
    }
    
    // 创建公司消息
    public function set_company_message()
    {
        $cuid = $this->auth->id;
        $value = $this->request->post('value');
        
        $suid =  $this->request->post('suid');
        $uidarray = explode(',',$suid);
        $amessageModel = new AmessageModel;
        $amessage_params['cuid'] = $cuid;
        $amessage_params['value'] = $value;
        $amessage_params['createtime'] = time();
        $amessage_params['type'] = 1;
        $amessage_params['compan_id'] = $this->get_companyid();
        $amessage_params['suids'] = $suid;
        // dump($uidarray);die;
         Db::startTrans();
            try {
                // 消息记录
              $amessage_ =   AmessageModel::create($amessage_params,true);
              $amessage_id =  $amessage_->id;
                //将获取到的用户信息插入到关联表中
             $messagereadModel = new MessagereadModel;
              foreach ($uidarray as $v)
              {
                  $messageread_params['suid'] = $v;
                  $messageread_params['cmid'] = $amessage_id;
                  $messageread_params['is_read'] = 0;
                  $messageread_params['readtime'] = time();
                  MessagereadModel::create($messageread_params,true);
              }
              Db::commit();
              $this->success('已通知');
            }
            catch (Exception $e) {
              Db::rollback();
              return $e->getMessage();
            }
    }
    // 查看我发的通知
    public function get_cmessage()
    {
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
        
        $where['cuid'] = $this->auth->id;;
        $amessageModel = new AmessageModel;
        $list = $amessageModel->field('id,createtime,value')->where($where)->order('id','desc')->limit($page_index,$limit)->select();
        $count =  $amessageModel->where($where)->count();
        $data['page']['nowpage'] = $page;
        //当前总数
        $data['page']['allnum'] = $count;
        //当前总页数
        $allpage = (intval($count/$limit)) + ($count%$limit > 0 ? 1: 0);
        $data['page']['allpage'] = $allpage;
        $data['list'] = $list;
        $this->success('',$data);
    }
    
    /**
     * 通知我的
     * 把未读的放前面
     * 把已读的放后面
     * 并且要按时间倒序来排
     */ 
    public function get_me()
    {
        $suid = $this->auth->id;
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
        
        //连接的表 + 别名  + 关联字段
        $join = [
                 ['fa_s_company_message w','a.cmid=w.id'],
                ];
                // dump($join);die;
        $where['suid'] = $suid;
        $list = Db::table('fa_s_company_message_reads')->alias('a')->join($join)->field('a.id,is_read,createtime,value')->where($where)->order('id','desc')->select();
        // 将已读和未读的区分开
        // 未读
        $noread = [];
        // 已读
        $read = [];
        foreach($list as $k =>$v)
        {
          if($v['is_read'] == 0)
          {
              $noread[$k] = $v;
          }else{
              $read[$k] = $v;
          }
          
        }
         $data['readlist'] = $read;
         $data['norealist'] = $noread;
        $this->success('',$data);
      
        
    }
    /**
     * 消息详情  
     * 1查看详情 
     * 2把未读的设置成已读 如果是已读就不改变
     */ 
     public function read_details()
     {
         //连接的表 + 别名  + 关联字段
        $id = $this->request->post('id');
        $suid = $this->auth->id;
        $join = [
                 ['fa_s_company_message w','a.cmid=w.id'],
                ];
                // dump($join);die;
        $where['suid'] = $suid;
        $where['a.id'] = $id;
        $list = Db::table('fa_s_company_message_reads')->alias('a')->join($join)->field('a.id,is_read,createtime,value')->where($where)->order('id','desc')->find();
        if($list['is_read'] == 0)
        {
            $user = MessagereadModel::get($id);
            $user->is_read     = 1;
            $user->save();
        }
        $data['list'] = $list;
        $this->success('',$data);
     }
    //首页通知
    public function index_message()
    {
        $uid = $this->auth->id;
        $join = [
                 ['fa_s_company_message w','a.cmid=w.id'],
                ];
                // dump($join);die;
        $where['suid'] = $uid;
        $where['is_read'] = 0; 
        $list = Db::table('fa_s_company_message_reads')->alias('a')->join($join)->field('a.id,is_read,createtime,value')->where($where)->order('id','desc')->find();
         $this->success('',$list);
    }
}