<?php

namespace app\api\controller\v1; 
use app\admin\model\score\Userscore as UserscoreModel;
use app\admin\model\score\Userscorelist as UserscorelistModel;
use app\admin\model\diytable\Conventionalmother as ConventionalmotherModel;
use think\Validate; 
use think\Db;
use app\admin\model\diytable\Conventionaltype as ConventionaltypeModel;
use app\admin\model\apply\Templateall as TemplateallModel;
use app\admin\model\template\Key as KeyModel;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// 导出表
use think\Loader;
use think\Env;
use think\File;
//最新版本的php导入导出 
// use phpOffice\phpspreadsheet\IOFactory;
  



class Excle  extends Base
{
    protected $style_data = [];
    //当前表头和内容
    protected $drr_data = [];
     //保存模板
    public function save_template()
     {
        $uid = $this->auth->id;
        $table_old = $this->request->post('table_old','');
        $variate_json = $this->request->post('variate_json',''); 
        $tid = $this->request->post('tid','');
        $data['table_old_json'] = urldecode($table_old);
        
        $data['uid'] = $uid;
        $data['company_id'] =  $this->get_companyid();  
    //   判断是否已经保存 如果已经保存就只做修改
        $templateallModel = new TemplateallModel;
        
        $where['tid'] = $tid;
        $li = $templateallModel->where($where)->find();
        
       
        //如果tid 不存在数据库中就添加
        if(!$li)
        {
            $data['tid'] =  $tid;
            $templateallModel->data($data);
            $templateallModel->save();
            $this->success('保存成功');
        }
        $templateallModel->save($data,['tid'=>$tid]);
        $this->success('保存成功');
     }
     
    //  预览模板
    public function preview()
    {
        /**
         * 预览实现方式
         * 1先获取选中表单内容获取表单下标(当时用户选中的那个行坐标)
         * 2获取模板id 也就是用户需要展示的那个表单内容
         * 3获取对应模板id 将模板对应模板从葡萄城的样式文件转化成phpoffice文件
         * 4再进行变量替换
         * 5保存文件
         * 6返回保存的文件路径
         */ 
        // 模板id
        $form_id = $this->request->post('form_id');
        $model_id = $this->request->post('model_id');
        $modelvalue = $this->request->post('value');
        // $path = 'http://api.tao5g.cn//static/excel_download/2332c81e9f0775a2e932c3467f208551.xlsx';
         // $this->success('',$path);
        // dump($this->style_data);die;
        $where['tid'] = $model_id; 
        $li = DB::name('s_template_all')->where($where)->find(); 
        $data =htmlspecialchars_decode($li['table_old_json']); 
        $modelvalue = json_decode(htmlspecialchars_decode($modelvalue),true);
        $modelvalue_arr = [];
        foreach($modelvalue as $vv)
        {
            $modelvalue_arr[] = $vv;
        }
        // dump($modelvalue_arr);die;
        $data_all = $data = json_decode($data,true);
        $this->style_data = $data_all['namedStyles'];
        $path = $this->set_phpoffice_ptc($data,$form_id,$model_id,$modelvalue_arr);
        
        $da['path'] = $path;
         
         $this->success('导出成功',$da);
        
        
    }
    //将葡萄城json文件转化成PHPoffice生成的文件 并返回对应的路径
    
