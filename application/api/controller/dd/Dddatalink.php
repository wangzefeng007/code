<?php

namespace app\api\controller\dd;

use think\Db;

class Dddatalink extends Base
{
	function get_num($str,$liuc_id,$sublist,$caculation_list,$allinf){
		$zz = '%[a-z]*(_)[0-9]*%si';
		// dump($caculation_list);
		// $str = 'input_1591947580621+input_1591947629642';
		
		preg_match_all($zz, $str, $matches);
		
		$matchesstr = $matches[0];//内容
		// dump($matches);
		
		foreach($matchesstr as $key => $val){
			
			foreach($caculation_list as $cakey => $caval){
				foreach($sublist as $subkey => $subval){
					// dump($subval);
					if($subval['key'] == $val){
						// DUMP($subval['value']);
						if($subval['value'] == "" || $subval['value'] == NULL){
							$str = str_replace($val,0,$str);
							// dump($str);
						}else{
							$str = str_replace($val,$subval['value'],$str);
						}
						
					}
				}
				$form_name = "fa_s_form_".$caval;
				$form_name_two = "s_form_".$caval;
				// dump($form_name);
				// dump($allinf);
				
				foreach($allinf as $cacukey => $cacuval){
					
					// dump($form_name);
					// dump($val);
					$sure = Db::query("SELECT column_name FROM information_schema.columns WHERE table_schema='code_tao5g' and table_name = '$form_name' AND column_name = '$val'");
					// dump($sure);
					if(count($sure)>0){
						$inf = Db::name($form_name_two)->field($val)->where('liuc_id',$liuc_id)->find();
						// dump(Db::name($form_name_two)->getlastsql());
						$num = $inf[$val];
						// dump($num);
						if($num !== "" && $num !== NULL){
							$str = str_replace($val,$num,$str);
						}
					}
				}
			}
		}
		// dump($str);
		$str = str_replace('--','+',$str);
		$str = str_replace('+-','-',$str);
		$str = str_replace('- -','+',$str);
		$str = str_replace('+ -','-',$str);
		preg_match_all($zz, $str, $lastmatches);
		
		// dump($lastmatches);
		// dump($str);
		// $str = eval("return $str;");
		
		$Formulaone=new Formulaone;
		$inf = $Formulaone->countjson($str);
		$inf = eval("return $inf;");
		$last[] = $inf;
		// dump($str);
		return $last;
	}
	function array_unset_tt($arr,$key){  
	    $res = array();
	    foreach ($arr as $value) {
	        if(isset($res[$value[$key]])){
	            unset($value[$key]);
	        }
	        else{
	            $res[] = $value[$key];
	        }
	    }
	    return $res;
	}
	function get_list($array,$showright){
		// dump($array);
		// dump($showright);
		$newarray = array();
		foreach($array as $last_inf_key => $last_inf_val){
			// dump($last_inf_val);
			$newarray[] = $last_inf_val[$showright];
		}
		return $newarray; 
	}
	
