<?php

namespace app\admin\model\csmding;

use think\Model;


class Dduser extends Model
{

    

    

    // 表名
    protected $name = 'csmding_dduser';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'active_text',
        'isadmin_text',
        'isboss_text',
        'ishide_text',
        'issenior_text',
        'isleader_text',
        'status_text',
        'faisactivie_text',
        'faisfilladmin_text'
    ];
    

    
    public function getActiveList()
    {
        return ['true' => __('Active true'), 'false' => __('Active false')];
    }

    public function getIsadminList()
    {
        return ['true' => __('Isadmin true'), 'false' => __('Isadmin false')];
    }

    public function getIsbossList()
    {
        return ['true' => __('Isboss true'), 'false' => __('Isboss false')];
    }

    public function getIshideList()
    {
        return ['true' => __('Ishide true'), 'false' => __('Ishide false')];
    }

    public function getIsseniorList()
    {
        return ['true' => __('Issenior true'), 'false' => __('Issenior false')];
    }

    public function getIsleaderList()
    {
        return ['true' => __('True'), 'false' => __('False')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getFaisactivieList()
    {
        return ['true' => __('Faisactivie true'), 'false' => __('Faisactivie false')];
    }

    public function getFaisfilladminList()
    {
        return ['true' => __('Faisfilladmin true'), 'false' => __('Faisfilladmin false')];
    }


    public function getActiveTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['active']) ? $data['active'] : '');
        $list = $this->getActiveList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsadminTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['isadmin']) ? $data['isadmin'] : '');
        $list = $this->getIsadminList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsbossTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['isboss']) ? $data['isboss'] : '');
        $list = $this->getIsbossList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIshideTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['ishide']) ? $data['ishide'] : '');
        $list = $this->getIshideList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsseniorTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['issenior']) ? $data['issenior'] : '');
        $list = $this->getIsseniorList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsleaderTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['isleader']) ? $data['isleader'] : '');
        $list = $this->getIsleaderList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getFaisactivieTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['faisactivie']) ? $data['faisactivie'] : '');
        $list = $this->getFaisactivieList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getFaisfilladminTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['faisfilladmin']) ? $data['faisfilladmin'] : '');
        $list = $this->getFaisfilladminList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
