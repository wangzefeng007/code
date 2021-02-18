<?php


namespace app\api\controller\dd;


use think\Controller;
use think\Db;
use think\Model;
use Session;

/**
 * 数据联动/公式计算 接口
 */
class Formulaone extends Base
{
	
	function Formula($json_str)
	{
		$Formulatwo= new Formulatwo;
		$MATCH_STR = "/([0-9,]*)((AND|OR|IF|IFS|IFSISEMPTY|CONCATENATE|UNION|LEFT|LEN|RIGHT|MID|ROUND|COUNT|MIN|MAX|INT|MOD|SUM|RAND|ABS|FIXED|POWER|DATEDIF|TIME|TODAY|YEAR|MONTH|DAY|TIMESTAMP|DATEDELTA|DAYS|NOW|MAPX)\(([^()]|(?R))*\))/";
		$MATCH_LIST = [];
		
		$list_count=0;
		if(!empty($json_str))
		{
			$last_str=$json_str;
			// echo "#####".$last_str."----------";
			// dump($Formulatwo->ChkExist_Formula($last_str));
			//判断如果包含了其中一个函数信息进行处理
			if($Formulatwo->ChkExist_Formula($last_str))
			{
				preg_match_all($MATCH_STR, $json_str, $MATCH_LIST);
				// dump($MATCH_LIST);
				// !empty($MATCH_LIST
				if(count($MATCH_LIST)>0)
				{
					$thenew_str="";
					// echo json_encode($MATCH_LIST)."----------";
					$new_i=0;
					if(count($MATCH_LIST)>1)
					{
						$new_i=1;
					}
					for ($i = $new_i; $i < count($MATCH_LIST); $i++) {
						$list_count=count($MATCH_LIST[$i]);
						// dump($list_count);
						for ($num = 0; $num < $list_count; $num++) {
							$old = $MATCH_LIST[$i][$num];
							// dump($Formulatwo->ChkExist_Formula($old));
// 							//判断如果包含了其中一个函数信息进行处理
							if($Formulatwo->ChkExist_Formula($old))
							{
								// echo "@@@@@@".$old."@@@@@@";
								if(strtolower(substr($old, 0, 4))=='sum(')
								{
									// dump(4);
									$str = mb_substr($old, 4);
									
									$str = mb_substr($str, 0, -1);
									// dump($str);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'sum'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
											// dump($str);
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_Sum($str);
										// dump($item_str);
										if(isset($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,4))=='and(')
								{
									$str = mb_substr($old,4);
									$str = mb_substr($str, 0, -1);
									
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'and'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_AND($str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,3))=='or(')
								{
									$str = mb_substr($old,3);
									$str = mb_substr($str, 0, -1);
									
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'or'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->FormulaOR($str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,4))=='max(')
								{
									$str = mb_substr($old,4);
									$str = mb_substr($str, 0, -1);
									
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'max'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_Max($str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,6))=='power(')
								{
									$str = mb_substr($old,6);
									$str = mb_substr($str, 0, -1);
									
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'power'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_POWER($str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,6))=='fixed(')
								{
									$str = mb_substr($old,6);
									$str = mb_substr($str, 0, -1);
									
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'fixed'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_FIXED($str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,4))=='abs(')
								{
									$str = mb_substr($old,4);
									$str = mb_substr($str, 0, -1);
									
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'abs'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_ABS($str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,5))=='rand(')
								{
									$str = mb_substr($old,5);
									$str = mb_substr($str, 0, -1);
									
									if(empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'rand'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_RAND($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,4))=='int(')
								{
									$str = mb_substr($old,4);
									$str = mb_substr($str, 0, -1);
									
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'int'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_INT($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,4))=='mod(')
								{
									$str = mb_substr($old,4);
									$str = mb_substr($str, 0, -1);
									
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'mod'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_MOD($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,4))=='min(')
								{
									$str = mb_substr($old,4);
									$str = mb_substr($str, 0, -1);
									
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'min'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_MIN($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,3))=='if(')
								{
									$str = mb_substr($old,3);
									$str = mb_substr($str, 0, -1);
//                                    echo $str;
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'if'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_IF($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,4))=='ifs(')
								{
									$str = mb_substr($old,4);
									$str = mb_substr($str, 0, -1);
//                                    echo $str;
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'ifs'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_IFS($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,12))=='concatenate(')
								{
									$str = mb_substr($old,12);
									$str = mb_substr($str, 0, -1);
//                                    echo $str;
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'concatenate'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_CONCATENATE($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,6))=='union(')
								{
									$str = mb_substr($old,6);
									$str = mb_substr($str, 0, -1);
//                                    echo $str;
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'union'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										$item_str=$Formulatwo->Formula_UNION($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											$last_str = str_replace($old, $item_str, $last_str);
										}
									}
								}
								elseif(strtolower(substr($old, 0,8))=='datedif(')
								{
									// dump("aaa");
									$str = mb_substr($old,8);
									$str = mb_substr($str, 0, -1);
								    // echo $str;
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'datedif'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_DATEDIF($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,6))=='round(')
								{
									// dump("aaa");
									$str = mb_substr($old,6);
									$str = mb_substr($str, 0, -1);
								    // echo $str;
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'round'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_ROUND($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,5))=='left(')
								{
									// dump("aaa");
									$str = mb_substr($old,5);
									$str = mb_substr($str, 0, -1);
								    // echo $str;
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'left'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_LEFT($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,4))=='len(')
								{
									$str = mb_substr($old,4);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'len'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_LEN($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,6))=='right(')
								{
									$str = mb_substr($old,6);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'right'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_RIGHT($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,4))=='mid(')
								{
									$str = mb_substr($old,4);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'mid'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_MID($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,6))=='count(')
								{
									$str = mb_substr($old,6);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'count'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_COUNT($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,5))=='time(')
								{
									$str = mb_substr($old,5);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'time'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_TIME($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,6))=='today(')
								{
									$str = mb_substr($old,6);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'today'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_TODAY($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,5))=='year(')
								{
									$str = mb_substr($old,5);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'year'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_YEAR($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,6))=='month(')
								{
									$str = mb_substr($old,6);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'month'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_MONTH($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,4))=='day(')
								{
									$str = mb_substr($old,4);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'day'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_DAY($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,6))=='month(')
								{
									$str = mb_substr($old,6);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'month'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_MONTH($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,10))=='timestamp(')
								{
									$str = mb_substr($old,10);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'timestamp'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_TIMESTAMP($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,10))=='datedelta(')
								{
									$str = mb_substr($old,10);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'datedelta'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_DATEDELTA($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,5))=='days(')
								{
									$str = mb_substr($old,5);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'days'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_DAYS($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,4))=='now(')
								{
									$str = mb_substr($old,4);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'now'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_NOW($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								elseif(strtolower(substr($old, 0,5))=='mapx(')
								{
									$str = mb_substr($old,5);
									$str = mb_substr($str, 0, -1);
									if(!empty($str))
									{
										if($Formulatwo->ChkExist_Formula($str,'mapx'))
										{
											//判断如果包含了其它函数信息进行处理
											$item_str=$this->Formula($str);
											//替换为最新的结果数据字符串
											$str=$item_str;
										}
										// dump($str);
										$item_str=$Formulatwo->Formula_NOW($str);
										// dump($item_str);
										if(!empty($item_str))
										{
											if($item_str == 'n'){
												$last_str = str_replace($old, 0, $last_str);
											}else{
												$last_str = str_replace($old, $item_str, $last_str);
											}
											
										}
										
									}
								}
								else{
									// echo "*****".$old."------".$list_count."$$$$$$$";
									$item_str=$this->Formula($old);
									if(!empty($item_str))
									{
										$last_str = str_replace($old, $item_str, $last_str);
									}
								}
								$thenew_str=$last_str;
// 								//echo "======".$thenew_str."=======";
							}
						}
					}
				}
			}
			else{
				$thenew_str = $last_str;
			}
		}
		return $thenew_str;
	}
	
	function countjson($json_str){
		// dump($json_str);
		// $json_str = 'ROUND(1/2,1)+1';
		// $json_str = 'DATEDIF("h",14:47,11:48)';
		// $json_str = 'ROUND((12-2*6/100-1*11/1000-2*11/1000)*1000,2)+SUM(10,20)';
		// $json_str = 'ZTMIN(1,2,3,2,1)';
//		$json_str = 'MAX(1,2,3,MAX(4,5,6))+POWER(3,2)+ABS(-7)+RAND()+MIN(1,2,3)';
//		 $json_str = 'CONCATENATE(hjlkasd1,dasd,3)';
		 // $json_str = 'SUM(0,SUM(0,0,SUM(0,0,SUM(0,0))))';
		 // $json_str = 'SUM(0,1,2)';
		 // $json_str = 'SUM(0,SUM(0,0,SUM(0,0,SUM(0,0))))+SUM(0,1,2,3)';
		 // $json_str = 'UNION(cc,bbb,aaa)';
		 // $json_str = '100--50';
		 $json_str = \str_replace('--','+',$json_str);
		 $json_str = \str_replace('+-','-',$json_str);
		 $json_str = \str_replace(' ','',$json_str);
		 // dump($json_str);
		 if($json_str == ''){
			 return '';
		 }else{
			 $A =  $this->Formula($json_str);
		 }
		
		// dump($A);
		// try{

  //           $result=eval("return $A;");
  //           // dump($result);
		// 	return round($result,2);
  //       }
  //       catch (\Exception $e) {
  //           // dump($A);
		// 	return '';
  //       }
		return $A;
		// dump($this->a);
	}
}