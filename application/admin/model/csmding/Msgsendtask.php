<?php

namespace app\admin\model\csmding;

use think\Model;


class Msgsendtask extends Model
{

    

    

    // 表名
    protected $name = 'csmding_msgsendtask';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'msgtype_text',
        'msgtaskstatus_text',
        'sendtime_text'
    ];
    

    
    public function getMsgtypeList()
    {
        return ['oa' => __('Oa')];
    }

    public function getMsgtaskstatusList()
    {
        return ['0' => __('Msgtaskstatus 0'), '1' => __('Msgtaskstatus 1'), '2' => __('Msgtaskstatus 2')];
    }


    public function getMsgtypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['msgtype']) ? $data['msgtype'] : '');
        $list = $this->getMsgtypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getMsgtaskstatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['msgtaskstatus']) ? $data['msgtaskstatus'] : '');
        $list = $this->getMsgtaskstatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSendtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['sendtime']) ? $data['sendtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setSendtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