    public function set_phpoffice_ptc($data,$form_id,$model_id,$modelvalue)
    {
        // //把表格中的值读取出来
        // $values = [];
        // //把表格中的合并单元格读取出来
        // $spans =[];
        
        //先读取表位置
        // dump($data);die;
        $sheets = $data['sheets'];
        // dump($sheets);die;
        $keys = array_keys($sheets)['0'];
    //   dump($keys);die;
        $list = $sheets[$keys];
        //合并单元格 j记得要加1
        $spans = $list['spans'];
        //实例化对象
        $spreadsheet = new Spreadsheet();
        // 获取活动工作簿
        $sheet = $spreadsheet->getActiveSheet();
        // dump($sheet);die;
        //开始合并单元格 
        for($i = 0;$i < count($spans);$i++)
        {
            //调用数字转字母 从0 = A开始 记得数字不要超过100  
            //开始合并
            // $sheet->mergeCells('A1:B5'); 
            
            $a_col = $this->numtoletter($spans[$i]['col']);
            $a_row = $spans[$i]['row']+1;
            $a = $a_col.$a_row;
            $b_col = $this->numtoletter($spans[$i]['col']+$spans[$i]['colCount']-1);
            $b_row = $spans[$i]['row'] + $spans[$i]['rowCount'];
            $b = $b_col.$b_row;  
            //开始合并
            $sheet->mergeCells("$a:$b"); 
        }
        //合并完成之后开始设置每一列的大小
        // getColumnDimension 获取一列
        // setWidth 设置一列的宽度  //  $sheet->getColumnDimension('A')->setWidth(100);
        //获取当前所有列 
        $col_arr  = $list['columns']; 
        for($o = 0; $o < count($col_arr); $o++)
        {  
            $c = $this->numtoletter($o);
            $lic = $col_arr[$o]['size']*0.15;
            $sheet->getColumnDimension("$c")->setWidth($lic);
        }
        //设置行大小
        $row_arr  = $list['rows']; 
        // getRowDimension 获取一行
        // getRowHeight 获取一行的高度
        // setRowHeight 设置一行的高度   $sheet->getRowDimension(1)->setRowHeight(100);
       
        for($p=0;$p<count($row_arr);$p++)
        {
             $d = $p + 1 ;  
             $row_row = $row_arr[$p]['size']*0.65;
             $sheet->getRowDimension($d)->setRowHeight($row_row);
        } 
        //设置完合并设置完行列高度 开始放值
        /**
         * 放值注意事项 如果是变量名就要取出来获取变量值后再放入
         * 如果只是值不是变量就不用
         */ 
         $tabledata = $list['data']['dataTable'];
         
        //  dump($tabledata);
        //  dump(count($tabledata));
        //  die;
         //$q 是行  $qq是列 列要转成字母
         $tr_arr =[]; //这个用来存变量的值
         for($q = 0; $q < count($tabledata); $q++)
         {
             
             for($qq = 0;$qq < count($tabledata[$q]);$qq++)
             {
                  
                 if(isset($tabledata[$q][$qq]['value']))
                 {
                     //判断当前值是否是变量 
                    $is_sc = strstr($tabledata[$q][$qq]['value'],"??]");
                    //如果有值就返回值  如果不是变量替换的 就返回false 说明当前值是表格值
                    if($is_sc){
                        
                        
                        
                        // dump($tabledata[$q][$qq]['value']);die;
                        $le_a = $this->numtoletter($qq); 
                        //设置值
                        $tr_arrs =[];
                        $let_a =  $le_a.($q+1); 
                        $tr_arrs['letnum'] = $let_a;
                        $tr_arrs['col'] = $qq;
                        $tr_arrs['row'] = $q;
                        $tr_arrs['value'] = $tabledata[$q][$qq]['value'];
                        //把变量提取出来到后面转化  提取的方式1 先转化成坐标形式 原数据
                        $tr_arr[] =$tr_arrs;
                        // dump($tr_arrs);die;
                    }
                    else{
                        $le = $this->numtoletter($qq); 
                        //设置值
                        $let =  $le.($q+1); 
                        $sheet->setCellValue($let,$tabledata[$q][$qq]['value']);
                        //设置当前格样式 暂时不做样式处理
                        //先获取原有单元格格式
                        $style = $tabledata[$q][$qq]['style']; 
                        list($font1,$font2,$font3,$font4) = $this->get_style($style);
                        //仅设置字体
                        // $sheet->getStyle($let)->getFont()->setBold($font2)->setName($font4)->setSize($font3);
                        
                        //dump($style);die;
                        
                        // 设置样式数组
                        $styleArray['alignment']['horizontal'] = 'center';  //水平居中
                        $styleArray['alignment']['vertical'] = 'center'; //垂直居中
                        
                        // $styleArray['borders']['outline']['borderStyle'] = 'thick';  //
                        
                        // $styleArray['borders']['allBorders']['borderStyle'] = Border::BORDER_THIN;//细边框
                        // $styleArray['borders']['outline']['color'] = ['argb' => 'FFFF0000'];  //
                        
                        $styleArray['font']['name'] = $font4; 
                        $styleArray['font']['bold'] = $font2; 
                        $styleArray['font']['size'] = $font3; 
                        
                        
                        $sheet->getStyle($let)->applyFromArray($styleArray);
                        
                        //在设置单元格样式
                    }
                 }   
             }
         } 
         //将变量转化成实际的数据  并通过数据转化 
          
         /***
          *重要的一步  
          * 第一步
          * 获取到表单当前空的值
          * 第二步把表单当前空的值赋值上input和model
          * 第三步开始放值
          * 
          */ 
        //   dump($form_id);die;
        // dump($form_id);die;
        $i = 0;
        $list = Db::name('s_apply_structure')->where('id',$form_id)->find();
       
        if(!$list['list'])
        {
            $this->error('当前表单没有添加工序');
        }
        $file_list = json_decode($list['list'],true);
        // dump($file_list);die;
        $filedata = [];
        // $filed = 3;
        $field = [];
        $table_data_name = [];
        foreach($file_list as $key => $value)
        {
            // dump($file_list);die;
            //$value 是一个工序 
            // dump($value);die;
            //一个工序一张表  $value['key']对应的是这个表名  $value['list'] 表示字段
            $table_data_name[] = $value['key'];
            foreach($value['list'] as $k => $v)
            { 
                // $v  表示当前表中的字段  $v['key'] 表示当前字段名$v['label'] 表示当前提示的信息
                //第一行数据
                $filedata[$i]['label'] = $v['label'];
                $labels[] = $v['label'];
                //第二行数据
                $filedata[$i]['key']   = $value['key'].'_'.$v['key']; 
                $filedata[$i]['model'] = $value['key']; 
                $filedata[$i]['field'] = $v['key']; 
                $field[] = $v['key']; 
                $i++;
            } 
        }
        $d_arr =[];
        for($d =0 ;$d<count($modelvalue);$d++)
        { 
            // dump($filedata[$d]);die;
            $p_a_temp['value'] = $modelvalue[$d];
            $p_a_temp['field'] = $filedata[$d]['field'];
            $p_a_temp['model'] = $filedata[$d]['model'];
            $p_a_temp['label'] = $filedata[$d]['label'];
            $d_arr[] = $p_a_temp;
        }
        unset($p_a_temp);
        
        //获取变量 $tr_arr
        //获取当前字段值 $d_arr
        
        // dump($d_arr);
        // dump(1213);
        // dump($tr_arr);die;
        $keyModel = new KeyModel;
        
        
        $this->drr_data = $d_arr;
        
        for($iii = 0; $iii< count($tr_arr);$iii++)
        { 
            $value_str_replace = $this->_str_replace($tr_arr[$iii]['value']);
            $sheet->setCellValue($tr_arr[$iii]['letnum'], $value_str_replace); 
        }
         
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');//创建生成的格式
    $path = '.'.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'excel_download'.DIRECTORY_SEPARATOR.md5(time());
    $result = $writer->save($path . '.xlsx');//表示在$path路径下面生成demo.xlsx文件
    $path = trim($path,'.');
    $path = $this->request->domain().DIRECTORY_SEPARATOR.$path.'.xlsx'; 
    return $path;
    }
    
