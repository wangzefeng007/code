<?php

namespace app\admin\model\templatelink;

use think\Model;
use traits\model\SoftDelete;

class Design extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 's_template_designdata';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'cretatime_text'
    ];
    

    



    public function getCretatimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['cretatime']) ? $data['cretatime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCretatimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
