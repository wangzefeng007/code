<?php

namespace app\api\controller\v1; 


use app\admin\model\designdata\Index as DesignDataModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Border;


class Designdata  extends Base
{
    
    
    protected $tree_data = [];
    //用来存表格全局
    protected $sheet_all = null;
    
    public function add()
    {
        $name = $this->request->post('name','');
        $uid = $this->auth->id;
        $company = $this->get_companyid(); 
        if(!$name)
        {
            $this->error('名字不能为空');
        } 
           $designDataModel = new DesignDataModel;
        // 判断是否已经存在
        $lis = $designDataModel->where('name',$name)->where('deletetime','null')->find();
       if($lis)
       {
          $this->error('当前名称已经存在,不能添加重复名称');
       }
        $designDataModel->name = $name;
        $designDataModel->create_user = $uid;
        $designDataModel->company = $company;
        $designDataModel->save();
        $this->success('新增成功');
    }
    
    //重命名
    public function renames()
    {
        $id = $this->request->post('id');
        $name = $this->request->post('name');
         if(!$name)
        {
            $this->error('名字不能为空');
        } 
         $designDataModel = new DesignDataModel;
        // 判断是否已经存在
        $lis = $designDataModel->where('name',$name)->where('deletetime',null)->find();
       if($lis)
       {
          $this->error('当前名称已经存在,不能重复名称');
       }
        $dae['name'] = $name;
        $result = $designDataModel->save($dae,['id'=>$id]);
        if($result)
        {
            $this->success('重命名成功');
        }else{
            $this->error('重命名失败');
        }
        
    }
   //设计数据首页
    public function index()
    {
        $designDataModel = new DesignDataModel;
        $company_id = $this->get_companyid(); 
        $list = $designDataModel->field('id,name,imgas_id')->where('company',$company_id)->where('deletetime','null')->select();
        $this->success('',$list);
    } 
    
    //设计数据导出
    public function export_data()
    {
        $path = 'http://api.tao5g.cn//static/designdata/设计数据模板.xlsx';
        $this->success('',$path);
    }
    
