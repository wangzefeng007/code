<?php

namespace app\api\controller\v1;

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
     public function get_companyid()
     {
         $company_key = $this->request->post('company');
         $company_list = CompanyModel::get(['key' => $company_key]);
         $company_id = $company_list['id'];
         return $company_id;
     }
     //根据用户token 获取当前用户对应的角色
     public function get_role()
     {
         $uid = $this->auth->id;
     }
     
     /**
      *获取当前用户所有可看的应用分类
      * 
      */
     public function get_user_apply_cates()
     {
    
          $cateModel = new CateModel();
          $where['uid'] = $this->auth->id; 
          $where['status'] = 0;
          $catelist = $cateModel->where($where)->select();
          $data ="";
          foreach ($catelist as $key =>$val)
          {
             $data .= $val['apply_cid'].',';
          }
          $data = rtrim($data,',');
          // 1,2,3
          return $data;
     }
     
     /**
      *生成日志操作日志 
      * 谁 uid 做了什么事 value
      */
      public function setlog($value = '',$key = '')
      {
          $UserModel = new UserlogModel();
          $UserModel->uid = $this->auth->id;
          $UserModel->createtime = time();
          $UserModel->value = $value;
          $UserModel->key = $key;
          $UserModel->save();
      }
      
      //积分插入
      public function addscore()
      {
          /**
           * 积分插入要分两步骤 1 要先插入到列表中,2再插入到积分表中算总分
           */
           $uid = $this->auth->id;
           
           
           
      }
      /***
       *数据库操作 查看数据库表是否存在 
       * 存在返回false
       * 不存在返回true
       */
       public function get_table_name($tablename)
       {
           //数据库名
           $databasename = 'code_tao5g';
           //  第一步查看数据库是否存在
           $sql1 = "select * from information_schema.SCHEMATA where SCHEMA_NAME = ?";
           $list1 =  Db::query($sql1,[$databasename]);
           if($list1)
           {
               $sql2 = "select * from information_schema.TABLES  where TABLE_NAME  = ?";
               $list2 =  Db::query($sql2,[$tablename]);
              if(empty($list2))
              {
                  return true;
              }
           }
           return false;
       }
       /**
        *创建表 
        * 错误码 
        * 001字段参数不能把为空
        * 002数据库表已存在
        * 
        * $data = array(array1,array2,$array3,....)
        * array1 = array2 = array3 = {key:"字段类型",name:"字段名",comment:'字段注释',default:"默认值"}
        * 
        */
       public function set_table_($tablename,$data = [])
       {
           //判断字段是否为空
           if(empty($data))
           {
               dump('001');
               return "001";
           }
           //查看当前数据库表是否已经存在
           if(!$this->get_table_name($tablename))
           {
               dump('002');
               return "002";
           }
           dump($data);die;
       }

     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
}