<?php

namespace app\api\controller\v1;

 
use think\Db;
use app\admin\model\templatelink\Form as FormModel; 
use app\admin\model\designdata\Index as DesignDataModel;
use app\admin\model\templatelink\Design as DesignModel;
/**
 *模板关联表单 
 */
class Templatelink extends Base
{ 
    protected $tid = 0;
    //设计数据列表
    public function designDataList()
    { 
        $company_id =  $this->get_companyid();
        $tid = $this->request->post('tid','');
        if(!$tid)
        {
            $this->error('请先选择模板');
        }
        $this->tid = $tid;
        $designDataModel = new DesignDataModel; 
        $list = $designDataModel->field('id,name')->where('status',1)->where('company',$company_id)->where('deletetime',null)->select(); 
        $this->success('',$list);
    }
    //点击确定时候触发
    public function sureDesign()
    {
        $value = $this->request->post('value',''); 
        $tid =  $this->request->post('tid','');
        $uid = $this->auth->id;
        $company_id =  $this->get_companyid();
        $designdata_id =  $this->request->post('design_id',''); 
        $designModel =  new DesignModel;
        $designModel->template_id = $tid;
        $designModel->company = $company_id;
        $designModel->user_id = $uid;
        $designModel->design_id = $designdata_id;
        $designModel->tree_value = htmlspecialchars_decode($value);
        $designModel->save(); 
        $this->success('保存成功');
    }
    //模板查看已经绑定哪些设计数据
    public function findTemolateDesign()
    {
        $tid =  $this->request->post('tid',''); 
        $company_id =  $this->get_companyid(); 
        $where['company'] = $company_id;
        $where['template_id'] = $tid;
        $list = Db::name('s_template_designdata')->where($where)->find();
        $value = $list['tree_value'];  
        $designDataModel = new DesignDataModel; 
        $lists = $designDataModel->field('id,name')->where('id',$list['design_id'])->find();   
        $data['value'] = $value;
        $data['design_name'] = $lists['name'];
        $data['design_id'] = $lists['id'];  
        $this->success('',$data);
    }
    
    public function getDesignForm()
    {
        // dump($page);dump($limit);die;
        $list = Db::name('s_design_form')->where('deletetime',0)->select();
        $data = [];
        foreach($list as $key => $value)
        {
            $da = [];
            $i = 1;
            foreach($value as $k => $v)
            {
                 $da['index_'.$i] = $v;
                 $i++;
            } 
            $da['index_11'] = '开发中';
            $da['index_12'] = '开发中';
            if(!$da['index_13'])
            {
                $da['index_13'] = '否';
            }
            if(!$da['index_14'])
            {
                $da['index_14'] = '否';
            }
            if(!$da['index_15'])
            {
                $da['index_15'] = '否';
            }
            if(!$da['index_16'])
            {
                $da['index_16'] = '无';
            }
            unset($da['index_17']);
            unset($da['index_18']);
            unset($da['index_19']);
            unset($da['index_20']);
            $data[] = $da;
        }
        $this->success('',$data);
    }
}