<?php

namespace app\api\controller\dd;
use think\Db;
class Launchprocess extends Base{
    //开始选择流程
    public function index()
    {
        $company_id = $this->request->post('company_id',1);
      	$inf = Db::name('s_apply_category')->field('cate_name,id')->where('company_id',$company_id)->where('deletetime',0)->select();
		$this->success(__('成功'),$inf);
    }
    //发起流程第二步 获取应用分类下的应用
    public function get_category()
    {
		$category_id = $this->request->post('category_id','');
		if (!$category_id) {
		    $this->error('未获取到应用分类id');
		}
		$inf = Db::name('s_apply_formlist')->field('id,name as cate_name')->where('category_id',$category_id)->where('deletetime',0)->select();
		$this->success(__('成功'),$inf);
	}
	//发起流程第三步 根据应用id 获取应用下的结构
	public function get_structure()
	{
	    $data = [];
	    $formlist_id = $this->request->post('formlist_id','');
	    if (!$formlist_id) {
		    $this->error('未获取到应用id');
		}
		$where['apply_id'] =  $formlist_id;
		$where['deletetime'] = 0;
	    $where['str_pid'] = 0;
		$inf = Db::name('s_apply_structure')->field('id,str_name as cate_name,type')->where($where)->select();
	   // $inf = Db::name('s_apply_structure')->where($where)->select();
	    
// 		dump($inf);die;
		foreach ($inf as $key => $value)
		{
		    if($value['type'] == 2)
		    {
		        $value['filetype'] = 'form';
		    }
		    elseif ($value['type'] == 1)
		    {
		        $value['filetype'] = 'dir';
		    }
		    else {
		        $value['filetype'] = 'rests';
		    }
		    $data[$key] = $value;  
		} 
		$this->success(__('成功'), $data);
		 
	}
	// 第四步 找下级目录
	public function get_structurelist()
	{
	    $data = [];
		$structure_id =  $this->request->post('structure_id',''); 
		if (!$structure_id) {
		    $this->error('未获取到应用结构id');
		}
		$where['str_pid'] =  $structure_id;
		$where['deletetime'] = 0;
		$inf = Db::name('s_apply_structure')->field('id,str_name as cate_name,type')->where($where)->select();
// 		dump($inf);die;
		foreach ($inf as $key => $value)
		{
		    if($value['type'] == 2)
		    {
		        $value['filetype'] = 'form';
		    }
		    elseif ($value['type'] == 1)
		    {
		        $value['filetype'] = 'dir';
		    }
		    else {
		        $value['filetype'] = 'rests';
		    }
		    
		    $data[$key] = $value;  
		}
		
		$this->success(__('成功'), $data);
	}
   // 第五步 确认文件
    	public function get_file()
    	{
		$structure_id =  $this->request->post('structure_id',''); 
		if (!$structure_id) {
		    $this->error('未获取到应用结构id');
		}
		$inf = Db::name('s_apply_structure')->field('id,str_name')->where('str_pid',$structure_id)->select();
		$this->success(__('成功'),$inf);
	}
}
