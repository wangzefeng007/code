<?php

namespace app\api\controller\v1;

use app\admin\model\apply\Images as ImagesModel;
use app\admin\model\apply\Category as CategoryModel;
use app\admin\model\apply\Formlist as FormlistModel;
use app\admin\model\apply\Structure as StructureModel;
use app\admin\model\apply\Formstructure as FormstructureModel;
use think\Db;

class Newsaveform extends Base
{
	public function form_save(){
		$Formstructure = new FormstructureModel;
		
		$uid = $this->auth->id;
		$company_id = $this->get_companyid();
		//有就传 没有就传空 文件id
		$structureid = $this->request->post('structureid');
		//json内容
		$form_strstructure  = $_POST['form_strstructure'];
		$form_strstructuredecode = json_decode($form_strstructure,true);
		// dump($form_strstructure);
		//归属应用id
		$apply_id  = $this->request->post('apply_id');
		//表单名称
		$form_name = $this->request->post('form_name');
		//表单层级
		$form_hierarchy = $this->request->post('form_hierarchy');
		
		// if($structureid == 0){
		// 	$in['name'] = $form_name;
		// 	$in['apply_id'] = $apply_id;
		// 	$in['form_hierarchy'] = $form_hierarchy;
		// 	$in['list'] = $form_strstructure;
		// 	// dump($in);
		// 	$informname = Db::name('s_formname')->insert($in);
		// 	// dump($informname);
		// 	$in_id = Db::name('s_formname')->getLastInsID();
		// }else{
			$in['str_name'] = $form_name;
			$in['list'] = $form_strstructure;
			// dump($in);
			$sure = Db::name('s_apply_structure')->where('id',$structureid)->update($in);
			// dump(Db::name('s_apply_structure')->getLastsql());
			$in_id = $structureid;
		// }
		$delformlist = Db::name('s_formlist')->where('form_belong',$in_id)->delete();
		foreach($form_strstructuredecode as $key => $val){
			// dump($val);
			Db::name('s_datalinklist')->where('listkey',$val['key'])->delete();
			$str = 'id int(10) primary key NOT NULL AUTO_INCREMENT';
			$name = "fa_s_form_".$val['key'];
			$nname = "s_form_".$val['key'];
			$form_key = $val['key'];
			$indataname = $val['label'];
			
			
			$data['type'] = $val['type'];
			$data['label'] = $val['label'];
			$data['typename'] = $val['typename'];
			$data['formtype'] = $val['formtype'];
			$data['form_hierarchy'] = $form_hierarchy;
			$data['form_key'] = $form_key;
			$data['form_belong'] = $in_id;
			$data['weight'] = $key;
			
			$insertsure = Db::name('s_formlist')->insert($data);
			$array = array();
			$adata = array();
			// dump($val['list']);
			$sql = "DROP TABLE IF EXISTS $name";
			Db::execute($sql);
			$alist = $val['list'];
			foreach($alist as $listkey => $listval){
				// dump($listkey);
				// dump($listval);
				
				$list_key = $listval['key'];
				// $list_key_name = $listval['key']."_name";
				// $list_key_type = $listval['key']."_type";
				// $liuc_id = 'liuc_id';
				$inf = json_encode($listval);
				$str = $str.", $list_key text";
				$adata[$listkey]['id'] = NULL;
				$adata[$listkey]['form_name'] = $nname;
				$adata[$listkey][$list_key] = $inf;
				// $adata[$listkey][$list_key_name] = $listval['label'];
				// $adata[$listkey][$list_key_type] = $listval['type'];
				
				$indata[$listkey]['list_key'] = $list_key;
				$indata[$listkey]['weight'] = $listkey;
				$indata[$listkey]['form_key'] = $form_key;
				$indata[$listkey]['in_id'] = $in_id;
				$indata[$listkey]['name'] = $indataname;
				
			}
			$str = $str." , liuc_id int(10) , company_id int(10),INDEX(company_id)";
			// dump($str);
			$sql = "CREATE TABLE $name ($str) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT";
			// dump($sql);
			$n = Db::execute($sql);
			
			// foreach($adata as $adatakey => $adataval){
			// 	dump($adataval);
			// 	$formname = $adataval['form_name'];
			// 	unset($adata[$adatakey]['form_name']);
			// 	$sureinsert = Db::name($formname)->insert($adata[$adatakey]);
			// }
			$del_process = Db::name('s_formprocess')->where('form_key',$form_key)->delete();
			// dump($indata);
			foreach($indata as $indatakey => $indataval){
				$insertprocess = Db::name('s_formprocess')->insert($indataval);
			}
		}
		/* 联动存储 */
		$datalink = $this->datalink($form_strstructuredecode);
		/* 公式存储 */
		$formular = $this->formular($form_strstructuredecode);
		/* 默认工序 */
		$process = $this->process($form_strstructuredecode,$in_id);
		
		$this->success(__('成功'));
	}
	// /* 获取结构下表单列表 */
	// public function formlist_get(){
	// 	//应用id
	// 	$apply_id = $_POST['apply_id'];
	// 	//层级id
	// 	$form_hierarchy = $_POST['form_hierarchy'];
	// 	$inf = Db::name('s_formname')->where('apply_id',$apply_id)->where('form_hierarchy',$form_hierarchy)->select();
	// 	$this->success(__('成功'),$inf);
	// }
	