    //用于变量替换使用  $strs 总字符串 $str 当前字符串变量值  $st 要替换的值 
    public function _str_replace($str)
    { 
         
        $data = $this->drr_data;
        $result = '';
        for($i = 0; $i < count($data);$i++)
        {
            //先查看字符串是否有在字符串中
           $strs =  '['.$data[$i]['label'].'??]'; 
           $ii = strpos($str,$strs);
        //   如果存在
           if ($ii !== false) {
             $str = str_replace($strs,$data[$i]['value'],$str);
           }
            
            
            
        }
        
    //   dump($str);die;
        return $str;
    }
    
    
    //获取样式
    //暂时不做
    public function get_style($style_name)
    {
        $st = '';
        // dump($this->style_data);die;
        if (count($this->style_data) > 0) {
           
           for($i = 0 ; $i< count($this->style_data);$i++)
           {
               if($this->style_data[$i]['name'] == $style_name)
               {
                //   dump($this->style_data[$i]);die;
                   $st = $this->style_data[$i]['font'];
                //   dump($st);
               }
           }
           
           
        }
        // die;
        //设置默认样式
        if(!$st)
        {
          $st =  "normal normal 12px 宋体";
        }
         $st_arr = explode(' ',$st);
        //  dump($st_arr);die;
        if($st_arr[1] == 'bold')
        {
            $st_arr[1] = true;
        }else
        {
             $st_arr[1] = false;
        }
        $st_arr[2] = intval($st_arr[2]);
        //第二个返回值表示是否是粗体 第三个返回值表示文字大小 第四个返回表示字体
        return $st_arr;
    } 
    //根据key找vlaue 再根据value找表单内容
    public function get_key_value($key)
    {
       
           $elist = DB::name('s_variate_template_key')->where('value','like',"%".$key."%")->find();
           if($elist)
           {
              $form_id =  $elist['form_id'];
              $form_model = $elist['form_model']; 
              $list = DB::name('s_formprocess_list')->where('form_key',$form_id)->find(); 
              $weight = $list['weight'];
              $node_key = $list['node_key'];
              $table_name = 's_form_'.$node_key;
            //   $table_name = 's_form_'.$weight.'_'.$node_key;
              $ll =  DB::name($table_name)->field($form_model)->find(); 
            //   dump($ll[$form_model]);die;
              return $ll[$form_model];
           }
             
           
           
    }
    // 查看key对应的值
    
     public function get_template()
     {  
        $tid = $this->request->post('tid','');
        $where['tid'] = $tid;
        $where['company_id'] = $this->get_companyid();
       
        $templateallModel = new TemplateallModel;
        $list =  $templateallModel->where($where)->find();
        
        //  加密压缩后的文件
        if($list['table_old_json'])
        {
            
            $list['table_old_json'] = htmlspecialchars_decode($list['table_old_json']);
            
        
        } 
        
        $this->success('查看成功',$list);
     }
  
