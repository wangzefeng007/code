<?php

namespace app\api\controller\dd;

use think\Db;
use app\admin\model\designdata\Index as DesignModel;
class Design extends Base
{
    
    public function index()
    {
        $company_id = $this->request->post('company'); 
        $designModel = new DesignModel; 
        $list = $designModel->field('id,name')->where('company',$company_id)->where('deletetime','null')->select();
        $this->success('',$list); 
    }
    
    public function findOne()
    {
        $id = $this->request->post('id','');
        //查看第几个层级
        $type = $this->request->post('type','');
        if ($type < 1) {
            $this->error('数据错误 type 不能小于1');
        }
        $name = $this->request->post('name','');
        //如果type = 1时候说明 说明没有父级
        
        $designModel = new DesignModel; 
        $list = $designModel->where('id',$id)->where('deletetime','null')->find();
        $table_value = $list['table_value'];
        if(!$table_value)
        {
            $this->error('当前设计数据没有值');
        }
        $table_value = json_decode($table_value,true);
        $table = $table_value['list'];
        $da = []; 
        if($type == 1)
        {
            foreach ($table as $key=>$value) {
                $da[] = $value['index_1'];
        } 
        $da = array_values(array_unique($da));
        $d = [];
        foreach ($da as  $kk => $VV)
        {
            $d[$kk]['name'] = $VV;
        } 
        $all_arr['last'] = 1;
        $all_arr['data'] = $d;
        $this->success('',$all_arr);  
        
        }else 
        { 
            //如果大于1 就要顺便带上父名称  父1|父2|父3
            $xu = [];
           
            foreach ($table as $key => $value) {
                $all_v = $value; 
                $da[] = $value['index_'.$type];
                
                for($i = $type; $i > 0; $i--)
                {
                    $xu[$key][] =  $value['index_'.$i];
                }
                
            }
            // dump($all_v['index_9998']);die;
             if(isset($all_v['index_9998']))
             {
                 unset($all_v['index_9998']);
             }
             if(isset($all_v['index_9999']))
             {
                 unset($all_v['index_9999']);
             }
             //去除数据id
            unset($all_v['index_0']);
            // 判断是否还有下一层
            $last_name = '';
            $last_array = [];
            foreach ($all_v as $ki => $vi)
            {
                if($vi)
                {
                    $last_array[] = $vi;
                }
            }
            $last = 1;
            $_index = count($last_array);
            $last_index = $_index - 1;
            unset($last_array[$last_index]);
            $last_name = implode('',$last_array); 
            if(strlen($last_name) == strlen($name))
            {
                $last = 0;
            }
            $all_list = implode('',$all_v); 
            if(strlen($name) >= strlen($all_list))
            {
                 $this->error('当前已经是最后一层');
            }
            $re = [];
            foreach($xu as $ke => $val)
            {
                $val = array_reverse($val); 
                $st = '';
                for($i = 0;$i< count($val);$i++)
                {
                    $st .= $val[$i] . '|';
                }
                $st = rtrim($st , '|');
                // dump($st);die;
                $re[$ke] =  $st;
                
            }   
            $result = array_values(array_unique($re));
            $list = [];
            for($z = 0;$z < count($result); $z++)
            {
                $li = explode('|',$result[$z]);
                $count = count($li);
                $v = $li[$count-1];  
                unset($li[$count-1]);
                $k = implode('',$li);
                $list[$k][] = $v;  
            } 
             $tlist = $list[$name];
             
            $d = [];
            foreach ($tlist as  $kk => $VV)
            {
                $d[$kk]['name'] = $VV;
            } 
            $all_arr['last'] = $last;
            $all_arr['data'] = $d;
            $this->success('',$all_arr);   
            
        } 
    }

}