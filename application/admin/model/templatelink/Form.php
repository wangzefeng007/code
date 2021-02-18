<?php

namespace app\admin\model\templatelink;

use think\Model;
use traits\model\SoftDelete;

class Form extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 's_templatelinkform';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    







    public function stemplateall()
    {
        return $this->belongsTo('app\admin\model\STemplateAll', 'template_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
