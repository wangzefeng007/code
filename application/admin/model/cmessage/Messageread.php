<?php

namespace app\admin\model\cmessage;

use think\Model;


class Messageread extends Model
{

    

    

    // 表名
    protected $name = 's_company_message_reads';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'is_read_text',
        'readtime_text'
    ];
    

    
    public function getIsReadList()
    {
        return ['0' => __('Is_read 0'), '1' => __('Is_read 1')];
    }


    public function getIsReadTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_read']) ? $data['is_read'] : '');
        $list = $this->getIsReadList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getReadtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['readtime']) ? $data['readtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setReadtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function scompanymessage()
    {
        return $this->belongsTo('app\admin\model\cmessage\Amessage', 'cmid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
