<?php
namespace app\admin\controller\csmding;

use addons\csmding\library\dingtalk\Body;
use addons\csmding\library\dingtalk\DingTalkClient;
use addons\csmding\library\dingtalk\DingTalkConstant;
use addons\csmding\library\dingtalk\Head;
use addons\csmding\library\dingtalk\Msg;
use addons\csmding\library\dingtalk\OA;
use addons\csmding\library\dingtalk\OapiMessageCorpconversationAsyncsendV2Request;
use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 消息推送
 *
 * @icon fa fa-circle-o
 */
class Msgsend extends Backend
{

    /**
     * Msgsend模型对象
     *
     * @var \app\admin\model\csmding\Msgsend
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\csmding\Msgsend();
        $this->view->assign("msgtypeList", $this->model->getMsgtypeList());
        $this->view->assign("sendrangeList", $this->model->getSendrangeList());
        $this->view->assign("hassendList", $this->model->getHassendList());
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index()
    {
        if(\addons\csmding\library\Csmding::assertconfig()===false){
            $this->error("您[钉钉对接套件]插件配置不完整,请到插件管理中配置",null,null,300);
        }
        
        // 设置过滤方法
        $this->request->filter([
            'strip_tags'
        ]);
        if ($this->request->isAjax()) {
            // 如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list ($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model->alias('t')
                ->join('csmding_msgsendtask a', "t.msgtaskid=a.id", "left")
                ->field("t.*,a.msgtaskerrcode,a.msgtaskerrmsg")
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array(
                "total" => $total,
                "rows" => $list
            );

            return json($result);
        }
        return $this->view->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $params['name'] = $params['oabodytitle']; // add by chensm
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
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (! in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['name'] = $params['oabodytitle']; // add by chensm
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    // 是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    // $params['accountkeyrule'] = strtolower($params['accountkeyrule']);
                    $result = $row->allowField(true)->save($params);
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
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    public function sendmsg($ids = null)
    {
        $id = $ids;
        $row = $this->model->where('id', '=', $id)->find();
        if ($row == null) {
            $this->error('id不存在');
        }

        $media = null;

        $config = get_addon_config("csmding");
        $ddh5agentid = (int) $config["ddh5agentid"];
        $userid_list = $this->getuserid_list($row->csmding_dduser_ids);
        $dept_id_list = $this->getdept_id_list($row->csmding_dddepartment_ids);

        $msgtaskid = null;
        $msgtaskerrcode = null;
        $msgtaskerrmsg = null;
        $osmsg = ''; // TODO
                     // 推送钉钉消息
        if (true) {
            try {
                $fileLocation = '@' . ROOT_PATH . 'public' . $row->oabodyimage;
                $media = \addons\csmding\library\Csmding::getMediaByFile($fileLocation);

                $access_token = \addons\csmding\library\Csmding::getAccessToken();
                $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_POST, DingTalkConstant::$FORMAT_JSON);
                $req = new OapiMessageCorpconversationAsyncsendV2Request();
                $req->setAgentId($ddh5agentid);
                switch ($row->sendrange) {
                    case "all":
                        $req->setToAllUser(true);
                        break;
                    case "byuser":
                        $req->setUseridList($userid_list);
                        break;
                    case "bydepart":
                        $req->setDeptIdList($dept_id_list);
                        break;
                }
                $msg = new Msg();
                $oa = new OA();
                $body = new Body();
                $body->image = $media;
                $body->content = $row->oabodycontent;
                $oabodyform_json = json_decode($row->oabodyform_json, true);
                //trace($oabodyform_json);
                // $forms = [];
                // foreach($oabodyform_json as $key=>$item){
                // $form = new Form();
                // $form->value=$item;
                // $form->key=$key;
                // $forms[] = $form;
                // }
                // $body->form = $forms;
                $body->title = $row->oabodytitle;
                $oa->body = $body;
                $head = new Head();
                $head->bgcolor = $row->oaheadbgcolor;
                $head->text = $row->oaheadtext;
                $oa->head = $head;
                $oa->pc_message_url = $row->oamessageurl;
                $oa->message_url = $row->oamessageurl;
                $msg->oa = $oa;
                $msg->msgtype = 'oa';
                $req->setMsg($msg);

                $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2");
                trace($resp);
                if ($resp != null) {
                    $msgtaskerrcode = $resp->errcode;
                    if ($resp->errcode == 1) {
                        $msgtaskerrmsg = $resp->errmsg;
                    }
                    $msgtaskid = $resp->task_id;
                }
            } catch (\Exception $e) {
                $msgtaskerrcode = '1';
                $msgtaskerrmsg = $e->getMessage();
            }
        }

        // 保存数据
        if (true) {
            $dao = new \app\admin\model\csmding\Msgsendtask();
            $param = [
                'csmding_msgsend_id' => $id,
                'msgtype' => $row->msgtype,
                'agent_id' => $ddh5agentid,
                'userid_list' => $userid_list,
                'dept_id_list' => $dept_id_list,
                'to_all_user' => ($row->sendrange == 'all') ? 'true' : 'false',
                'msg' => $osmsg,
                'msgtaskid' => $msgtaskid,
                'msgtaskerrcode' => $msgtaskerrcode,
                'msgtaskerrmsg' => $msgtaskerrmsg,
                'sendtime' => time(),
                'createtime' => time()
            ];
            $result = $dao->create($param);

            $this->model->where('id', '=', $id)->update([
                'msgtaskid' => $result->id,
                'hassend' => 'Y'
            ]);
        }

        if ($msgtaskerrcode == '0') {
            $this->success();
        } else {
            $this->error('推送失败,失败原因:' . $msgtaskerrmsg);
        }
    }

    private function getuserid_list($csmding_dduser_ids)
    {
        $dao = new \app\admin\model\csmding\Dduser();
        $ll = $dao->where('id', 'in', $csmding_dduser_ids)
            ->where('status', '=', 'normal')
            ->select();
        $ids = [];
        foreach ($ll as $item) {
            $ids[] = $item->userid;
        }
        $sr = implode($ids, ',');

        return $sr;
    }

    private function getdept_id_list($csmding_dddepartment_ids)
    {
        $dao = new \app\admin\model\csmding\Dddepartment();
        $ll = $dao->where('id', 'in', $csmding_dddepartment_ids)
            ->where('status', '=', 'normal')
            ->select();
        $ids = [];
        foreach ($ll as $item) {
            $ids[] = $item->departmentid;
        }
        $sr = implode($ids, ',');

        return $sr;
    }
}