    // 加密中文乱码%u开头
    public function js_unescape($str)
     {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) 
        {
            if ($str[$i] == '%' && $str[$i+1] == 'u') {
                $val = hexdec(substr($str, $i+2, 4));
                if ($val < 0x7f) $ret .= chr($val);
                else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
                else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
                $i += 5;
            }
            else if ($str[$i] == '%') 
            {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            }
            else {
                $ret .= $str[$i];
            }
        }
        return $ret;
    }
    // 模板变量
    public function variatelist()
    {
         $id =  $this->request->post('id','');       
         $keyModel = new  KeyModel;        
         $inf = Db::name('s_apply_structure')->where('id',$id)->where('deletetime',0)->where('type',2)->find();   
         $data = [];        
         $li_key = $keyModel->order('id','desc')->find();     
         $list =[];
             $list_arr =[];   
             if($inf['list'])    
             {          
                 $dd = json_decode($inf['list'],true);     
                 for($ix = 0 ;$ix < count($dd);$ix++)     
                 {             
                     for($ic=0;$ic<count($dd[$ix]['list']);$ic++)          
                     {               
                         $na = [];             
                         $na['name'] = $dd[$ix]['list'][$ic]['label'];      
                         $na['model'] = $dd[$ix]['list'][$ic]['model'];    
                         $na['variate'] = '['.$dd[$ix]['list'][$ic]['label'].'??]';     
                         $na['variate_md'] = '['.$dd[$ix]['list'][$ic]['label'].'??]'.$dd[$ix]['list'][$ic]['model'];  
                         $list_arr[] = $na;  
                               }                     
                             }                         
                          }   
                         //   dump($list_arr);die;       
                         //保存到数据库中       //首先先找数据库中是否已经存在 如果已经存在就不保存 如果不存在就保存一份    
                         foreach ($list_arr as $yyk=>$yyv)    
                         {                     
                         $listsxx = []; 
                         $listsxx['name'] = $yyv['name'];
                         $listsxx['list'][$yyk]['label'] = $yyv['name'];        
                         $listsxx['list'][$yyk]['key'] = $yyv['variate'];       
                         
                         //如果覆盖了就用这个
                        //  $listsxx['name'] = $yyv['name'];
                        //  $listsxx['list']['label'] = $yyv['name'];        
                        //  $listsxx['list']['key'] = $yyv['variate'];   
                        
                        
                        
                        
                         $list[] =  $listsxx; 
                         unset($listsxx);         
                         $where['variate_md'] = $yyv['variate_md'];       
                         $lsi_key = $keyModel->where($where)->find();    
                         //如果不存在就保存一份
                         if(!$lsi_key)         
                         {      
                             $des['variate_md']=$yyv['variate_md']; 
                             $des['variate']=$yyv['variate'];   
                             $des['key']=$yyv['model'];  
                             $des['value']=$yyv['name'];        
                             $des['form_id']=$id;   
                             $des['form_model']=$yyv['model'];    
                             Db::name('s_variate_template_key')->insert($des);
                         }      
                } 
    //   dump($list);die;
       $this->success('',$list);
     
    }
    //把key值存到数据库表中 并且要把对应的位置保存到到value中
    public function set_key($i,$value,$e = '',$form_id)
    {
      
         $keyModel =  new KeyModel;
        //如果model已经在数据库中就不添加
        if($e)
        { 
          $elist = DB::name('s_variate_template_key')->where('value','like',"%".$e."%")->select();
        
           if($elist)
           {
               return;
           }
        }
       
        $where['value'] = $value;
        // 查看是否有重复的key
        $lists = $keyModel->where($where)->where('deletetime',0)->find();
       
        if($lists)
        {
            return;
        }
        $where['key'] = $i;
        // 查看是否有重复的key
        $list = $keyModel->where($where)->where('deletetime',0)->find();
        // 如果不存在就插入到数据库中
        if(!$list)
        {
            $keyModel->key = $i;
            $keyModel->value = $value;
            $keyModel->form_id = $form_id;
            $keyModel->form_model = $e;
            $keyModel->save();
            return ;
         }
         $i++; 
         $this->set_key($i,$value,$e,$form_id);
    }
    /**
     * 寻找模板应用位置
     * 1 先找应用 2再找表单 表单显示的内容为 应用分类-应用名
     */ 
    public function find_apply_variate()
    {
        $company_id = $this->get_companyid();
        $where['a.deletetime'] = 0;
        $where['a.company_id'] = $company_id; 
         $inf =  Db::table('fa_s_apply_formlist')
        ->alias('a')
        ->join('fa_s_apply_category w','a.category_id = w.id')
        ->where($where)
        ->field('a.*,w.cate_name')
        ->select();
        $data = [];
        foreach ($inf as $key => $value)
        { 
            $data[$key]['id'] = $value['id'];
            $data[$key]['name'] = $value['cate_name']."-".$value['name'];
        }
         $this->success('',$data);
    }
     /**
     * 寻找模板表单在应用中的位置
     * 1 先找应用 2再找表单 表单显示的内容为 应用-表单名
     */ 
    public function find_form_variate()
    {
        $id = $this->request->post('id','');
        $where['apply_id'] = $id;
        $where['type'] = 2;
        $where['deletetime'] = 0;
        $inf = Db::name('s_apply_structure')->where($where)->select();
        $data = [];
        foreach ($inf as $key => $value)
        {
            
                 $data[$key]['id'] = $value['id'];
                 $data[$key]['name'] = $value['str_name']; 
                 $data[$key]['str_pid'] = $value['str_pid']; 
        }
        $list = [];
        $wheres['deletetime'] = 0;
        $wheres['type'] = 1;
        $re_list = Db::name('s_apply_structure')->where($wheres)->select();
        foreach ($data as $k => $v)
        {
             foreach ($re_list as $key => $value)
             {
                 if($v['str_pid'] == $value['id'])
                 {
                     $list[$k]['id'] = $v['id'];
                     $list[$k]['name'] = $value['str_name'].'-'.$v['name'];
                 }
             }
        }
        $this->success('',$list);
    }
    
     /**
     * 寻找模板表单在应用中的位置
     * 1 先找应用 2再找表单 表单显示的内容为 应用-表单名
     */ 
    public function find_model_variate()
    {
        $id = $this->request->post('id','');
        $where['apply_id'] = $id;
        $where['type'] = 3;
        $where['deletetime'] = 0;
        $inf = Db::name('s_apply_structure')->where($where)->select();
        $data = [];
        foreach ($inf as $key => $value)
        {
            
                 $data[$key]['id'] = $value['id'];
                 $data[$key]['name'] = $value['str_name']; 
                 $data[$key]['str_pid'] = $value['str_pid']; 
        }
        $list = [];
        $wheres['deletetime'] = 0;
        $wheres['type'] = 1;
        $re_list = Db::name('s_apply_structure')->where($wheres)->select();
        foreach ($data as $k => $v)
        {
             foreach ($re_list as $key => $value)
             {
                 if($v['str_pid'] == $value['id'])
                 {
                     $list[$k]['id'] = $v['id'];
                     $list[$k]['name'] = $value['str_name'].'-'.$v['name'];
                 }
             }
        }
        $this->success('',$list);
    }
    
    
    /**
     *表单数据列表展示 可导入功能 
     */
    public function form_list_form()
    {
        $form_id = $this->request->post('id','');
        if(!$form_id)
        {
            $this->error('表单id不能为空');
        }
       $list = Db::name('s_apply_structure')->where('apply_id',$form_id)->where('type',2)->find();
       
       $list = json_decode($list['list'],true);
       dump($list);die;
       
       $num = count($list);
       $data = [];
       foreach ($list as $key => $value)
       {
           dump($value['list']);die;
          $data[$key][] = $value['list']['model'];
           
       }
       dump($data);die;
       
       
       $this->success($num); 
    }
    
     /**
     * 下载表单内容格式文件
     * 参数为表单id
     * 根据表单id
     * 获取当前表单都有几个表
     * 把所有的表对应的字段提取出来放到表格中 
     */
    public  function download_formfile()
    {
        $form_id = $this->request->post('form_id','6');
        // dump($form_id);die;
        $i = 0;
        $list = Db::name('s_apply_structure')->where('id',$form_id)->find();
       
        if(!$list['list'])
        {
            $this->error('当前表单没有添加工序无法导入');
        }
        $file_list = json_decode($list['list'],true);
        // dump($file_list);die;
        $filedata = [];
        // $filed = 3;
        $field = [];
        $table_data_name = [];
        foreach($file_list as $key => $value)
        {
            // dump($file_list);die;
            //$value 是一个工序 
            // dump($value);die;
            //一个工序一张表  $value['key']对应的是这个表名  $value['list'] 表示字段
            $table_data_name[] = $value['key'];
            foreach($value['list'] as $k => $v)
            { 
                // $v  表示当前表中的字段  $v['key'] 表示当前字段名$v['label'] 表示当前提示的信息
                //第一行数据
                $filedata[$i]['label'] = $v['label'];
                //第二行数据
                $filedata[$i]['key']   = $value['key'].'_'.$v['key']; 
                $filedata[$i]['model'] = $value['key']; 
                $filedata[$i]['field'] = $v['key']; 
                $field[] = $v['key']; 
                $i++;
            } 
        }
        // dump($filedata);die;
        $all_list = [];
        //把当前表单中的数据工序的表内容都提取出来
        foreach($table_data_name as $pk=>$pv)
        {
            $tablename = 's_form_'.$pv;
            $all_list[$pv] = Db::name($tablename)->select();
            
        }
        // dump($all_list);die;
        
        // dump($table_data_name);die;
        // 模板文件加入数据 
        // 把数据放入到模板数据中 
        //没有数据结构
        $table_data_referrer = [];
        //有数据结构
        $table_all_data_referrer = [];
       // 查看当前已经有几个流程在执行  几个流程表示当前有多少行(不包含)
        //type = 2  说明当前流程已完成  type = 1的时候 表示当前流程未完成 并且在当前字段中process 表示进行到第几个工序
        $countlist = Db::name('m_begin_process')->where('form_id',$form_id)->select();
        // dump($countlist);die;
       //表示当前多少列表
        $countcol = count($filedata);
        // 表示当前有几个表 就是有几道工序
        $counttable_num = count($table_data_name);
        // 用来存空的数据  如果空的数据长度等于系统长度就说明是没有值的
        $null_arr = [];
        // dump(count($countlist));die;
        // dump($countlist);die;
        foreach ($countlist as $kk => $vv)
        {
            
           
                foreach ($all_list as $kkk => $vvv)
                {
                    if(isset($vvv))
                    {
                         if(isset($vvv[$kk]))
                         {
                             $table_data_referrer[$kk][] = $vvv[$kk];
                         }else{
                             $table_data_referrer[$kk][] = [];
                         }
                    }
                    else
                    {
                         $table_data_referrer[$kk][] = [];
                    }
                   
                   
                }
        }  
      
       $ddr_arr = [];
       $merge_arr = [];
       
       foreach($table_data_referrer as $ffk => $ffv)
       {
           foreach ($ffv as $mmk => $mmv)
           {
            //   dump($mmv);die;
               if (isset($mmv['id'])) {
                   unset($mmv['id']);
               }
               if (isset($mmv['liuc_id'])) {
                   unset($mmv['liuc_id']);
               }
               $ffv[$mmk] = $mmv;
           } 
          $merge_arr[] = array_reduce($ffv, 'array_merge', array());
           
       } 
       $all_array=[];
       $rrt_data = [];
       for($j = 0; $j<count($field); $j++)
       {
           
           foreach($merge_arr as $mak => $mav)
           { 
              $is_c = array_key_exists($field[$j],$mav);
              if(!$is_c)
              {
                  $mav[$field[$j]] = '';
              } 
              $merge_arr[$mak] = $mav; 
           }
          
       } 
    //   dump($merge_arr);die;
      for($z = 0; $z <count($filedata);$z++)
      {
          foreach ($merge_arr as $meak=> $meav)
          {
              $filedata[$z]['col'][] = $meav[$filedata[$z]['field']]; 
              
          }
      
      }
    
    //   dump($merge_arr);die;
      $path = dirname(APP_PATH);//找到当前脚本所在路径
    //   dump($path);die;
    // dump($path);die;
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $PHPSheet = $spreadsheet->getActiveSheet();
      $PHPSheet->setTitle("demo");//给当前活动sheet设置名称
      $a = 'A';
      //设置第一行数据
      foreach ($filedata as $bbkk => $bbvv)
      {
          $PHPSheet->setCellValue("$a".'1',$bbvv['label']); 
          $a++;
      }
      //设置第二行数据
      $n = 2;
      $x = 0;
      $kye_ad = [];
      $keys = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ'];
      if(!$bbvv['col'])
      {
          $this->error('当前表单没有数据');
      }
         foreach ($filedata as $bbkk => $bbvv)
      {
          
        //   dump($keys[$bbkk]);
        //   $bbvv['col'];
        //   dump($filedata);die;
        
         for($s = 0; $s < count($bbvv['col']);$s++)
         {
             $nums = $n+$s;
             $PHPSheet->setCellValue($keys[$bbkk].$nums,$bbvv['col'][$s]); 
         }
           
      }
     
      $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');//创建生成的格式
      header('Content-Disposition: attachment;filename="表单数据.xlsx"');//下载下来的表格名
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      $path = '.'.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'excel_download'.DIRECTORY_SEPARATOR.md5(time());
    //   dump($path);die;
    //
//   dump(); die;
      $result = $writer->save($path . '.xlsx');//表示在$path路径下面生成demo.xlsx文件
    //   dump($result);die;
    //   dump($result);die;
    //   dump($PHPExcel);die;
        $path = trim($path,'.');
        $path = $this->request->domain().DIRECTORY_SEPARATOR.$path.'.xlsx'; 
         $da['path'] = $path;
         $this->success('导出成功',$da);
    }
    
    // 导入功能
    public function upload_excel()
    {
        // dump(1);die;
        $file = $this->request->file('excel');
        
        /**事先获取到当前表单的数据 一个是做验证
         * 
         */ 
         $form_id = $this->request->post('form_id','6');
        // dump($form_id);die;
        $i = 0;
        $list = Db::name('s_apply_structure')->where('id',$form_id)->find();
       
        if(!$list['list'])
        {
            $this->error('当前表单没有添加工序无法导入');
        }
        $file_list = json_decode($list['list'],true);
        // dump($file_list);die;
        $filedata = [];
        // $filed = 3;
        $field = [];
        $table_data_name = [];
        $model = [];
        foreach($file_list as $key => $value)
        {
            // dump($file_list);die;
            //$value 是一个工序 
            // dump($value);die;
            //一个工序一张表  $value['key']对应的是这个表名  $value['list'] 表示字段
            $table_data_name[] = $value['key'];
            foreach($value['list'] as $k => $v)
            { 
                // $v  表示当前表中的字段  $v['key'] 表示当前字段名$v['label'] 表示当前提示的信息
                //第一行数据
                $filedata[$i]['label'] = $v['label'];
                //第二行数据
                $filedata[$i]['key']   = $value['key'].'_'.$v['key']; 
                $filedata[$i]['model'] = $value['key']; 
                $filedata[$i]['field'] = $v['key']; 
                $field[] = $v['key']; 
                $model[] = $value['key'];
                $i++;
            } 
        }
       
        // dump($model);die;
        
        //开始处理导入功能
        $files = $_FILES['excel'];
        header("content-type:text/html;charset=utf-8");
        // 创建读操作
         
        $reader = IOFactory::createReader('Xlsx');
        // 打开文件 载入excel表格
        $spreadsheet = $reader->load($_FILES['excel']['tmp_name']);
        // 获取活动工作簿
        $sheet = $spreadsheet->getActiveSheet();
        // 获取内容的最大列 如 D
        $highest = $sheet->getHighestColumn();
        
        
        // 获取内容的最大行 如 4
        $row = $sheet->getHighestRow();
        //  dump(ord('z'));die;
        $num = $this->lettertonum($highest);
        //比较一下数据是否跟数据库的数据是统一的 
         if(count($filedata[0]) > $num)
         {
             $this->error('您上传的表单表头数量少于录入的数量');
         }
         if(count($filedata) < $num)
         {
             $this->error('您上传的表单表头数量大于录入的数量');
         }
        $data = [];
        // dump($num);
        // dump($row);
        // die;
        // dump($sheet->getCellByColumnAndRow(2,3)->getValue());die;
        for($k = 2; $k <= $row; $k++)
        {
            for($i = 1; $i <= $num; $i++)
            {
                $data[$k][$i] = $sheet->getCellByColumnAndRow($i,$k)->getCalculatedValue();
                // dump($data);die;
            }
        }
        // 重新排序
        $data = array_merge($data);
        //开始验证数据长度是否是正确的
        
        //开始组装保存的数据
        // dump($data);die;
        // 保存组装好的数据
        $result = [];
        foreach ($data as $key => $value)
        {
            $valie = array_merge($value);
            for($h = 0; $h < count($value);$h++)
            {
                // dump($value);die;
              $result[$key][$h]['field'] =  $field[$h];
              $result[$key][$h]['model'] =  $model[$h];
              $result[$key][$h]['value'] =  $valie[$h];
               
            }
          
        }
        // dump($model);die;
        // dump($filedata);
        // dump($data);
        // dump($field);
        // die;
        //开始保存数据库表 保存时候要把对应的form_id 写入到数据表fa_m_begin_process    type = 2
        
        // 开启事务
        
          Db::startTrans();
          
          try {
            //   数组去重 获取到当前是要插入到几个数据库中
             $ddu_model = array_merge(array_unique($model));
            //  dump($result);die;
              for($w = 0; $w < count($result); $w++)
              { 
                  $datae['type'] = 2;
                  $datae['form_id'] = $form_id;
                  $datae['company_id'] = $this->get_companyid();
                  $datae['upload_time'] = time();
                //   dump($datae);die;
                  $liucid = Db::name('m_begin_process')->insertGetId($datae);
                  
                //  当前插入的数据
                for($q = 0 ; $q < count($ddu_model); $q++)
                {
                    $datas =[];
                     foreach ($result[$w] as $rtk => $rtv)
                    {
                        if($rtv['model'] == $ddu_model[$q])
                        {
                            $datas[$rtv['field']] = $rtv['value'];
                        }
                    }
                   
                    $datas['liuc_id'] = $liucid;
                    // dump($datas);die;
                    $table_names = 's_form_'.$ddu_model[$q];
                    Db::name($table_names)->insert($datas);
                }
                  
              }
              Db::commit(); 
            }
              catch (Exception $e) {
              Db::rollback();
              return $e->getMessage();
            }
           $this->success('导入成功');
        
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
    
    //查看当前表单内容
    public function get_form_lists()
    {
         $form_id = $this->request->post('form_id','');
        //   dump($form_id);die;
        // dump($form_id);die;
        $i = 0;
        $list = Db::name('s_apply_structure')->where('id',$form_id)->find();
       
        if(!$list['list'])
        {
            $this->error('当前表单没有添加工序无法导入');
        }
        $file_list = json_decode($list['list'],true);
        // dump($file_list);die;
        $filedata = [];
        // $filed = 3;
        $field = [];
        $table_data_name = [];
        foreach($file_list as $key => $value)
        {
            // dump($file_list);die;
            //$value 是一个工序 
            // dump($value);die;
            //一个工序一张表  $value['key']对应的是这个表名  $value['list'] 表示字段
            $table_data_name[] = $value['key'];
            foreach($value['list'] as $k => $v)
            { 
                // $v  表示当前表中的字段  $v['key'] 表示当前字段名$v['label'] 表示当前提示的信息
                //第一行数据
                $filedata[$i]['label'] = $v['label'];
                $labels[] = $v['label'];
                //第二行数据
                $filedata[$i]['key']   = $value['key'].'_'.$v['key']; 
                $filedata[$i]['model'] = $value['key']; 
                $filedata[$i]['field'] = $v['key']; 
                $field[] = $v['key']; 
                $i++;
            } 
        }
        // dump($labels);die;
        $all_list = [];
        //把当前表单中的数据工序的表内容都提取出来
        foreach($table_data_name as $pk=>$pv)
        {
            $tablename = 's_form_'.$pv;
            $all_list[$pv] = Db::name($tablename)->select();
            
        }
        // dump($all_list);die;
        
        // dump($table_data_name);die;
        // 模板文件加入数据 
        // 把数据放入到模板数据中 
        //没有数据结构
        $table_data_referrer = [];
        //有数据结构
        $table_all_data_referrer = [];
       // 查看当前已经有几个流程在执行  几个流程表示当前有多少行(不包含)
        //type = 2  说明当前流程已完成  type = 1的时候 表示当前流程未完成 并且在当前字段中process 表示进行到第几个工序
        $countlist = Db::name('m_begin_process')->where('form_id',$form_id)->select();
        // dump($countlist);die;
       //表示当前多少列表
        $countcol = count($filedata);
        // 表示当前有几个表 就是有几道工序
        $counttable_num = count($table_data_name);
        // 用来存空的数据  如果空的数据长度等于系统长度就说明是没有值的
        $null_arr = [];
        // dump(count($countlist));die;
        foreach ($countlist as $kk => $vv)
        {
            
           
                foreach ($all_list as $kkk => $vvv)
                {
                    if(isset($vvv))
                    {
                         if(isset($vvv[$kk]))
                         {
                             $table_data_referrer[$kk][] = $vvv[$kk];
                         }else{
                             $table_data_referrer[$kk][] = [];
                         }
                    }
                    else
                    {
                         $table_data_referrer[$kk][] = [];
                    }
                   
                   
                }
        }  
      
 
       $merge_arr = [];
       foreach($table_data_referrer as $ffk => $ffv)
       {
           foreach ($ffv as $mmk => $mmv)
           {
            //   dump($mmv);die;
               if (isset($mmv['id'])) {
                   unset($mmv['id']);
               }
               if (isset($mmv['liuc_id'])) {
                   unset($mmv['liuc_id']);
               }
               $ffv[$mmk] = $mmv;
           } 
          $merge_arr[] = array_reduce($ffv, 'array_merge', array());
           
       } 
  
       $all_array=[];
       $rrt_data = [];
       for($j = 0; $j<count($field); $j++)
       {
           
           foreach($merge_arr as $mak => $mav)
           { 
              $is_c = array_key_exists($field[$j],$mav);
              if(!$is_c)
              {
                  $mav[$field[$j]] = '';
              } 
              $merge_arr[$mak] = $mav; 
           }
          
       } 
    //   dump($merge_arr);die;
       $merge_arrs =[];
       for($tg = 0; $tg < count($merge_arr);$tg++)
       {
            foreach ($merge_arr[$tg] as $mav)
         { 
            $merge_arrs[$tg][] =$mav;
         }
         
       } 
   
    //     for($tg = 0; $tg < count($merge_arr);$tg++)
    //   {
    //         foreach ($merge_arr[$tg]  as $mak => $mav)
    //      { 
    //         $merge_arrs[$tg][$labels['']] =$mav;
    //      }
         
    //   } 
       
    $merge_arrss =[];
      for($o = 0; $o < count($merge_arrs); $o++)
        {
            // dump($o);die;
            for($s = 0;$s <count($merge_arrs[$o]);$s++)
            {
                // dump($labels[$s]);die;
                // $merge_arrss[$o][$s]['title'] = $merge_arrs[$o][$s];
                $merge_arrss[$o]['index_'.$s] = $merge_arrs[$o][$s];
            }
            
         }
     //   组装结构给前端
       $xtable = [];
       for($y = 0; $y< count($labels);$y++)
       {
           $xtable[$y]['title'] = $labels[$y];
           $xtable[$y]['width'] = '200px';
           $xtable[$y]['dataIndex'] = 'index_'.$y;
        //   组织筛选结构
            $title_select = [];
            for($pi  = 0 ; $pi < count($merge_arrss);$pi++)
            {
                if ($merge_arrss[$pi]['index_'.$y] != '') {
                    
                $title_select_temp['text'] = $merge_arrss[$pi]['index_'.$y];
                $title_select_temp['value'] = $merge_arrss[$pi]['index_'.$y];
                // $title_select[] = json_encode($title_select_temp);
                $title_select[] = $title_select_temp;
                unset($title_select_temp);
                
                }
               
            } 
            $xtable[$y]['filters'] = array_values(array_unique($title_select,SORT_REGULAR));
            // dump($xtable[$y]['filters']);
            
            if($xtable[$y]['title'] == '数据id')
            {
                //去除数据id
                unset($xtable[$y]['filters']);
                $xtable[$y]['filters']['text'] = '当前数据id不进行筛选';
                $xtable[$y]['filters']['value'] = '当前数据id不进行筛选';
            } 
       }    
      $dataxs['table_th'] = $xtable;
      $dataxs['list'] = $merge_arrss; 
      $this->success('',$dataxs);
      
      
      
      
      
      
    }
    
    
    
    
   }
    
    
