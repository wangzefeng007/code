<?php

namespace app\api\controller\v1;

use app\admin\model\apply\Images as ImagesModel;
use app\admin\model\apply\Category as CategoryModel;
use app\admin\model\apply\Formlist as FormlistModel;
use app\admin\model\apply\Structure as StructureModel;
use app\admin\model\apply\Formstructure as FormstructureModel;
use think\Db;

class Saveform extends Base
{
	public function form_save(){
		
		$Formstructure = new FormstructureModel;
		
		$uid = $this->auth->id;
		$company_id = $this->get_companyid();
		//有就传 没有就传空
		$structureid = $this->request->post('structureid');
		//json内容
		$form_strstructure  = $this->request->post('form_strstructure');
		// dump(gettype($form_strstructure));
		//归属应用id
		$apply_id  = $this->request->post('apply_id');
		//表单名称
		$form_name = $this->request->post('form_name');
		//表单层级
		$form_hierarchy = $this->request->post('form_hierarchy');
		
		$form_strstructure = base64_encode($form_strstructure);
		
		$createtime = time();
		
		if($structureid == ''){
			$data['form_strstructure'] = $form_strstructure;
			$data['company_id'] = $company_id ;
			$data['apply_id'] = $apply_id;
			$data['createtime'] = $createtime;
			$data['u_id'] = $uid;
			$data['form_name'] = $form_name;
			$data['form_hierarchy'] = $form_hierarchy;
			$sure = $Formstructure->insert($data);
			if($sure !== false){
				$this->setlog('创建应用表单'.$form_name);
				$this->success(__('成功'));
			}else{
				$this->error(__('失败'));
			}
		}else{
			// dump($structureid);
			// $Formstructure = Formstructure::where('id',$structureid)->find();
			// $Formstructure->form_strstructure = $form_strstructure;
			// $Formstructure->form_name = $form_name;
			// $Formstructure->form_hierarchy = $form_hierarchy;
			// $Formstructure->updatetime = time();
			// $Formstructure->save();
			// $this->setlog('修改应用表单'.$form_name);
			// $this->success(__('成功'));
			$data['form_strstructure'] = $form_strstructure;
			$data['form_name'] = $form_name;
			$data['form_hierarchy'] = $form_hierarchy;
			$insert = Db::name('s_form_structure')->where('id',$structureid)->update($data);
			if($insert !== false){
				$this->success(__('成功'));
			}else{
				$this->error(__('失败'));
			}
		}
	}
	public function form_get(){
		$structureid = $this->request->post('structureid');
		$inf = Db::name('s_form_structure')->field('form_strstructure')->where('id',$structureid)->find();
		// dump($inf);
		$inf = base64_decode($inf['form_strstructure']);
		$this->success(__('成功'),$inf);
	}
	
}