    //设计数据导入
    public function import_data()
    {
        /**
         *注意事项 
         * A列是当前数据id
         * 从B列开始 每增加一列 那么树形图结构就等级多一个
         * 树形结构要注意是用汉字进行排版树形结构
         * 如果所有数据中有空值就报错
         * 
         * 
         * 第一步 需要先判断数据id是否是固定的位数 并且所有的数据都统一长度 如果长度不统一就报错
         * 
         */
         //处理文件数据太大导致无法导入 
         //改变数据库字段类型 把text改成longtext
         
         
        $id = $this->request->post('id');
        $file = $this->request->file('excel','');
        if(!$file)
        {
            $this->error('请导入excel文件');
        }
         //开始处理导入功能
        $files = $_FILES['excel'];
        $reader = IOFactory::createReader('Xlsx');
        
        // 打开文件 载入excel表格
        $spreadsheet = $reader->load($_FILES['excel']['tmp_name']);
         // 获取活动工作簿
        $sheet = $spreadsheet->getActiveSheet();
        
        $this->sheet_all = $sheet;
        // 获取内容的最大列 如 D
         
        $highest = $sheet->getHighestColumn();
        // dump($highest);die;
         // 获取内容的最大行 如 4
        $row = $sheet->getHighestRow();
        
        if($row <= 1)
        {
            $this->error('当前表格表身没有数据,终止导入');
        }
        //先确定第一列是数据id
        $a1 =  $sheet->getCellByColumnAndRow(1, 1)->getValue();
        //验证数据id是否重复
        //使用方法 就是获取全部 然后再验证去重后比较两者的 长度是否一致
        
         
         $arr1 = [];
         for($k = 2; $k <= $row ;$k++)
        {
           $arr1[] = $sheet->getCellByColumnAndRow(1,$k)->getValue();
        }         
        $arr2 = array_unique($arr1);
        
        if(count($arr1) != count($arr2))
        {
            $this->error('设计数据的数据id里有重复值');
        } 
        if($a1 != '数据id')
        {
            $this->error('A列必须由数据id,且A1必须是 数据id 字眼');
        }
        //验证数据id是否统一长度的  长度为14
        for($a = 2;$a <= $row; $a++)
        {
            $value =  $sheet->getCellByColumnAndRow(1, $a)->getValue(); 
            
            if(strlen(strval($value)) != 14)
            {
                $msg = '在A'.$a.'的数据id长度不是14位,请修改完成重新上传';
                $this->error("$msg");
            } 
        } 
        unset($value);
        //这是个树形数据
        $tree_data = [];    
        //这是个表格数据
        $table_data = [];       
        //表格表头
        $table_th = [];
          //表格表头
        $table_tr = [];
        //表身数据
        $table_body = [];
        
        //把字母转化成数字
        $hight_num = $this->lettertonum($highest);
        //从第一列第二行开始
        for($i = 1 ; $i <= $hight_num+1 ; $i++)
        {
            for($j = 1;$j <= $row; $j++)
            {
                $value =  $sheet->getCellByColumnAndRow($i, $j)->getValue();
                
                //树形图设计
                //从第二列的第二行开始才是属于树形图
                // if($i>=2 && $j>=2)
                // {
                //     dump($value);die;
                // }
                // dump();die;
                // 开始处理数据
                // 先获取表头信息
                if($j == 1)
                {
                    $table_th[] = $value;
                }
                //数据id
                if($i == 1 && $j > 1)
                {
                    $dataid_list[] = $value;
                }
                
                //获取值表格数据
                if($i >= 1 && $j > 1)
                {
                   $table_body[$i][] = $sheet->getCellByColumnAndRow($i, $j)->getValue();
                } 
                if($i >1 && $j >1)
                {
                     $tree_key = $sheet->getCellByColumnAndRow($i, $j)->getValue();
                     $tree_body[$i][] = $tree_key.'[]'.$i.'#'.$j;
                }
                
                
            }
        } 
        // dump($table_body);
        // dump($tree_body);die;
        //重新排序
        $table_body =   array_values($table_body);
   
        $tree_body =   array_values($tree_body);
        //存入数据表中
        $table['table']['body'] = $table_body;
             
        $table['table']['th'] = $table_th;
        //组装表格
       $table_list =  $this->set_table($table_th,$table_body,$row-1);
       
       $table_value = json_encode($table_list);
      
        //组装树形图
        // dump($tree_body);die;
        $tree_list = $this->set_tree($tree_body,$dataid_list); 
         
        $tree_list = json_encode($tree_list); 
        
         $designDataModel = new DesignDataModel; 
         $dae['tree_value'] = $tree_list;
         $dae['table_value'] = $table_value;
        //  dump($dae);die;
         $designDataModel->save($dae,['id'=>$id]);
         
         
         $this->success('导入成功');
        
        
        
    }
    //组装表格
    public function set_table($table_th,$table_body,$rownum)
    {
        
        $data = [];
        $table_ths = []; 
        $p = 0;
        for($i= 0; $i < count($table_th);$i++)
        {
            $table_ths[$i]['title'] = $table_th[$i];
            $table_ths[$i]['dataIndex'] = 'index_'.$i;
            $table_ths[$i]['width'] = '200px';
            $p = $i;
        }  
        $colnum = count($table_body);
        for($j = 0;$j < $rownum;$j++)
        {
            for($h = 0;$h < $colnum;$h++)
            {
                $list[$j]['index_'.$h] = $table_body[$h][$j];
                
            }
            //设计数据里绑定表单和模板使用的
            $list[$j]['index_9998'] = NULL;  
            $list[$j]['index_9999'] = NULL; 
        }
        //添加 绑定表单  绑定模板
        
        
        $form_array['title'] = '关联表单';
        $form_array['dataIndex'] = 'index_9998';
        $form_array['width'] = '200px';
        array_push($table_ths,$form_array);
        $form_array['title'] = '关联模板';
        $form_array['dataIndex'] = 'index_9999';
        $form_array['width'] = '200px';
        array_push($table_ths,$form_array); 
        $data['table_th'] = $table_ths;
        $data['list'] = $list;
        return $data;
    }
    