	/* 获取需要的字段key */
	public function firest_datalink(){
		$key = isset($_POST['key']) ? $_POST['key'] : 'card_1609292693929';
		$inf = Db::name('s_datalinklist')->field('linkleft')->where('listkey',$key)->group('linkleft')->select();
		// dump($inf);
		$this->success(__('成功'),$inf);
	}
	/*获取联动条件 */
	public function get_datalink(){
		// /* 当前流程 */
		// $form_key = isset($_POST['form_key']) ? $_POST['form_key'] : 'card_1609122845612';
		// /* 目标key */
		// $form_name = 's_'.$form_key;
		/* 流程id */
		$liuc_id = isset($_POST['id']) ? $_POST['id'] : '135451';
		/* 第几个流程 */
		$process = isset($_POST['process']) ? $_POST['process'] : '2';
		
		$sublist = isset($_POST['sublist']) ? $_POST['sublist'] : '[{"key":"date_1609415588209","value":0},{"key":"input_1609415617729","value":"FX9-ZKJL-20201128-"},{"key":"number_1609415778471","value":""},{"key":"number_1609415794408","value":""},{"key":"number_1609415818075","value":""},{"key":"cyradio_1609415749440","value":""}]';
		$sublist = json_decode($sublist,true);
		$company_id = isset($_POST['company_id']) ? $_POST['company_id'] :'';
		// dump($sublist);
		$form_id = Db::name('m_begin_process')->field('form_id')->where('id',$liuc_id)->find();
		$form_key = $form_id['form_id'];
		// dump($form_key);
		$allinf = Db::name('s_formprocess_list')->where('form_key',$form_key)->column('node_key');
		// dump($allinf);
		$inf = Db::name('s_formprocess_list')->field('node_key')->where('weight',$process)->where('form_key',$form_key)->find();
		$node_key = $inf['node_key'];
		// dump($node_key);
		// dump($inf);
		$all = Db::name('s_datalinklist')->where('listkey',$node_key)->where("showleft","notlike","select_%")->group('showleft')->select();
		// dump($all);
		foreach($all as $allkey => $allval){
			// dump($allval);
			if($allval['type'] == 1){
				$allarray = array();
				$alllist = Db::name('s_datalinklist')->where('showleft',$allval['showleft'])->select();
				// dump($alllist);
				foreach($alllist as $listkey => $listval){
					// dump($listval);
					foreach($sublist as $sublistkey => $sublistval){
						if($sublistval['key'] == $listval['linkleft'] && $sublistval['value'] !== ''){
							// $linkleft = $listval['linkleft'];
							$alllist[$listkey]['linkleft'] = $sublistval['value'];
						}
					}
				}
				foreach($alllist as $tlistkey => $tlistval){
					// dump($tlistval);
					$form_name_t = 's_form_'.$tlistval['gongxukey02'];
					$form_name_o = 's_form_'.$tlistval['gongxukey01'];
					$form_name_th = 's_form_'.$tlistval['showkey'];
					$inf = Db::name($form_name_t)->where($tlistval['linkright'],$tlistval['linkleft'])->column('liuc_id');
					// dump(Db::name($form_name_t)->getlastsql());
					if(count($inf) == 0){
						$now = Db::name($form_name_o)->where('liuc_id',$liuc_id)->value($tlistval['linkleft']);
						// dump($now);
						$inf = Db::name($form_name_t)->where($tlistval['linkright'],$now)->column('liuc_id');
						// dump($now_inf);
					}
					if($tlistkey == 0){
						$allarray = $inf;
					}else{
						$allarray = array_intersect($allarray,$inf);
					}
				}
				$last_form = "s_form_".$allval['showkey'];
				$showright = $allval['showright'];
				$showleft = $allval['showleft'];
				$showinf = Db::name($last_form)->whereIn('liuc_id',$allarray)->group($showright)->column($showright);
				// dump($showinf);
				$now_data['key'] = $showleft;
				$now_data['value'] = $showinf;
				$last_data[] = $now_data;
			}
		}
		$last_data = isset($last_data) ? $last_data : array();
		$countlastdata = count($last_data);
		$countsublist = count($sublist);
		if($countlastdata > 0 && $countsublist>0){
			for($n = 0;$n<$countsublist;$n++){
				for($w = 0;$w<$countlastdata;$w++){
					$count = count($last_data[$w]['value']);
					if($sublist[$n]['key'] == $last_data[$w]['key'] && $count>0){
						
						$sublist[$n]['value'] = $last_data[$w]['value'][0];
					}
					
				}
			}
		}
		
		// dump($sublist);
		// foreach($sublist as $newsublistkey => $newsublistval){
		// 	foreach($last_data as $lastdatakey => $lastdataval){
		// 		dump($sublist);
		// 		dump($last_data);
		// 		// if($sublist['key'] == $last_data['key']){
		// 		// 	$sublist[$newsublistkey]['value'] = $lastdataval['value'][0];
		// 		// }
		// 	}
		// }
		// dump($sublist);
		foreach($all as $allkey => $allval){
			if($allval['type'] == 2){
				$list = Db::name('s_apply_structure')->field('list')->where('id',$form_key)->find();
				$list = json_decode($list['list'],true);
				foreach($list as $listkey => $listval){
					$caculation_list[] = $listval['key'];
				}
				$showleft = $allval['showleft'];
				// 在 try 块 触发异常
				try
				{
				   $num = $this->get_num($allval['formula_str'],$liuc_id,$sublist,$caculation_list,$allinf);
				   // dump($num);
				}
				// 捕获异常
				catch(Exception $e)
				{
				    $num = '';
				}
				$now_data['key'] = $showleft;
				$now_data['value'] = $num;
				$last_data[] = $now_data;
			}
		}
		// dump($last_data);
		$last_data = isset($last_data) ? $last_data : array();
		$this->success(__('成功'),$last_data);
	}
	/* 点击出内容 */
	public function click_select(){
		/* 点击的下拉框key */
		$key = isset($_POST['key']) ? $_POST['key'] : '';
		/* 流程id */
		$liuc_id = isset($_POST['id']) ? $_POST['id'] : '';
		/* 第几个流程 */
		$process = isset($_POST['process']) ? $_POST['process'] : '';
		
		$sublist = isset($_POST['sublist']) ? $_POST['sublist'] : '';
		
		$sublist = json_decode($sublist,true);
		
		$inf = Db::name('s_datalinklist')->where('showleft',$key)->select();
		if(count($inf)>0){
			$showkey = $inf[0]['showkey'];
			$showleft = $inf[0]['showleft'];
			$showright = $inf[0]['showright'];
			$show_form = "s_form_".$showkey;
			$type = $inf[0]['type'];
			$last_data = array();
			if($type == 1){
				foreach($inf as $infkey => $infval){
					$linkleft = $infval['linkleft'];
					$linkright = $infval['linkright'];
					$form_name = "s_form_".$infval['gongxukey01'];
					$form_name_t = "s_form_".$infval['gongxukey02'];
					
					if($infval['gongxukey01'] == $infval['listkey']){
						// dump("aaa");
						foreach($sublist as $subkey => $subval){
							if($subval['key'] == $infval['linkleft']){
								$linkleftinf = $subval['value'];
							}
						}
					}else{
						$result = Db::name($form_name)->field($infval['linkleft'])->where('liuc_id',$liuc_id)->find();
						$linkleftinf = $result['linkleftinf'];
					}
					$liuc_idlist = Db::name($form_name_t)->where($linkright,$linkleftinf)->column('liuc_id');
					if($infkey == 0){
						$last_data = $liuc_idlist;
					}else{
						$last_data = array_intersect($last_data,$liuc_idlist);
					}
				}
				$lastinf = Db::name($show_form)->whereIn('liuc_id',$last_data)->group($showright)->column($showright);
				// dump($lastinf);
				$now_data['key'] = $key;
				foreach($lastinf as $nkey => $val){
					$ndata['label'] = $val;
					$data[] = $ndata;
				}
				$data = isset($data) ? $data : '';
				$now_data['value'] = $data;
			}else{
				if(count($inf) == 1 ){
					$form_name = "s_form_".$inf[0]['showkey'];
					$showleft = $inf[0]['showleft'];
					$showright = $inf[0]['showright'];
					$lastinf = Db::name($form_name)->group($showright)->column($showright);
					// dump($inf);
					$now_data['key'] = $showleft;
					foreach($lastinf as $nkey => $val){
						$ndata['label'] = $val;
						$data[] = $ndata;
					}
					$data = isset($data) ? $data : '';
					$now_data['value'] = $data;
				}
			}
			
		}
		
		// $form_name = "s_form_".$inf['showkey'];
		// $tkey = $inf['showright'];
		
		// $inf = Db::name($form_name)->field($tkey)->group($tkey)->select();
		// // dump($inf);
		// $now_data['key'] = $key;
		// foreach($inf as $nkey => $val){		// 	$ndata['label'] = $val[$tkey];
		// 	$data[] = $ndata;
		// }
		// $now_data['value'] = $data;
		$this->success(__('成功'),$now_data);
	}
	public function test(){
		$str = '123456';
		$zz = '^[0-9]$';//\((?R)*\)
		$m = array();
		preg_replace($zz,$str,$m);
		dump($m);
	}
	public function testd(){
		$a = array(2,13,42,34,56,23,67,365,87665,54,68,3);
		$inf = $this->quick_sort($a);
		dump($inf);
	}
	function quick_sort($a)
	{
	    // 判断是否需要运行，因下面已拿出一个中间值，这里<=1
	    if (count($a) <= 1) {
	        return $a;
	    }
	
	    $middle = $a[0]; // 中间值
	
	    $left = array(); // 接收小于中间值
	    $right = array();// 接收大于中间值
	
	    // 循环比较
	    for ($i=1; $i < count($a); $i++) { 
	
	        if ($middle < $a[$i]) {
	
	            // 大于中间值
	            $right[] = $a[$i];
	        } else {
	
	            // 小于中间值
	            $left[] = $a[$i];
	        }
	    }
		dump($left);
		dump($right);
	    // 递归排序划分好的2边
	    $left = $this->quick_sort($left);
	    $right = $this->quick_sort($right);
	
	    // 合并排序后的数据，别忘了合并中间值
	    return array_merge($left, array($middle), $right);
	}
	public function testgo(){
		// $Formulaone=controller('Formulaone');
		$Formulaone=new Formulaone;
		$str = 'ROUND((12-2*6/100-1*11/1000-2*11/1000)*1000,2)+SUM(10,20)';
		$inf = $Formulaone->countjson($str);
		
		dump($inf);
	}
	
}