	public function form_get(){
		
		$structureid = isset($_POST['structureid']) ? $_POST['structureid'] : '2';
		
		$inf = Db::name('s_apply_structure')->where('id',$structureid)->find();
		
		$last = $inf['list'];
		$last = \json_decode($last);
		// $data['list'] = $last;
		// $data['name'] = $inf['str_name'];
		// dump($last);
		$this->success(__('成功'),$last);
	}
	
	function datalink($form_strstructuredecode){
		// dump($form_strstructuredecode);
		foreach($form_strstructuredecode as $key => $val){
			$listkeyval = $val['key'];
			// dump($val);
			$val = $val['list'];
			// Db::name('s_datalinklist')->where('listkey',$listkeyval)->delete();
			foreach($val as $listkey =>$listval){
				
					$mr_pitchon = $listval['options']['mr_pitchon'];
					/* 联动 */
					if($mr_pitchon == 1){
						$linkageObj = isset($listval['linkageObj']) ? $listval['linkageObj']: '';
						$data['id'] = NULL;
						$data['listkey'] = $listkeyval;
						$data['type'] = $mr_pitchon;
						$data['groupId'] = $linkageObj['groupId'];
						$data['applyId'] = $linkageObj['applyId'];
						$data['folderId'] = $linkageObj['folderId'];
						$data['fileId'] = $linkageObj['fileId'];
						$data['showleft'] = $linkageObj['linkageShow']['name'];
						$data['showright'] = $linkageObj['linkageShow']['value'];
						$data['showkey'] = $linkageObj['linkageShow']['gongxukey'];
						
					foreach($linkageObj['linkageList'] as $objkey => $objval){
						// dump($objval);
						$data['linkleft'] = $objval['name'];
						$data['linkright'] = $objval['value'];
						$data['gongxukey01'] = $objval['gongxukey01'];
						$data['gongxukey02'] = $objval['gongxukey02'];
						// dump($data);
						Db::name('s_datalinklist')->insert($data);
					}
				/* 关联 */
				}else if($mr_pitchon == 3){
					$formRelevancy = $listval['formRelevancy'];
					// dump($listval);
					$data['id'] = NULL;
					$data['listkey'] = $listkeyval;
					$data['type'] = $mr_pitchon;
					$data['groupId'] = $formRelevancy['groupId'];
					$data['applyId'] = $formRelevancy['applyId'];
					$data['folderId'] = $formRelevancy['folderId'];
					$data['fileId'] = $formRelevancy['fileId'];
					
					$data['showleft'] = $formRelevancy['linkageShow']['name'];
					$data['showright'] = $formRelevancy['linkageShow']['value'];
					$data['showkey'] = $formRelevancy['linkageShow']['gongxukey'];
					
					Db::name('s_datalinklist')->insert($data);
				}
			} 
		}
	}
	function formular($form_strstructuredecode){
		foreach($form_strstructuredecode as $key => $val){
			$listkeyval = $val['key'];
			// Db::name('s_datalinklist')->where('listkey',$listkeyval)->delete();
			// dump($val['list']);
			foreach($val['list'] as $listkey => $listval){
				// dump($listval);
				$in = isset($listval['formulaObj']['formula']) ? $listval['formulaObj']['formula'] : 'no';
				if($in !== 'no'){
				
					if($in !== ''){
						$data['id'] = NULL;
						$data['type'] = 2;
						$data['listkey'] = $listkeyval;
						$data['formula_str'] = $listval['formulaObj']['formula'];
						$data['showleft'] = $listval['key'];
						// $data['showkey'] = $listkeyval;
						Db::name('s_datalinklist')->insert($data);
					}
				}
			}
		}
	}
	function process($form_strstructuredecode,$in_id){
		// $form_key = Db::name('s_apply_structure')->where('id',$in_id)->find();
		// dump($form_key);
		// dump($in_id);
		$sure = Db::name('s_dd_formprocess')->where('belong_id',$in_id)->where('type',1)->find();
		if(count($sure)>0){
			$id = $sure['id'];
		}else{
			$datat['id'] = NULL;
			$datat['name'] = '默认流程';
			$datat['type'] = 1;
			$datat['start'] = 1;
			$datat['createtime'] = time();
			$datat['belong_id'] = $in_id;
			$in = Db::name('s_dd_formprocess')->insert($datat);
			$id = Db::name('s_dd_formprocess')->getLastInsID();
			// dump($id);
		}
		$count = count($form_strstructuredecode);
		Db::name('s_formprocess_list')->where('process_id',$id)->delete();
		foreach($form_strstructuredecode as $key => $val){
			$keyadd = $key+1;
			$keylast = $key-1;
			$data['id'] = NULL;
			$data['process_id'] = $id;
			$data['form_key'] = $in_id;
			$data['form_type'] = 0;
			$data['creattime'] = time();
			$data['node_key'] = $val['key'];
			$data['weight'] = $key;
			$data['last'] = isset($form_strstructuredecode[$keylast]['key']) ? $form_strstructuredecode[$keylast]['key'] : '';
			$data['next'] = isset($form_strstructuredecode[$keyadd]['key']) ? $form_strstructuredecode[$keyadd]['key'] : '';
			$sure = Db::name('s_formprocess_list')->insert($data);
		}
	}
}