<?php

namespace app\api\controller\v1;

use app\admin\model\help\Center as HelpCenterModel;

use think\Db;

class Help extends Base
{
    public function index()
    {
        $HelpCenterModel  = new HelpCenterModel; 
        $list = $HelpCenterModel->field('id,name')->select();
        $this->success('成功',$list);
    }
    public function get_list()
    {
        $id = $this->request->post('id',6);
        $list = HelpCenterModel::get($id);
        $this->success('成功',$list);
    }
}