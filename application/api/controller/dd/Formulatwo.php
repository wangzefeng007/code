<?php


namespace app\api\controller\dd;


use function PHPSTORM_META\type;
use app\api\controller\dd\Formulathree;

use think\Controller;
use think\Db;
use think\Model;
use Session;

/**
 * 数据联动/公式计算 接口
 */
class Formulatwo extends Base
{
	public function _initialize()
	{
		
		$this->Formulathree= new Formulathree;
	}
	
	function Formula_Sum($json_str)
	{
		global $_FormulaObj;
		
		$SUM = "/(SUM\(([^()]|(?R))*\))/";
		$SUM_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($SUM, $json_str, $SUM_LIST);
		
		if(!empty($SUM_LIST))
		{
			$list_count=count($SUM_LIST[0]);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $SUM_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				
				if(!empty($str))
				{
					$item_str=$this->Formula_Sum($str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------进sum".$last_str."------";
			$new_str = $this->Formulathree->SUM($last_str);
			// echo "------出sum".$new_str."------";
			return $new_str;
		}
		
	}
		
	//LOOP PRODUCT
	function Formula_Product($json_str)
	{
		global $_FormulaObj;
		
		$PRODUCT = "/(PRODUCT\(([^()]|(?R))*\))/";
		$PRODUCT_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($PRODUCT, $json_str, $PRODUCT_LIST);
		
		if(!empty($PRODUCT_LIST))
		{
			$list_count=count($PRODUCT_LIST[0]);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $PRODUCT_LIST[0][$num];
				$str = mb_substr($old, 8);
				$str = mb_substr($str, 0, -1);
				
				if(!empty($str))
				{
					$item_str=$this->Formula_Product($str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			//echo "------".$last_str."------";
			$new_str = $this->Formulathree->PRODUCT($last_str);
			//echo "------".$new_str."------";
			return $new_str;
		}
		
	}
		
	//LOOP MIN
	function Formula_ZTMin($json_str)
	{
		global $_FormulaObj;
		
		$ZTMIN = "/(ZTMIN\(([^()]|(?R))*\))/";
		$ZTMIN_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($ZTMIN, $json_str, $ZTMIN_LIST);
		
		if(!empty($ZTMIN_LIST))
		{
			$list_count=count($ZTMIN_LIST[0]);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $ZTMIN_LIST[0][$num];
				$str = mb_substr($old, 6);
				$str = mb_substr($str, 0, -1);
				
				if(!empty($str))
				{
					$item_str=$this->Formula_ZTMin($str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->ZTMIN($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
		
	}
	function Formula_MAX($json_str)
	{
		// $json_str ='1,2,3';
		$MAX = "/(MAX\(([^()]|(?R))*\))/";
		$MAX_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MAX, $json_str, $MAX_LIST);
		
		if(!empty($MAX_LIST))
		{
			$list_count=count($MAX_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MAX_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");
					$item_str=$this->Formula_MAX($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->MAX($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
		
	}
	function Formula_AND($json_str)
	{
		// $json_str ='1,2,3';
		$AND = "/(AND\(([^()]|(?R))*\))/";
		$AND_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($AND, $json_str, $AND_LIST);
		
		if(!empty($AND_LIST))
		{
			$list_count=count($AND_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $AND_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");
					$item_str=$this->Formula_AND($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->xAND($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
		
	}
	function Formula_OR($json_str)
	{
		// $json_str ='1,2,3';
		$OR = "/(OR\(([^()]|(?R))*\))/";
		$OR_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($OR, $json_str, $OR_LIST);
		
		if(!empty($OR_LIST))
		{
			$list_count=count($OR_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $OR_LIST[0][$num];
				$str = mb_substr($old, 3);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");
					$item_str=$this->Formula_OR($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->xzOR($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
		
	}
	function Formula_POWER($json_str)
	{
		// $json_str ='1,2,3';
		$POWER = "/(POWER\(([^()]|(?R))*\))/";
		$POWER_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($POWER, $json_str, $POWER_LIST);
		
		if(!empty($POWER_LIST))
		{
			$list_count=count($POWER_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $POWER_LIST[0][$num];
				$str = mb_substr($old, 6);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");
					$item_str=$this->Formula_POWER($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$A = explode(",",$last_str);
			$new_str = $this->Formulathree->POWER($A[0],$A[1]);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
		
	}
	function Formula_FIXED($json_str)
	{
		// $json_str ='1,2,3';
		$FIXED = "/(FIXED\(([^()]|(?R))*\))/";
		$FIXED_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($FIXED, $json_str, $FIXED_LIST);
		
		if(!empty($FIXED_LIST))
		{
			$list_count=count($FIXED_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $FIXED_LIST[0][$num];
				$str = mb_substr($old, 6);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");
					$item_str=$this->Formula_FIXED($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$A = explode(",",$last_str);
			$new_str = $this->Formulathree->FIXED($A[0],$A[1]);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
		
	}
	function Formula_ABS($json_str)
	{
		// $json_str ='1,2,3';
		$ABS = "/(ABS\(([^()]|(?R))*\))/";
		$ABS_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($ABS, $json_str, $ABS_LIST);
		
		if(!empty($ABS_LIST))
		{
			$list_count=count($ABS_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $ABS_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");
					$item_str=$this->Formula_ABS($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->ABS($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
		
	}
	function Formula_RAND($json_str)
	{
		// $json_str ='1,2,3';
		$RAND = "/(RAND\(([^()]|(?R))*\))/";
		$RAND_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($RAND, $json_str, $RAND_LIST);
		
		if(!empty($RAND_LIST))
		{
			$list_count=count($RAND_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $RAND_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");
					$item_str=$this->Formula_RAND($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->RAND();
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
		
	}
	function Formula_INT($json_str)
	{
		// $json_str ='1,2,3';
		$INT = "/(INT\(([^()]|(?R))*\))/";
		$INT_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($INT, $json_str, $INT_LIST);
		
		if(!empty($INT_LIST))
		{
			$list_count=count($INT_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $INT_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");
					$item_str=$this->Formula_INT($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->INT($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
		
	}
	function Formula_MIN($json_str)
	{
		// $json_str ='1,2,3';
		$MIN = "/(MIN\(([^()]|(?R))*\))/";
		$MIN_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MIN, $json_str, $MIN_LIST);
		
		if(!empty($MIN_LIST))
		{
			$list_count=count($MIN_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MIN_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");
					$item_str=$this->Formula_MIN($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->MIN($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
		
	}
	function Formula_MOD($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(MOD\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
		
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
		
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");
					
					$item_str=$this->Formula_MOD($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$A = explode(",",$last_str);
			$new_str = $this->Formulathree->MOD($A[0],$A[1]);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
		
	}
	function Formula_IF($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(IF\(([^()]|(?R))*\))/";
		$MOD_LIST = [];

		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);

		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 3);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");

					$item_str=$this->Formula_IF($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$A = explode(",",$last_str);
			$new_str = $this->Formulathree->IFT($A[0],$A[1],$A[2]);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}

	}
	function Formula_IFS($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(IFS\(([^()]|(?R))*\))/";
		$MOD_LIST = [];

		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);

		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");

					$item_str=$this->Formula_IFS($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->IFS($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}

	}
	function Formula_CONCATENATE($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(CONCATENATE\(([^()]|(?R))*\))/";
		$MOD_LIST = [];

		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);

		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 12);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");

					$item_str=$this->Formula_CONCATENATE($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->CONCATENATE($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}

	}
	function Formula_UNION($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(UNION\(([^()]|(?R))*\))/";
		$MOD_LIST = [];

		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);

		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 6);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");

					$item_str=$this->Formula_UNION($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->UNION($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}

	}

	
	//datedif
	function Formula_DATEDIF($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(DATEDIF\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 8);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump("aaaa");
	
					$item_str=$this->Formula_DATEDIF($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->DateDiff($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	
	//ROUND
	function Formula_ROUND($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(ROUND\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 6);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_ROUND($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->ROUNDT($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//LEFT
	function Formula_LEFT($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(LEFT\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 5);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_LEFT($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->LEFT($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//LEN
	function Formula_LEN($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(LEN\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_LEN($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->LEN($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//RIGHT
	function Formula_RIGHT($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(RIGHT\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 6);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_RIGHT($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->RIGHT($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//MID
	function Formula_MID($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(MID\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_MID($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->MID($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//COUNT
	function Formula_COUNT($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(COUNT\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 6);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_COUNT($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->COUNT($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	// //MIN
	// function Formula_MIN($json_str)
	// {
	// 	// $json_str ='1,2,3';
	// 	$MOD = "/(MIN\(([^()]|(?R))*\))/";
	// 	$MOD_LIST = [];
	
	// 	$list_count=0;
	// 	$last_str=$json_str;
	// 	preg_match_all($MOD, $json_str, $MOD_LIST);
	
	// 	if(!empty($MOD_LIST))
	// 	{
	// 		$list_count=count($MOD_LIST[0]);
	// 		// dump($list_count);
	// 		for ($num = 0; $num < $list_count; $num++) {
	// 			$old = $MOD_LIST[0][$num];
	// 			$str = mb_substr($old, 4);
	// 			$str = mb_substr($str, 0, -1);
	// 			// dump($str);
	// 			if(!empty($str))
	// 			{
	// 				// dump($str);
	
	// 				$item_str=$this->Formula_MIN($str);
	// 				// dump($item_str);
	// 				if(!empty($item_str))
	// 				{
	// 					$last_str = str_replace($old, $item_str, $last_str);
	// 				}
	// 			}
	// 		}
	// 	}
	// 	if(!empty($last_str))
	// 	{
	// 		// echo "------JIN".$last_str."------";
	// 		$new_str = $this->Formulathree->MIN($last_str);
	// 		// echo "------CHU".$new_str."------";
	// 		return $new_str;
	// 	}
	
	// }
	//TIME
	function Formula_TIME($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(TIME\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 5);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_TIME($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->TIME($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//TODAY
	function Formula_TODAY($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(TODAY\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 5);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_TODAY($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->TODAY($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//YEAR
	function Formula_YEAR($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(YEAR\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 5);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_YEAR($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->YEAR($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//MONTH
	function Formula_MONTH($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(MONTH\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 6);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_MONTH($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->MONTH($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//DAY
	function Formula_DAY($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(DAY\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_DAY($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->DAY($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//TIMESTAMP
	function Formula_TIMESTAMP($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(TIMESTAMP\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 10);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_TIMESTAMP($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->TIMESTAMP($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//DATEDELTA
	function Formula_DATEDELTA($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(DATEDELTA\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 10);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_DATEDELTA($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->DATEDELTA($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//DAYS
	function Formula_DAYS($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(DAYS\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 5);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_DAYS($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->DAYS($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//NOW
	function Formula_NOW($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(NOW\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 4);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_NOW($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->NOW($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//MAPX
	function Formula_MAPX($json_str)
	{
		// $json_str ='1,2,3';
		$MOD = "/(MAPX\(([^()]|(?R))*\))/";
		$MOD_LIST = [];
	
		$list_count=0;
		$last_str=$json_str;
		preg_match_all($MOD, $json_str, $MOD_LIST);
	
		if(!empty($MOD_LIST))
		{
			$list_count=count($MOD_LIST[0]);
			// dump($list_count);
			for ($num = 0; $num < $list_count; $num++) {
				$old = $MOD_LIST[0][$num];
				$str = mb_substr($old, 5);
				$str = mb_substr($str, 0, -1);
				// dump($str);
				if(!empty($str))
				{
					// dump($str);
	
					$item_str=$this->Formula_MAPX($str);
					// dump($item_str);
					if(!empty($item_str))
					{
						$last_str = str_replace($old, $item_str, $last_str);
					}
				}
			}
		}
		if(!empty($last_str))
		{
			// echo "------JIN".$last_str."------";
			$new_str = $this->Formulathree->MAPX($last_str);
			// echo "------CHU".$new_str."------";
			return $new_str;
		}
	
	}
	//Check
	function ChkExist_Formula($json_str,$formula_key='')
	{
		
		$check_isexist=false;
		//判断使用的是小写函数名
		$all_formula = array("and","or","if","ifs","ifsisempty","concatenate","union","left","len","right","mid","round","count","min","max","int","mod","sum","rand","abs","fixed","power","datedif","time","today","year","month","day","timestamp","datedelta","days","now","mapx");
		$formula_count = count($all_formula);
		
		if(!empty($formula_key))
		{
			for($i=0; $i<$formula_count; $i++) {
				$item_str=$all_formula[$i];
				if($item_str != $formula_key)
				{
					if(strpos(strtolower($json_str),$item_str.'(')!==false)
					{
						$check_isexist=true;
						break;
					}
				}
			}
		}
		else
		{
			//检测包含任何其中一个
			for($i=0; $i<$formula_count; $i++) {
				$item_str=$all_formula[$i];
				if(strpos(strtolower($json_str),$item_str.'(')!==false)
				{
					$check_isexist=true;
					break;
				}
			}
		}
		return $check_isexist;
	}
}