<?php


namespace app\api\controller\v1;

use app\BaseController;
use think\Db;
/**
 * 表单目录树
 */
class Formcopy extends Base
{
	// 获取当前公司树目录
	public function get_tree(){
		$formtree = new Formtree;
		$inf = $formtree->get_tree_node();
		// dump($inf);
		// $company_id = $this->get_companyid();
		$this->success(__('成功'),$inf);
	}
	/* 复制表单接口 */
	/* 暂未考虑数据冲突 */
	public function copyform(){
		$u_id = $this->auth->id;
		// $u_id = 4;
		$company_id = $this->get_companyid();
		$apply_id = $_POST['apply_id'];
		//选择的formid
		$formid = $_POST['form_id'];
		// 需要复制到新公司或者结构的id
		$str_pid = $_POST['str_pid'];
		$str_name = $_POST['str_name'];
		$createtime = time();
		//找到对应表单的结构
		$inf = Db::name('s_apply_structure')->field('list,apply_id')->where('id',$formid)->find();
		$list = $inf['list'];
		
		$data['id'] = NULL;
		$data['apply_id'] = $apply_id;
		$data['str_name'] = $str_name;
		$data['u_id'] = $u_id;
		$data['list'] = $list;
		$data['company_id'] = $company_id;
		$data['str_pid'] = $str_pid;
		$data['createtime'] = $createtime;
		
		$sure = Db::name('s_apply_structure')->insert($data);
		if($sure !== false){
			$this->success(__('成功'));
		}else{
			$this->error(__('失败'));
		}
	}
	/* 应用复制接口 */
	public function copylist(){
		
		$company_id = $this->get_companyid();
		$apply_id = $_POST['apply_id'];
		$listinf = Db::name('s_apply_structure')->field('list')->where('str_pid',$apply_id)->select();
		dump($listinf);
	}
}