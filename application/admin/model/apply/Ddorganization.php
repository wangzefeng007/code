<?php

namespace app\admin\model\apply;

use think\Model;


class Ddorganization extends Model
{

    

    

    // 表名
    protected $name = 's_user_organization';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function scompany()
    {
        return $this->belongsTo('app\admin\model\Company', 'company_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
