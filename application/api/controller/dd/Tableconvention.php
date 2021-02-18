<?php
namespace app\api\controller\dd;
 
use app\admin\model\diytable\Conventionalmother as ConventionalmotherModel;
use think\Db;
class Tableconvention extends Base
{
   //获取常规列表 差一个公司的id
   public function get_table_list()
   {
    $conventionalmotherModel = new   ConventionalmotherModel;
    $where['status'] = 1;
    $list = $conventionalmotherModel->field('id,table_key,table_value')->where($where)->select();
    $data['list'] = $list;
    $this->success('',$data);
   }
   public function get_form_structure()
   {
       $table_id = $this->request->post('id');
       $conventionalmotherModel = new   ConventionalmotherModel;
       $where['id'] = $table_id;
       $list =  $conventionalmotherModel->where($where)->field('id,table_key,table_field_value,formsub_list')->find();  
       $res = json_decode($this->js_unescape(urldecode(gzdecode(base64_decode($list['formsub_list'])))),true);
    //   dump($res);die;
       $datas = [];
       foreach ($res as $key=>$value)
       {
           $datas[$key]['lable'] = $value['value_all'];
           $datas[$key]['name'] = $value['key'];
           if($value['str_type'] == 'begin_date')
           {
                $datas[$key]['value'] = date("Y/m/d");
           }elseif ($value['str_type'] == 'user_name') {
                $datas[$key]['value'] = $this->get_uid_name();
           }else{
                $datas[$key]['value'] = null;
           }
       }
       $data['table_key'] = $list['table_key'];
       $data['id'] = $table_id;
       $data['list'] = $datas;
       $this->success('',$data);
   }
   //暂存  能改能删 
   //保存  一旦保存不能改不能删  
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
}
