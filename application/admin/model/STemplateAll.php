<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class STemplateAll extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 's_template_all';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    







    public function user()
    {
        return $this->belongsTo('User', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
