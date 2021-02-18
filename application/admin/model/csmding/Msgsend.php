<?php

namespace app\admin\model\csmding;

use think\Model;


class Msgsend extends Model
{

    

    

    // 表名
    protected $name = 'csmding_msgsend';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'msgtype_text',
        'sendrange_text',
        'hassend_text',
        'sendtime_text'
    ];
    

    
    public function getMsgtypeList()
    {
        return ['oa' => __('Oa')];
    }

    public function getSendrangeList()
    {
        return ['all' => __('Sendrange all'), 'byuser' => __('Sendrange byuser'), 'bydepart' => __('Sendrange bydepart')];
    }

    public function getHassendList()
    {
        return ['Y' => __('Hassend y'), 'N' => __('Hassend n')];
    }


    public function getMsgtypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['msgtype']) ? $data['msgtype'] : '');
        $list = $this->getMsgtypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSendrangeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['sendrange']) ? $data['sendrange'] : '');
        $list = $this->getSendrangeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getHassendTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['hassend']) ? $data['hassend'] : '');
        $list = $this->getHassendList();
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
