<?php


namespace app\api\controller\v1;

use app\BaseController;
use think\Db;

class Sortcopy extends Base
{
	/*
	* 作用:生成随机密钥
	* 参数:
	* 备注:
	*/
	function getRandomString($len, $chars=null)
	{
	    if (is_null($chars)){
	        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    }  
	    mt_srand(10000000*(double)microtime());
	    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
	        $str .= $chars[mt_rand(0, $lc)];  
	    }
	    return $str;
	}
	/*
	* 作用:使用密钥生成 表单
	* 参数:
	* 备注:
	*/
	function generatelist($form_list_id){
		// $form_list_id = '1,4,6';
		$data = array();
			$n = explode(',',$form_list_id);
			foreach($n as $key => $value){
				// dump($key);
				// dump($value);
				$category = Db::name('s_apply_category')->field('id,cate_name')->where('id',$value)->where('deletetime',0)->find();
				// dump($category['cate_name']);
				if(count($category)>0){
					$data[] = $category;
					$formlist = Db::name('s_apply_formlist')->field('id,imgas_id,name,imgbg')->where('category_id',$category['id'])->select();
					if(count($formlist)>0){
						$data[$key]['formlist'] = $formlist;
						foreach($formlist as $listkey => $listvalue){
							$structure = Db::name('s_apply_structure')->field('id,apply_id,str_name,type,list,form_type')->where('apply_id',$listvalue['id'])->where('str_pid',0)->select();
							$data[$key]['formlist'][$listkey]['children'] = $structure;
							foreach($structure as $structurekey => $structurevalue){
								$last = Db::name('s_apply_structure')->field('id,str_name,list,icon,type')->where('str_pid',$structurevalue['id'])->select();
								$data[$key]['formlist'][$listkey]['children'][$structurekey]['children'] = $last;
							}
						}
					}
				}else{
					$data[] = '';
				}
			}
			// dump($data);
			$desc_data1 = json_encode($data,JSON_UNESCAPED_UNICODE);
			$desc_data2 = gzcompress($desc_data1);
			$desc_data3 = base64_encode($desc_data2);
			return $desc_data3;
	}
	/*
	* 作用:获取分类列表
	* 参数:
	* 备注:
	*/
	public function get_list(){
		$company_id = $this->get_companyid();
		// $company_id = 3;
		$inf = Db::name('s_apply_category')->field('id,cate_name')->where('company_id',$company_id)->select();
		// $inft = Db::name('')->where('')->
		// dump($inf);
		$this->success(__('成功'),$inf);
	}
	/*
	* 作用:获取授权公司列表
	* 参数:
	* 备注:
	*/
	public function get_company(){
		$inf = Db::name('s_company')->field('id,name')->select();
		
		$this->success(__('成功'),$inf);
	}
	/*
	* 作用:生成key
	* 参数:
	* 备注:
	*/
	public function generatekey(){
		// $company_id = $this->get_companyid();
		$user_id = $this->auth->id;
		/* 选中的分类id */
		$form_list_id = $_POST['id'];
		$form_list_id = implode(",",$form_list_id);
		/* 目标公司id */
		$c_id = $_POST['c_id'];
		
		$sureadmin = Db::name('user')->field('company_id')->where('id',$user_id)->where('company_id',0)->find();
		// dump($sureadmin);
		if(count($sureadmin)>0){
			$secret_key = $this->getRandomString(8);
			$structure = $this->generatelist($form_list_id);
			$data['id'] = NULL;
			$data['time'] = time();
			$data['form_list_id'] = $form_list_id;
			$data['use_state'] = 1;
			$data['secret_key'] = $secret_key;
			$data['c_id'] = $c_id;
			$data['structure'] = $structure;
			$insert = Db::name('m_sortcopy')->insert($data);
			$msg = $secret_key;
			if($insert !== false){
				$this->success(__($msg));
			}else{
				$this->error(__('请稍后重试'));
			}
			
		}else{
			$this->error(__('普通用户无法生成密钥'));
		}
	}
	/*
	* 作用:使用key
	* 参数:
	* 备注:
	*/
	public function usekey(){
		Db::startTrans();
		$u_id = $this->auth->id;
		$company_id = $this->get_companyid();
		$secret_key = $_POST['secret_key'];
		if($secret_key !== ''){
			$inf = Db::name('m_sortcopy')->field('time,structure')->where('c_id',$company_id)->where('secret_key',$secret_key)->where('use_state',1)->find();
			// dump(Db::name('m_sortcopy')->getlastsql());
			// dump($inf);
			if(count($inf)>0){
				$list = $inf['structure'];
				$desc_data11 = base64_decode($list);
				$desc_data12 = gzuncompress($desc_data11);
				$lastlist = json_decode($desc_data12,true);
				// dump($lastlist);
			try{
				foreach($lastlist as $listkey => $listvalue){
					$cate_name = isset($listvalue['cate_name']) ? $listvalue['cate_name'] : '';
					$cate_id = isset($listvalue['id']) ? $listvalue['id'] : '';
					$form_list = isset($listvalue['formlist']) ? $listvalue['formlist'] : '';
					if($cate_id !== '' && $cate_name !== ''){
						$data_one['id'] = NULL;
						$data_one['cate_name'] = $cate_name;
						$data_one['company_id'] = $company_id;
						$data_one['u_id'] = $u_id;
						// dump($data_one);
						$re1 = Db::name('s_apply_category')->insertGetId($data_one);
						// $re1 = 13;
						// dump($form_list);
						foreach($form_list as $listkey => $listvalue){
							// dump($listvalue);
							$data_two['id'] = NULL;
							$data_two['imgas_id'] = $listvalue['imgas_id'];
							$data_two['name'] = $listvalue['name'];
							$data_two['imgbg'] = $listvalue['imgbg'];
							$data_two['company_id'] = $company_id;
							$data_two['u_id'] = $u_id;
							$data_two['category_id'] = $re1;
							// dump($data_two);
							$children = isset($listvalue['children']) ? $listvalue['children']: '';
							$re2 = Db::name('s_apply_formlist')->insertGetId($data_two);
							// dump($re2);
							// $re2 = 1;
							if($children !== ''){
								// dump($children);
								foreach($children as $childrenkey => $childrenvalue){
									// dump($childrenvalue);
									$data_three['id'] = NULL;
									$data_three['str_name'] = $childrenvalue['str_name'];
									$data_three['type'] = $childrenvalue['type'];
									$data_three['form_type'] = $childrenvalue['form_type'];
									$data_three['apply_id'] = $re2;
									$data_three['u_id'] = $u_id;
									$data_three['list'] = $childrenvalue['list'];
									$data_three['str_pid'] = 0; 
									$re3 = Db::name('s_apply_structure')->insertGetId($data_three);
									// $re3 = 2;
									$children_t = isset($childrenvalue['children']) ? $childrenvalue['children'] : '';
									
									if($children_t !== ''){
										// dump($children_t);
										$data_four_list = array();
										foreach($children_t as $tkey => $tvalue){
											$data_four['id'] = NULL;
											$data_four['str_name'] = $tvalue['str_name'];
											$data_four['list'] = $tvalue['list'];
											$data_four['icon'] = $tvalue['icon'];
											$data_four['type'] = $tvalue['type'];
											$data_four['apply_id'] = $re2;
											$data_four['u_id'] = $u_id;
											$data_four['str_pid'] = $re3;
											$data_four_list[] = $data_four;
										}
										// dump($data_four_list);
										if(count($data_four_list)>0){
											$re4 = Db::name('s_apply_structure')->insertAll($data_four_list);
										}
										
									}
								}
							}
						}
					}
				}
			$this->success(__('成功'));
			}catch (\think\Exception\DbException $exception){
				Db::rollback();
				$this->error(__('请稍后重试'));
			}
			 }else{
				$this->error(__('密钥已被使用'));
			}
		}else{
			$this->error(__('密钥不可为空'));
		}
		
	}
	/*
	* 作用:生成列表
	* 参数:
	* 备注:
	*/
	public function keylist(){
		$inf = Db::name('m_sortcopy')->field('secret_key,use_state')->order('time')->select();
	}
	function quick_sort ($array) {
	    if (count($array) <= 1) {
	        return $array;
	    }
	    $left_array = [];
	    $right_array = [];
	    $key = array_shift($array);
	    foreach ($array as $value) {
	        if ($key > $value) {
	            $left_array[] = $value;
	        }else {
	            $right_array[] = $value;
	        }
	    }
	    return array_merge($this->quick_sort($left_array), [$key], $this->quick_sort($right_array));
	}
	function bubble_sort ($array) {
		/* 键名重组 */
	    $array = array_values($array);
		/* 循环第一层 */
	    for ($i = 0; $i < count($array); $i++) {
			/* 循环第一层相邻的第二个 */
	        for ($j = 0;$j < count($array) - $i - 1; $j++) {
				/* 如果前一个比后一个大 */
	            if ($array[$j] > $array[$j + 1]) {
					/* 就把大的和小的调换位置 */
	                $temp = $array[$j + 1];
	                $array[$j + 1] = $array[$j];
	                $array[$j] = $temp;
	            }
	        }
	    }
	 
	    return $array;
	}
}