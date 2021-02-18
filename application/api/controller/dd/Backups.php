<?php
namespace app\api\controller\dd; 
use app\admin\model\backups\Dingding as DingdingModel;
class Backups extends Base

{
  public function set_backups()
  {
    $title   = $this->request->post('title','');
    $content = $this->request->post('content','');
    $ramark  = $this->request->post('ramark','');
    $dingdingModel = new DingdingModel;
    $dingdingModel->key_title = $title;
    $dingdingModel->value_con = $content;
    $dingdingModel->ramark = $ramark;
    $dingdingModel->save();
    $this->success('保存成功');
  } 
}
