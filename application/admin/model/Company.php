<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Company extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 's_company';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text',
        'types_text',
        'probationtime_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }

    public function getTypesList()
    {
        return ['0' => __('Types 0'), '1' => __('Types 1'), '2' => __('Types 2')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getTypesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['types']) ? $data['types'] : '');
        $list = $this->getTypesList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getProbationtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['probationtime']) ? $data['probationtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setProbationtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function user()
    {
        return $this->belongsTo('User', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
