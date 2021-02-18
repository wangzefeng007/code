<?php
namespace app\admin\controller\csmding;

use addons\csmding\library\dingtalk\DingTalkClient;
use addons\csmding\library\dingtalk\DingTalkConstant;
use addons\csmding\library\dingtalk\OapiMediaUploadRequest;
use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * Media
 *
 * @icon fa fa-circle-o
 */
class Msgmedia extends Backend
{

    /**
     * Msgmedia模型对象
     *
     * @var \app\admin\model\csmding\Msgmedia
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\csmding\Msgmedia();
    }

    public function add()
    {
        if(\addons\csmding\library\Csmding::assertconfig()===false){
            $this->error("您[钉钉对接套件]插件配置不完整,请到插件管理中配置",null,null,300);
        }
        
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            // add by chensm0517 处理mediaid
            if (true) {
                $fileLocation = '@' . ROOT_PATH . 'public' . $params['image'];

                $access_token = \addons\csmding\library\Csmding::getAccessToken();

                $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_POST, DingTalkConstant::$FORMAT_JSON);
                $req = new OapiMediaUploadRequest();
                $req->setType("image");
                $req->setMedia($fileLocation);
                $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/media/upload");
                if ($resp != null) {
                    $params['mediaid'] = $resp->media_id;
                }
                // var_dump($params['mediaid']);

                // $this->error('');
            }

            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    // 是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
