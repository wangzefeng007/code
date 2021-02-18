<?php
namespace app\admin\controller\csmding;

use app\common\controller\Backend;

/**
 * 钉钉员工管理
 *
 * @icon fa fa-circle-o
 */
class Dduser extends Backend
{

    /**
     * Dduser模型对象
     *
     * @var \app\admin\model\csmding\Dduser
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\csmding\Dduser();
        $this->view->assign("activeList", $this->model->getActiveList());
        $this->view->assign("isadminList", $this->model->getIsadminList());
        $this->view->assign("isbossList", $this->model->getIsbossList());
        $this->view->assign("ishideList", $this->model->getIshideList());
        $this->view->assign("isseniorList", $this->model->getIsseniorList());
        $this->view->assign("isleaderList", $this->model->getIsleaderList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("faisactivieList", $this->model->getFaisactivieList());
        $this->view->assign("faisfilladminList", $this->model->getFaisfilladminList());
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index()
    {
        if (\addons\csmding\library\Csmding::assertconfig() === false) {
            $this->error("您[钉钉对接套件]插件配置不完整,请到插件管理中配置", null, null, 300);
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
                ->where('status', '=', 'normal')
                ->order($sort, $order)
                ->count();

            $list = $this->model->where($where)
                ->where('status', '=', 'normal')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            \addons\csmding\library\CsmUtils::convertListColumn($list, 'faadmin_id', new \app\admin\model\Admin(), 'nickname');
            $list = collection($list)->toArray();
            $result = array(
                "total" => $total,
                "rows" => $list
            );

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 根据部门id获取人员ids,用于点击部门查询人员
     */
    public function getUsersByDepartsAjax()
    {
        $departids = $this->request->request('departids');
        $dao = new \app\admin\model\csmding\Dduser2depart();
        $list = $dao->where("csmding_dddepartment_id", 'in', $departids)->select();
        // echo $dao->getLastSql();
        $userids = '-1';
        foreach ($list as $item) {
            $userids .= ',' . $item->csmding_dduser_id;
        }
        $this->success('', null, array(
            'userids' => $userids
        ));
    }

    public function getdeparttreeAjax()
    {
        $dao = new \app\admin\model\csmding\Dddepartment();
        $list = $dao->where('status', '=', 'normal')
            ->order("order", 'desc')
            ->order("id", 'asc')
            ->select();
        $treedata = [];
        foreach ($list as $v) {
            $treedata[] = [
                'id' => $v->id,
                'parent' => $v->parentid ? $v->parentid : '#',
                'text' => $v->name
            ];
        }
        return json($treedata);
    }

    /**
     * 将人员加入到部门
     */
    public function selectuser($ids)
    {
        $dao = new \app\admin\model\csmding\Dduser();
        if ($this->request->isPost()) {
            $faadmin_id = $this->request->request('faadmin_id');

            $rr = $dao->where('id', 'in', $ids)
                ->where('faadmin_id', '=', $faadmin_id)
                ->where('status', '=', 'normal')
                ->find();
            if ($rr) {
                $this->error('该账号已经绑定' . $rr->name . ',绑定失败!');
            }

            $dao->where('id', 'in', $ids)->update([
                'faadmin_id' => $faadmin_id,
                'faisfilladmin'=>'true'//v1.0.5 修复钉钉用户绑定后,登录需要重新初始化账号的问题
            ]);
            $this->success();
        }
        $this->assign('id', $ids);
        $row = $dao->where('id', 'in', $ids)->find();
        $this->assign('row', $row);
        return $this->view->fetch();
    }
}