    //组装树形图  认祖归宗法
    public function set_tree($tree_body,$dataid_list)
    { 
        // dump($tree_body);die;
        // 虚拟id
        $id = 1;
        $data = [];
        //给值设置id 和p_name
        for($i = 0 ;$i< count($tree_body);$i++)
        {
            for($j = 0; $j < count($tree_body[$i]); $j++)
            {
                $data[$i][$j]['id'] = $id;
                $data[$i][$j]['value'] = $tree_body[$i][$j];
                if($i == 0)
                {
                    $data[$i][$j]['p_name'] = 0;
                }else{
                    $data[$i][$j]['p_name'] = $tree_body[$i-1][$j];
                }
                
                $id++;
            }
        }
        //设置pid = 0
        
        foreach($data as $key=> $value)
        {
            foreach($value as $kk =>$vv)
            {  
                if($vv['p_name'] == '0')
                {
                    $data[$key][$kk]['pid'] = '首';
                    unset($data[$key][$kk]);
                }else{ 
                    // dump($data[$key][$kk]['p_name']);die;
                    //单独把pid =  p_name 会导致数据冗余 需要把前一列的数据也要放置进入
                    // 也就是 从第3列数据开始 一层层的加进来 变成一个整体的父id
                    // $data[$key][$kk]['pid'] = $data[$key][$kk]['p_name'];
                    // dump($data[$key][$kk]);
                  list($data[$key][$kk]['id'],$data[$key][$kk]['pid'],$data[$key][$kk]['value']) = $this->getPid($data[$key][$kk]['p_name']); 
                //   dump($data[$key][$kk]['p_name']);
                }
            } 
            
        }
        
    //   die;
        //将二维数组转化成一维 且去除p_name 
        $list = [];
        foreach($data as $key => $value)
        {
            foreach ($value as $kk=> $vv)
            { 
                unset($vv['p_name']);
                $list[] = $vv;
            }
        }
        //  dump($list);die;
        //去重pid = 0;将id转化成字符串
        $pid_0_arr = [];
        foreach($list as $kkk => $vvv)
        {
            if($vvv['pid'] == '首')
            {
                unset($vvv['id']);
                $vvv['id'] = $vvv['value'];
                $vvv['label'] = $vvv['value'];
                $pid_0_arr[] = $vvv;
            }
        } 
        
        //不带id的pid = 0; 并重新整理id =字符串
        $pid_0_arr =  array_unique($pid_0_arr, SORT_REGULAR);
    //   dump($pid_0_arr);die;
        // 把所有的id转化成字符串  
        $str_list = [];
        foreach($list as $kk => $vv)
        {
            if($vv['pid'] != '首')
            {
                // $vv['id'] = $vv['value'];
                $vv['label'] = $vv['value'];
                $str_list[] = $vv;
            }
        }
        //pid = 0的合并
        $result = array_merge($pid_0_arr,$str_list); 
        // dump($list);die;
        $result =  array_unique($result, SORT_REGULAR);
        // dump($result);die; 
        $list_tree = $this->getChild($result,'首');
        return $list_tree;
        
    }
    //获取第三列的之后的pid 获取上一级的数据
    public function getPid($data)
    {   
        // dump($data);die;
         $highest = $this->sheet_all->getHighestColumn();
      
        //$data = 高速铁路[]2#2  前面一个是原值 后面一个是坐标  如果第一个是2 就返回 自己 或者返回首
        $array_all = explode('[]',$data);
        $array_one = $array_all[0];
        $array_two = $array_all[1];
        $array_colrow = explode('#',$array_two);
        $array_col = $array_colrow[0];
        $array_row = $array_colrow[1];
        // dump($array_one);die;
        if ($array_col == 2) {
            return array($array_one,'首',$array_one);
            // return '首';
        }elseif ($array_col > 2) {
            // dump($data);die;
            //开始找儿子
            $pid_name = '';
            $id_name = '';
            //获取id
            // dump($data);die;
            for($i = 2; $i <= $array_col; $i++ )
            {
              $id_name .= $this->sheet_all->getCellByColumnAndRow($i,$array_row)->getValue();
            }    
            //获取pid
            for($i = 2; $i < $array_col; $i++ )
            {
              $pid_name .= $this->sheet_all->getCellByColumnAndRow($i,$array_row)->getValue();
            }  
            return array($id_name,$pid_name,$array_one);
        }
        else{
            $this->error('当前数据表有误,请修改后重新上传,或联系管理员');
        }
        
        
        // explode('#',$array_all);
        // dump($array_col);die;
        
    }
    
    
    function getChild($data, $id)
    {
          // dump($data);die;
        $child = array();
        // dump($data);die;
        foreach ($data as $key => $datum) {
            if ($datum['pid'] == $id) {
				// dump($datum);die;
                $datum['children'] = $this->getChild($data, $datum['id']);
                $child[] = $datum;
                unset($data[$key]);

            }

        }

        return $child;
  
    }
    
    
    //表格结构的设计数据详情
    public function table_details()
    {
        $id = $this->request->post('id','');
        if(!$id)
        {
            $this->error('id不能为空');
        }
        $designDataModel = new DesignDataModel;
        $list = $designDataModel->where('id',$id)->find();
        $result = json_decode($list['table_value'],true);
        $this->success('',$result); 
    }
    
    //树形结构的设计数据详情
    public function tree_details()
    {
        $id = $this->request->post('id','');
        if(!$id)
        {
            $this->error('id不能为空');
        }
        $designDataModel = new DesignDataModel;
        $list = $designDataModel->where('id',$id)->find();
        $result = json_decode($list['tree_value'],true);
        $this->success('',$result); 
    }
    
     //字母转数字 仅限大写字母
    public function lettertonum($letter)
    {   
        //验证当前字符串是否是纯字母的
        $pa = preg_match("/^[a-zA-Z\s]+$/",$letter);
        if(!$pa)
        {
            return false;
        }
        $letter = strtoupper($letter);
        //记得要验证除大写以外的都不要
        if(strlen($letter) == 1)
        {
            return ord($letter)-64;
        }
        if(strlen($letter) > 1)
        {
          $num =  (strlen($letter)-1) * 26 + (ord(substr($letter,0,1)) - 64);
          return $num; 
            
        }
    }
    //数字转大写字母  从0开始
    public function numtoletter($num)
    {
       $keys = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ'];
       return $keys[$num];;
    }
    
    
    
}
    
    
