<?php

namespace app\admin\model\csmding;

use think\Model;


class Dddepartment extends Model
{

    

    

    // 表名
    protected $name = 'csmding_dddepartment';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'createdeptgroup_text',
        'autoadduser_text',
        'depthiding_text',
        'outerdept_text',
        'status_text'
    ];
    

    
    public function getCreatedeptgroupList()
    {
        return ['true' => __('Createdeptgroup true'), 'false' => __('Createdeptgroup false')];
    }

    public function getAutoadduserList()
    {
        return ['true' => __('Autoadduser true'), 'false' => __('Autoadduser false')];
    }

    public function getDepthidingList()
    {
        return ['true' => __('Depthiding true'), 'false' => __('Depthiding false')];
    }

    public function getOuterdeptList()
    {
        return ['true' => __('Outerdept true'), 'false' => __('Outerdept false')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getCreatedeptgroupTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['createdeptgroup']) ? $data['createdeptgroup'] : '');
        $list = $this->getCreatedeptgroupList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getAutoadduserTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['autoadduser']) ? $data['autoadduser'] : '');
        $list = $this->getAutoadduserList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getDepthidingTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['depthiding']) ? $data['depthiding'] : '');
        $list = $this->getDepthidingList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getOuterdeptTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['outerdept']) ? $data['outerdept'] : '');
        $list = $this->getOuterdeptList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
