<?php

namespace app\admin\model\apply;

use think\Model;


class Images extends Model
{

    

    

    // 表名
    protected $name = 's_images';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_status_text'
    ];
    

    
    public function getTypeStatusList()
    {
        return ['1' => __('Type_status 1'), '2' => __('Type_status 2'), '3' => __('Type_status 3')];
    }


    public function getTypeStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type_status']) ? $data['type_status'] : '');
        $list = $this->getTypeStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
