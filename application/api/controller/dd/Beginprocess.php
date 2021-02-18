<?php

namespace app\api\controller\dd;

use think\Db;

class Beginprocess extends Base
{
	public function begin_form(){
		$form_id = isset($_POST['id']) ? $_POST['id'] : ''; 
// 		dump($form_id);die;
		$structure = Db::name('s_apply_structure')->where('id',$form_id)->find();
// 		dump($structure);die;
		$company_id = $structure['company_id'];
		$process = Db::name('s_dd_formprocess')->where('belong_id',$form_id)->find();
// 		dump($process);die;
		$list = $structure['list'];
		$list = json_decode($list,true);
		$process_id =  $process['id'];
		//如果是默认流程
		if($process['type'] == 1){
			$inf = Db::name('s_formprocess_list')->where('process_id',$process_id)->where('last','')->find();
			// dump($inf);
			//当前进行到的工序id
			$data['id'] = NULL;
			$data['process_id'] = $process_id;
			$data['now_process'] = $inf['id'];
			$data['type'] =  1;
			$data['company_id'] = $company_id;
			$data['form_id'] = $form_id;
			$data['time'] = time();
			$data['design_id'] = $this->getDesignId();
			$insert = Db::name('m_begin_process')->insert($data);
			if($insert !== false){
				$all['begin'] = Db::name('m_begin_process')->where('company_id',$company_id)->where('type',1)->select();
				$all['over'] = Db::name('m_begin_process')->where('company_id',$company_id)->where('type',2)->select();
			}
		}else{
			
		}
		$this->success(__('成功'));
	}
	public function getDesignId()
	{
	    $design_value = $this->request->post('desgin','');
	    if(!$design_value)
	    {
	        $this->error('请选择设计数据');
	    }
	    $design_value = htmlspecialchars_decode($design_value);
	    $left_design_value = ltrim($design_value,'["');
	    $right_design_value = rtrim($left_design_value,'"]'); 
	    $design_value = explode('","',$right_design_value); 
	    if (!is_array($design_value)) {
	        $this->error('您选择的数据不是数组,请联系管理员查看问题');
	    }
	    if(count($design_value)>10)
	    {
	        $this->error('设计数据不能超过10层');
	    }
	    for($i = 0; $i < count($design_value); $i++)
	    {
	        $data['design'.$i] = $design_value[$i];
	    }
	    $id = Db::name('s_design_form')->insertGetId($data);
	    if(!$id)
	    {
	       $this->error('请联系管理员');
	    }
	    return $id;
	}
	public function get_formlist(){
		$company_id = isset($_POST['company_id']) ? $_POST['company_id'] : '3';
		// $company = Db::name('s_company')->where('s_company',$company_key)->find();
		// $company_id = $company['id'];
		
		$all = Db::name('m_begin_process')->where('company_id',$company_id)->where('upload_time',NULL)->select();
		
		// $all['over'] = Db::name('m_begin_process')->where('company_id',$company_id)->where('type',2)->select();
		$begin = array();
		$over = array();
		foreach($all as $key => $val){
			// dump($val);
			$inf = Db::name('s_formprocess_list')->field('node_key,form_key')->where('id',$val['now_process'])->find();
			$node_key = $inf['node_key'];
			$form_key = $inf['form_key'];
			$inft= Db::name('s_formprocess')->field('name,weight')->where('form_key',$node_key)->find();
			$name = $inft['name'];
			$form_id = $val['form_id'];
			$form_name_inf = Db::name('s_apply_structure')->field('str_name')->where('id',$form_id)->find();
			$form_name = $form_name_inf['str_name'];
			$all[$key]['name'] = $name;
			$all[$key]['form_name'] = $form_name;
			$all[$key]['node_key'] = $node_key;
			$weight = $inft['weight'] + 1;
			$all[$key]['weight'] = $weight;
			$count = Db::name('s_formprocess')->where('in_id',$form_key)->group('form_key')->count();
			// dump($count);
			$all[$key]['all'] = $count;
			
			if($val['type'] == 1){
				$begin[] = $all[$key];
			}else{
				$over[] = $all[$key];
			}
		}
		// dump($begin);
		// dump($over);
		$lastlist['begin'] = $begin;
		$lastlist['over'] = $over;
		// dump($lastlist);
		$this->success(__('成功'),$lastlist);
	}
	public function get_form(){
		$form_id = isset($_POST['form_id']) ? $_POST['form_id'] : '4';
		/* list里的id */
		$liuc_id = isset($_POST['liuc_id']) ? $_POST['liuc_id'] : '11';
		/* 1进行中 2已完成 */
		$type = isset($_POST['type']) ? $_POST['type'] : '1';
		
		$company_id = isset($_POST['company_id']) ? $_POST['company_id'] : '3';
		
		$inf = Db::name('s_apply_structure')->field('list')->where('id',$form_id)->find();
		$list = $inf['list'];
		$list = \json_decode($list);
		if($type == 1){
			$liucinf = Db::name('m_begin_process')->field('process')->where('id',$liuc_id)->find();
			$num = $liucinf['process'];
			$node_keyinf = Db::name('s_formprocess_list')->field('node_key')->where('form_key',$form_id)->where('weight',$num)->find();
			$node_key = $node_keyinf['node_key'];
			$form_name = "s_form_".$node_key;
			// dump($form_name);
			$inf = Db::name($form_name)->where('liuc_id',$liuc_id)->find();
			// dump($inf);
			$lastarr = array();
			foreach($list as $listkey => $listval){
				$list[$listkey] = get_object_vars($list[$listkey]);
				if($list[$listkey]['key'] == $node_key){
					$lastarr = $list[$listkey];
				}
			}
			foreach($lastarr['list'] as $lastkey => $lastval){
				$lastarr['list'][$lastkey] = get_object_vars($lastarr['list'][$lastkey]);
				$field  = $lastarr['list'][$lastkey]['key'];
				$lastinf = Db::name($form_name)->field($field)->where('liuc_id',$liuc_id)->find();
				// dump($lastinf);
				if($lastinf !== false){
					$lastarr['list'][$lastkey]['data'] = $lastinf[$field];
				}else{
					$lastarr['list'][$lastkey]['data'] = '';
				}
			}
			$this->success(__('成功'),$lastarr);
			// dump($lastarr);
		}else{
			
		}
		
	}
	public function save_form(){
		// form_list里来的 
		$form_id = isset($_POST['form_id']) ? $_POST['form_id'] : '';
		$liuc_id = isset($_POST['liuc_id']) ? $_POST['liuc_id'] : '';
		/* 数据列表 */
		$list = isset($_POST['list']) ? $_POST['list'] : '';
		/* 1暂存 2保存 */
		$type = isset($_POST['type']) ? $_POST['type'] : '';
		$company_id = isset($_POST['company_id']) ? $_POST['company_id'] : '3';
		
		$list = json_decode($list);
		$list = get_object_vars($list);
		$newlist = $list;
		$list['liuc_id'] = $liuc_id;
		// 公司
		$list['company_id'] = $company_id;
		//在已开工里面找当前进行到第几工序 和流程表id
		$inf = Db::name('m_begin_process')->field('process,process_id')->where('id',$liuc_id)->find();
		$arr = array_values($newlist);
		// dump($arr);
		//当前的工序
		$weight = $inf['process'];
		//流程表id
		$process_id = $inf['process_id'];
		// 在节点记录表中找到 对应的内容
		$nodekeyinf = Db::name('s_formprocess_list')->field('node_key,next')->where('process_id',$process_id)->where('weight',$weight)->find();
		// dump($nodekeyinf);
		
		$node_key = $nodekeyinf['node_key'];
		$form_name = "s_form_".$node_key;
		// dump($form_name);
		$inf = Db::name($form_name)->where('liuc_id',$liuc_id)->find();
		// dump($inf);
		if(count($inf)>0){
			$del = Db::name($form_name)->where('liuc_id',$liuc_id)->delete();
			$upsure = Db::name($form_name)->insert($list);
			// dump(Db::name($form_name)->getlastsql());
		}else{
			$upsure = Db::name($form_name)->insert($list);
			// dump(Db::name($form_name)->getlastsql());
		}
		if($type == 2){
			if($weight == 0){
				$listinf = Db::name('s_apply_structure')->where('id',$form_id)->value('list');
				$listinf = json_decode($listinf,true);
				if(count($listinf) !== 0){
					$listinf = $listinf[0]['list'];
				}
				//键名 listz
				$listz = array_keys($list);
				// dump($listz);
				//值 arr
				// dump($arr);
				// dump($listinf);	
				foreach($listinf as $listinfkey => $listinfval){
					if($listinfkey < 4){
						$arrnew[] = $listinfval['key'];
					}
				}
				// dump($arrnew);
				foreach($arrnew as $newkey => $newval){
					foreach($listz as $listzkey => $listzval){
						if($listzval == $newval){
							$newarr[$newkey] = $arr[$listzkey];
						}	
					}
				}
				$one = isset($newarr[0]) ? $newarr[0] : '';
				$two = isset($newarr[1]) ? $newarr[1] : '';
				$three = isset($newarr[2]) ? $newarr[2] : '';
				$four = isset($newarr[3]) ? $newarr[3] : '';
			}
			$next_key = $nodekeyinf['next'];
			
			if($next_key == ''){
				$up = Db::name('m_begin_process')->where('id',$liuc_id)->update(['type' => 2]);
			}else{
				// dump($next_key);
				// dump($liuc_id);
				$belong_inf = Db::name('s_dd_formprocess')->where('belong_id',$form_id)->find();
				$belong_id = $belong_inf['id'];
				$now_weightinf = Db::name('s_formprocess_list')->field('weight')->where('process_id',$belong_id)->where('node_key',$next_key)->find();
				// dump(Db::name('s_formprocess_list')->getlastsql());
				$now_weight = $now_weightinf['weight'];
				// dump($now_weight);
				if($now_weight == 1){
					$up = Db::name('m_begin_process')->where('id',$liuc_id)->update(['process' => $now_weight,'one_inf'=>$one,'two_inf'=>$two,'three_inf'=>$three,'four_inf'=>$four]);
				}else{
					$up = Db::name('m_begin_process')->where('id',$liuc_id)->update(['process' => $now_weight]);
				}
				
			}
		}
		$this->success(__("成功"));
	}
	//获取完成表单A
	public function get_over_form(){
		$id = $this->request->post('id');
		$forminf = Db::name('m_begin_process')->field('id,form_id')->where('id',$id)->find();
		$form_id = $forminf['form_id'];
		$formprocess = Db::name('s_formprocess_list')->where('form_key',$form_id)->column('node_key');
		// dump($formprocess);
		foreach($formprocess as $key => $val){
			$name = "s_form_".$val;
			$inf = Db::name($name)->where('liuc_id',$id)->find();
			$data[$key]['key'] = $val;
			$data[$key]['inf'] = $inf;
		}
		// dump($data);
		$form_inf = Db::name('s_apply_structure')->field('list')->where('id',$form_id)->find();
		$form_list = $form_inf['list'];
		// dump($form_list);
		$form_list = json_decode($form_list,true);
		// dump($form_list);
		foreach($form_list as $listkey => $listval){
			foreach($data as $datakey => $dataval){
				$array_keys = array_keys($dataval['inf']);
				// dump($dataval);
				// dump($listval);
				if($dataval['key'] == $listval['key']){
					foreach($array_keys as $arrkey => $arrval){
						//输入的内容key
						// dump($arrval);
						foreach($listval['list'] as $listvalkey => $listvalval){
							if($listvalval['key'] == $arrval){
								// dump("aaa");
								$form_list[$listkey]['list'][$listvalkey]['data'] = $dataval['inf'][$arrval];
							}
						}
					}
				}
			}
		}
		// dump($form_list);
		$this->success(__('成功'),$form_list);
	}
	
}