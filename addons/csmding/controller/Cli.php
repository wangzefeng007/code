<?php
namespace addons\csmding\controller;

use think\addons\Controller;
use addons\csmding\library\Csmding;
use addons\csmding\library\dingtalk\DingTalkClient;
use addons\csmding\library\dingtalk\DingTalkConstant;
use addons\csmding\library\dingtalk\OapiDepartmentListRequest;
use addons\csmding\library\dingtalk\OapiDepartmentGetRequest;
use addons\csmding\library\dingtalk\OapiUserListbypageRequest;
use fast\Random;
use addons\csmding\library\dingtalk\OapiUserGetRequest;
use addons\csmding\library\dingtalk\OapiAuthScopesRequest;

class Cli extends Controller
{

    private $maxQueryDepartUserCount = 100;

    /**
     * http://127.0.0.1/fastadmin_plugin_csmmeet/public/addons/csmding/cli/index
     * http://fatest.163fan.com/addons/csmding/cli/index
     * 1.钉钉部门同步到本地 clidepart
     * > 判定是否同步开通部门(由于需要把管理员id回写到dd表中)
     * > 创建或更新钉钉部门
     * > 钉钉员工同步到本地 clideuser
     * > 判定是否同步开通员工
     * > 创建或更新钉钉员工
     * 3.不是本地标记的员工删除 cleardduser
     * 4.不是本地标记的部门删除 cleardddepart
     * 5.同步dd部门到csmadmin部门 syncCsmDepart
     * 6.同步dd员工到faadmin syncFaAdmin
     * 7.同步dd任职到csmadmin任职
     */
    public function index()
    {
        $access_token = Csmding::getAccessToken();
        // var_dump($access_token);
        $remoteversion = time();
        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET, DingTalkConstant::$FORMAT_JSON);
        
        $scoperesp = $this->cliauthScope($c, $access_token);
        //var_dump($scoperesp->auth_org_scopes->authed_dept);
        //根据授权部门同步
        if(!in_array(1, $scoperesp->auth_org_scopes->authed_dept)){
            $this->_asynrootdepart($remoteversion);
        }
        foreach($scoperesp->auth_org_scopes->authed_dept as $departid){
            $this->clidepart($c, $access_token,$departid, $remoteversion);
        }
        //根据授权部门同步
        foreach($scoperesp->auth_org_scopes->authed_user as $userid){
            $this->cliuserByUserid($c, $access_token,$userid, $remoteversion);
        }
        
        $this->cleardduser($remoteversion);
        $this->cleardddepart($remoteversion);

        if ($this->isdduseropendepart()) {
            $this->syncCsmDepart();
        }
        if ($this->isdduseropenadmin()) {
            $this->syncFaAdmin();
        }
        if ($this->isdduseropendepart()) {
            $this->syncUser2Depart();
        }
        static::p('执行结束');
    }

    private static function pp(&$param, $resp, $fieldname, $paramfieldname = null)
    {
        if ($paramfieldname == null) {
            $paramfieldname = strtolower($fieldname);
        }
        try {
            $vv = $resp->$fieldname;
            if (is_bool($vv)) {
                if ($vv === true) {
                    $vv = "true";
                } else {
                    $vv = "false";
                }
            }
            $param[$paramfieldname] = $vv;
        } catch (\Exception $e) {
            return null;
        }
    }

    // 钉钉通讯录同步开通管理员
    private function isdduseropenadmin()
    {
        $config = get_addon_config("csmding");
        $isdduseropenadmin = $config["isdduseropenadmin"];
        if ($isdduseropenadmin == 'Y') {
            return true;
        } else {
            return false;
        }
    }

    // 钉钉通讯录同步到本地组织架构(需要安装插件[后台管理账号增强])
    private function isdduseropendepart()
    {
        $plugin = get_addon_info('csmadmin');
        if ($plugin && $plugin['state']) {
            $config = get_addon_config("csmding");
            $isdduseropenadmin = $config["isdduseropendepart"];
            if ($isdduseropenadmin == 'Y') {

                return true;
            } else {
                return false;
            }
        } else {
            // 没有安装插件,则不同步
            return false;
        }
    }

    private function syncUser2Depart()
    {
        $dddao = new \app\admin\model\csmding\Dduser2depart();
        $csmadmindao = new \app\admin\model\csmadmin\Depart2user();
        if (true) {
            $ll = $csmadmindao->alias('csmadmin')
                ->join('csmadmin_depart depart', "csmadmin.csmadmin_depart_id=depart.id AND depart.`status`='normal'")
                ->join('admin admin', "csmadmin.faadmin_id=admin.id AND admin.`status`='normal'")
                ->join('csmding_dduser2depart ding', "csmadmin.faadmin_id=admin.id and csmadmin.csmadmin_depart_id=depart.id", 'left')
                ->where("csmadmin.status='normal'")
                ->where("ding.id is null")
                ->field('csmadmin.id')
                ->select();
            $ids = [];
            foreach ($ll as $item) {
                $ids[] = $item->id;
            }
            $csmadmindao->where('id', 'in', $ids)->delete();

            $ll = $dddao->alias('ding')
                ->join('csmding_dddepartment dddepart', "ding.csmding_dddepartment_id=dddepart.id  AND dddepart.`status`='normal'")
                ->join('csmding_dduser dduser', "ding.csmding_dduser_id=dduser.id AND dduser.`status`='normal'")
                ->join('csmadmin_depart2user csmadmin', "csmadmin.faadmin_id=dduser.faadmin_id and csmadmin.csmadmin_depart_id=dddepart.csmadmin_depart_id and csmadmin.status='normal'", 'left')
                ->where("csmadmin.id is null")
                ->field('dduser.faadmin_id,dddepart.csmadmin_depart_id')
                ->select();
            foreach ($ll as $item) {
                $csmadmindao->create([
                    'csmadmin_depart_id' => $item->csmadmin_depart_id,
                    'faadmin_id' => $item->faadmin_id
                ]);
            }
        }
    }

    // 同步开通Fadmin
    private function syncFaAdmin()
    {
        $dddao = new \app\admin\model\csmding\Dduser();
        $dao = new \app\admin\model\Admin();
        if (true) {
            // 禁用帐号
            $ll = $dddao->where('status', '=', 'hidden')->select();
            $faadminids = [];
            foreach ($ll as $item) {
                $faadminids[] = $item->faadmin_id;
            }
            $dao->where("id", 'in', $faadminids)->update([
                'status' => 'hidden',
                'updatetime' => time()
            ]);
        }

        // 同步新增或修改帐号
        if (true) {
            $ll = $dddao->alias('t')
                ->join('admin a', "t.faadmin_id=a.id and a.status='normal'", 'left')
                ->where('t.status', '=', 'normal')
                ->field('a.id a_id,t.userid ,t.name,t.email,t.faadmin_id,t.id')
                ->select();

            foreach ($ll as $item) {
                if ($item->a_id != null) {
                    $faadminid = $item->faadmin_id;
                    $dao->where('id', '=', $faadminid)->update([
                        'nickname' => $item['name'],
                        'email' => $item['email'],
                        'updatetime' => time()
                    ]);
                } else {
                    //$username = substr($item['userid'], - 20);
                    $params = [
                        'username' => time().Random::alpha(4),
                        'nickname' => $item['name'],
                        'salt' => Random::alnum(),
                        'password' => time(),
                        'email' => $item['email'],
                        'loginfailure' => 0,
                        'avatar' => '/assets/img/avatar.png',
                        'createtime' => time()
                    ];
                    // var_dump($params);
                    $result = $dao->create($params);
                    // 回写dd表
                    $adminid = $result->id;
                    $dddao->where('id', '=', $item->id)->update([
                        'faadmin_id' => $adminid,
                        'updatetime' => time()
                    ]);

                    // 默认角色
                    $config = get_addon_config("csmding");
                    $ddadmindefaultroles = $config['ddadmindefaultroles'];
                    // FA 模式
                    if ($ddadmindefaultroles != null && $ddadmindefaultroles != '') {
                        $ddadmindefaultrolesarr = explode(",", $ddadmindefaultroles);
                        // 按照fa的授权模式
                        $dataset = [];
                        foreach ($ddadmindefaultrolesarr as $value) {
                            $dataset[] = [
                                'uid' => $adminid,
                                'group_id' => $value
                            ];
                        }
                        $groupaccessdao = new \app\admin\model\AuthGroupAccess();
                        $groupaccessdao->saveAll($dataset);
                    }
                    // csmadmin 模式
                    $plugin = get_addon_info('csmadmin');
                    if ($plugin && $plugin['state']) {
                        if ($ddadmindefaultroles != null && $ddadmindefaultroles != '') {
                            $ddadmindefaultrolesarr = explode(",", $ddadmindefaultroles);

                            $dataset = [];
                            foreach ($ddadmindefaultrolesarr as $value) {
                                $dataset[] = [
                                    'faadmin_id' => $adminid,
                                    'auth_group_id' => $value
                                ];
                            }
                            $group2admindao = new \app\admin\model\csmadmin\Group2admin();
                            $group2admindao->saveAll($dataset);
                        }
                    }
                }
            }
        }
    }

    // 同步开通csmadmin部门
    private function syncCsmDepart()
    {
        $dddao = new \app\admin\model\csmding\Dddepartment();
        $dao = new \app\admin\model\csmadmin\Depart();

        // 同步删除部门(to csmadmin_depart)
        if (true) {
            $ll = $dddao->where('status', '=', 'hidden')->select();
            $csmadmin_departids = [];
            foreach ($ll as $item) {
                $csmadmin_departids[] = $item->csmadmin_depart_id;
            }
            $dao->where("id", 'in', $csmadmin_departids)->delete();
        }

        // 同步部门(to csmadmin_depart)
        if (true) {
            $ll = $dddao->alias('t')
                ->join('csmadmin_depart a', "t.csmadmin_depart_id=a.id and a.status='normal'", 'left')
                ->where('t.status', '=', 'normal')
                ->field('t.csmadmin_depart_id,t.name,t.order,t.id,a.id a_id')
                ->select();

            foreach ($ll as $item) {
                if ($item->a_id != null) {
                    $dao->where('id', '=', $item->csmadmin_depart_id)->update([
                        'name' => $item['name'],
                        'weigh' => $item['order'],
                        'updatetime' => time()
                    ]);
                    static::p("更新到csmadmin depart:" . $item['name']);
                } else {
                    $params = [
                        'name' => $item['name'],
                        'weigh' => $item['order'],
                        'root_id' => 1,
                        'fromsys' => 'dd',
                        'fromuuid' => $item['id'],
                        'createtime' => time()
                    ];
                    $result = $dao->create($params);
                    $csmadmin_depart_id = $result->id;
                    static::p("新增到csmadmin depart:" . $item['name']);

                    // 将部门id回写dd的部门表
                    $dddao->where('id', '=', $item->id)->update([
                        'csmadmin_depart_id' => $csmadmin_depart_id,
                        'updatetime' => time()
                    ]);
                }
            }
        }

        // 更新父节点
        $ll = $dddao->alias('t')
            ->join('csmding_dddepartment a', 't.parentid=a.id', 'left')
            ->field('t.csmadmin_depart_id,a.csmadmin_depart_id parent_csmadmin_depart_id')
            ->where('t.status', '=', 'normal')
            ->select();

        foreach ($ll as $item) {
            $root_id = null;
            $parent_id = null;
            // 如果没有父节点,则当前节点为根节点;如果有父节点,则root为父节点的根节点
            if ($item->parent_csmadmin_depart_id == null) {
                $parent_id = 0;
                $root_id = $item->csmadmin_depart_id;
            } else {
                $parent_id = $item->parent_csmadmin_depart_id;
                $row = $dao->where('id', '=', $item->parent_csmadmin_depart_id)
                    ->where('status', '=', 'normal')
                    ->find();
                if ($row) {
                    $root_id = $row->root_id;
                }
            }
            $dao->where('id', '=', $item->csmadmin_depart_id)->update([
                'parent_id' => $parent_id,
                'root_id' => $root_id,
                'updatetime' => time()
            ]);
        }
        $ll = $dao->where('status', '=', 'normal')->select();
        $this->setArrRootid($ll);
        foreach ($ll as &$item) {
            $dao->where('id', '=', $item->id)->update([
                'root_id' => $item->root_id
            ]);
        }
    }

    /**
     * 遍历,计算父节点
     * array(object->id,parent_id,root_id)
     */
    private function setArrRootid(&$arr)
    {
        $arr2 = [];
        foreach ($arr as &$item) {
            $arr2['I' . $item->id] = $item;
        }
        while (true) {
            $hasnullroot = false;
            foreach ($arr as &$item) {
                if ($item->root_id == null) {
                    $hasnullroot = true;
                    $parentnode = $arr2['I' . $item->parent_id];
                    $parent_id = $parentnode['parent_id'];
                    $root_id = $parentnode['root_id'];
                    $item->parent_id = $parent_id;
                    if ($root_id != null) {
                        $item->root_id = $root_id;
                    }
                }
            }
            if ($hasnullroot === false) {
                break;
            }
        }
    }

    /**
     * 删除同步的员工
     */
    private function cleardduser($remoteversion)
    {
        $dao = new \app\admin\model\csmding\Dduser();
        // 同步删除任职关系
        if (true) {
            $ll = $dao->where('remoteversion', '<>', $remoteversion)
                ->where('status', '=', 'normal')
                ->select();
            $faadminids = [];
            foreach ($ll as $item) {
                $faadminids[] = $item->id;
            }

            $dduser2departdao = new \app\admin\model\csmding\Dduser2depart();
            $dduser2departdao->where('csmding_dduser_id', 'in', $faadminids)->delete();
            static::p("删除任职关系:" . implode($faadminids, ','));
        }
        // 同步dduser数据
        $ll = $dao->where('remoteversion', '<>', $remoteversion)
            ->where('status', '=', 'normal')
            ->select();
        foreach ($ll as $item) {
            static::p("删除钉钉员工dduser:" . $item->userid);
        }
        $dao->where('remoteversion', '<>', $remoteversion)
            ->where('status', '=', 'normal')
            ->update([
            'status' => 'hidden',
            'updatetime' => time()
        ]);
    }

    /**
     * 删除同步的部门
     */
    private function cleardddepart($remoteversion)
    {
        $dao = new \app\admin\model\csmding\Dddepartment();

        $ll = $dao->where('remoteversion', '<>', $remoteversion)
            ->where('status', '=', 'normal')
            ->select();
        foreach ($ll as $item) {
            static::p("删除钉钉部门dduser:" . $item->name);
        }
        $dao->where('remoteversion', '<>', $remoteversion)
            ->where('status', '=', 'normal')
            ->update([
            'status' => 'hidden',
            'updatetime' => time()
        ]);
    }
    
    private function cliauthScope($c,$access_token){
        $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_GET , DingTalkConstant::$FORMAT_JSON);
        $req = new OapiAuthScopesRequest();
        $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/auth/scopes");
        return $resp;
    }

    
    private function cliuserByUserid($c, $access_token, $userid, $remoteversion){
        $req = new OapiUserGetRequest();
        $req->setUserid("040655446571753427");
        $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/user/get");
        if ($resp != null) {
            $dao = new \app\admin\model\csmding\Dduser();
            $dao2 = new \app\admin\model\csmding\Dduser2depart();
            $dddepart = new \app\admin\model\csmding\Dddepartment();
            $resp->department = [1];//部门默认挂在跟节点
            $this->_asynuser($remoteversion, $resp,$dao,$dao2,$dddepart);
        }
        
    }
    /**
     * cli从钉钉同步员工到本地
     */
    private function cliuser($c, $access_token, $dddepartid, $remoteversion, $offset)
    {
        $req = new OapiUserListbypageRequest();
        $req->setDepartmentId($dddepartid);
        $req->setOffset($offset);
        $req->setSize($this->maxQueryDepartUserCount);
        $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/user/listbypage");

        if ($resp != null) {
            $dao = new \app\admin\model\csmding\Dduser();
            $dao2 = new \app\admin\model\csmding\Dduser2depart();
            $dddepart = new \app\admin\model\csmding\Dddepartment();
            foreach ($resp->userlist as $item) {
                $this->_asynuser($remoteversion, $item,$dao,$dao2,$dddepart);
//                 $row = $dao->where('userid', '=', $item->userid)
//                     ->where('status', '=', 'normal')
//                     ->find();
//                 $param = [
//                     'remoteversion' => $remoteversion
//                 ];
//                 static::pp($param, $item, 'userid');
//                 static::pp($param, $item, 'unionid');
//                 static::pp($param, $item, 'mobile');
//                 static::pp($param, $item, 'tel');
//                 static::pp($param, $item, 'workPlace');
//                 static::pp($param, $item, 'remark');
//                 static::pp($param, $item, 'order');
//                 static::pp($param, $item, 'isAdmin');
//                 static::pp($param, $item, 'isBoss');
//                 static::pp($param, $item, 'isHide');
//                 static::pp($param, $item, 'isLeader');
//                 static::pp($param, $item, 'name');
//                 static::pp($param, $item, 'active');
//                 static::pp($param, $item, 'position');
//                 static::pp($param, $item, 'email');
//                 static::pp($param, $item, 'avatar');
//                 static::pp($param, $item, 'jobnumber');
//                 static::pp($param, $item, 'extattr');

//                 if (! isset($param['email']) || $param['email'] == null || $param['email'] == '') {
//                     $param['email'] = $param['userid'];
//                 }

//                 $userid = null;
//                 // 更新部门清单
//                 if ($row != null) {
//                     $param['updatetime'] = time();
//                     $dao->where("id", "=", $row->id)->update($param);
//                     static::p("更新员工:" . $item->userid);
//                     $userid = $row->id;
//                 } else {
//                     $param['createtime'] = time();
//                     $dao->create($param);
//                     static::p("创建员工:" . $item->userid);
//                     $userid = $dao->getLastInsID();
//                 }

//                 // 处理人员任职部门关系
//                 if (true) {
//                     $departments = $item->department;
//                     $user2departs = $dao2->where('csmding_dduser_id', '=', $userid)->select();
//                     $dbuser2departs = [];
//                     foreach ($user2departs as $departrow) {
//                         $dbuser2departs[] = $departrow->csmding_dddepartment_id;
//                     }
//                     // 删除权限组
//                     if (true) {
//                         $delll = array_unique(array_diff($dbuser2departs, $departments));
//                         foreach ($delll as $iid) {
//                             $dao2->where("csmding_dduser_id", '=', $userid)
//                                 ->where('csmding_dddepartment_id', '=', $iid)
//                                 ->delete();
//                             static::p("删除权限组:userid-{$userid}/depart-{$iid}");
//                         }
//                     }
//                     // 新增权限组
//                     if (true) {
//                         $addll = array_unique(array_diff($departments, $dbuser2departs));

//                         foreach ($addll as $iid) {
//                             $dddepartrow = $dddepart->where('departmentid', '=', $iid)
//                                 ->where('status', '=', 'normal')
//                                 ->find();
//                             if ($dddepartrow != null) {
//                                 $param = [
//                                     'csmding_dduser_id' => $userid,
//                                     'csmding_dddepartment_id' => $dddepartrow->id
//                                 ];
//                                 $dao2->create($param);
//                                 static::p("创建权限组:userid-{$userid}/depart-" . $dddepartrow->id);
//                             }
//                         }
//                     }
//                 }
            }

            if ($resp->hasMore != null && $resp->hasMore === true) {
                $this->cliuser($c, $access_token, $remoteversion, $offset + $this->maxQueryDepartUserCount);
            }
        }
    }
    
    private function _asynuser($remoteversion,$item,$dao,$dao2,$dddepart){
//         $dao = new \app\admin\model\csmding\Dduser();
//         $dao2 = new \app\admin\model\csmding\Dduser2depart();
//         $dddepart = new \app\admin\model\csmding\Dddepartment();        
        
        $row = $dao->where('userid', '=', $item->userid)
        ->where('status', '=', 'normal')
        ->find();
        $param = [
            'remoteversion' => $remoteversion
        ];
        static::pp($param, $item, 'userid');
        static::pp($param, $item, 'unionid');
        static::pp($param, $item, 'mobile');
        static::pp($param, $item, 'tel');
        static::pp($param, $item, 'workPlace');
        static::pp($param, $item, 'remark');
        static::pp($param, $item, 'order');
        static::pp($param, $item, 'isAdmin');
        static::pp($param, $item, 'isBoss');
        static::pp($param, $item, 'isHide');
        static::pp($param, $item, 'isLeader');
        static::pp($param, $item, 'name');
        static::pp($param, $item, 'active');
        static::pp($param, $item, 'position');
        static::pp($param, $item, 'email');
        static::pp($param, $item, 'avatar');
        static::pp($param, $item, 'jobnumber');
        static::pp($param, $item, 'extattr');
        
        if (! isset($param['email']) || $param['email'] == null || $param['email'] == '') {
            $param['email'] = $param['userid'];
        }
        
        $userid = null;
        // 更新部门清单
        if ($row != null) {
            $param['updatetime'] = time();
            $dao->where("id", "=", $row->id)->update($param);
            static::p("更新员工:" . $item->userid);
            $userid = $row->id;
        } else {
            $param['createtime'] = time();
            $dao->create($param);
            static::p("创建员工:" . $item->userid);
            $userid = $dao->getLastInsID();
        }
        
        // 处理人员任职部门关系
        if (true) {
            $departments = $item->department;
            $user2departs = $dao2->where('csmding_dduser_id', '=', $userid)->select();
            $dbuser2departs = [];
            foreach ($user2departs as $departrow) {
                $dbuser2departs[] = $departrow->csmding_dddepartment_id;
            }
            // 删除权限组
            if (true) {
                $delll = array_unique(array_diff($dbuser2departs, $departments));
                foreach ($delll as $iid) {
                    $dao2->where("csmding_dduser_id", '=', $userid)
                    ->where('csmding_dddepartment_id', '=', $iid)
                    ->delete();
                    static::p("删除权限组:userid-{$userid}/depart-{$iid}");
                }
            }
            // 新增权限组
            if (true) {
                $addll = array_unique(array_diff($departments, $dbuser2departs));
                
                foreach ($addll as $iid) {
//                     var_dump($iid);
//                     var_dump($dddepart);
                    $dddepartrow = $dddepart->where('departmentid', '=', $iid)
                    ->where('status', '=', 'normal')
                    ->find();
                    if ($dddepartrow != null) {
                        $param = [
                            'csmding_dduser_id' => $userid,
                            'csmding_dddepartment_id' => $dddepartrow->id
                        ];
                        $dao2->create($param);
                        static::p("创建权限组:userid-{$userid}/depart-" . $dddepartrow->id);
                    }
                }
            }
        }
        
    }

    //部分授权会存在没有跟节点,为了显示树形结构,需要人工补全跟节点
    private function _asynrootdepart($remoteversion){
        $rootdepartid = 1;
        $dao = new \app\admin\model\csmding\Dddepartment();
        $row = $dao->where('departmentid','=',$rootdepartid)->where('status','=','normal')->find();
        if(!$row){
            $dao->create([
                'departmentid'=>$rootdepartid,
                'name'=>'根节点',
                'parentid'=>0,
                'order'=>'0',
                'status'=>'normal',
                'createtime'=>time(),
            ]);
        }
    }
    
    /**
     * cli从钉钉同步部门到本地
     */
    private function clidepart($c, $access_token,$departid, $remoteversion)
    {
        
        $req = new OapiDepartmentListRequest();
        $req->setId($departid);
        $resp = $c->execute($req, $access_token, "https://oapi.dingtalk.com/department/list");
        //var_dump($resp);
        if($resp==null || $resp->errcode!='0'){
            $this->error( $resp->errmsg,null,null,60);
        }
        
        $reqd = new OapiDepartmentGetRequest();
        $dao = new \app\admin\model\csmding\Dddepartment();
        //由于当前跟节点在list中不返回,需要额外调用
        $this->_asyndepart($c,$reqd,$remoteversion,$access_token, $departid,$dao);

        if ($resp != null && $resp->errcode == '0') {
            foreach ($resp->department as $item) {
                $this->_asyndepart($c,$reqd,$remoteversion,$access_token, $item->id,$dao);
//                 $row = $dao->where('departmentid', '=', $item->id)
//                     ->where('status', '=', 'normal')
//                     ->find();
//                 $reqd->setId($item->id);
//                 $respd = $c->execute($reqd, $access_token, "https://oapi.dingtalk.com/department/get");
//                 if ($respd == null) {
//                     continue;
//                 }
//                 $param = [
//                     'remoteversion' => $remoteversion
//                 ];
//                 static::pp($param, $respd, 'name');
//                 static::pp($param, $respd, 'ext');
//                 static::pp($param, $respd, 'userPermits');
//                 static::pp($param, $respd, 'userPerimits');
//                 static::pp($param, $respd, 'orgDeptOwner');
//                 static::pp($param, $respd, 'outerDept');
//                 static::pp($param, $respd, 'deptManagerUseridList');
//                 static::pp($param, $respd, 'groupContainSubDept');
//                 static::pp($param, $respd, 'outerPermitUsers');
//                 static::pp($param, $respd, 'outerPermitDepts');
//                 static::pp($param, $respd, 'deptPerimits');
//                 static::pp($param, $respd, 'createDeptGroup');
//                 static::pp($param, $respd, 'deptGroupChatId');
//                 static::pp($param, $respd, 'id', 'departmentid');
//                 static::pp($param, $respd, 'autoAddUser');
//                 static::pp($param, $respd, 'deptHiding');
//                 static::pp($param, $respd, 'deptPermits');
//                 static::pp($param, $respd, 'order');
//                 static::pp($param, $respd, 'parentid');

//                 // 处理父节点
//                 if (isset($param['parentid'])) {
//                     $rowparent = $dao->where('departmentid', '=', $param['parentid'])
//                         ->where('status', '=', 'normal')
//                         ->find();
//                     if ($rowparent != null) {
//                         $param['parentid'] = $rowparent->id;
//                     }
//                 }

//                 // $csmding_dddepartment_id = null;
//                 // 更新部门清单
//                 if ($row != null) {
//                     $param['updatetime'] = time();
//                     $dao->where("id", "=", $row->id)->update($param);
//                     static::p("更新部门: " . $respd->name);
//                     // $csmding_dddepartment_id = $row->id;
//                 } else {
//                     $param['createtime'] = time();
//                     static::p("创建部门: " . $respd->name);
//                     $dao->create($param);
//                     // $csmding_dddepartment_id = $dao->getLastInsID();
//                 }

//                 $this->cliuser($c, $access_token, $item->id, $remoteversion, 0);
            }
        }
    }
    
    private function _asyndepart($c,$reqd,$remoteversion,$access_token,$departtid,$dao){
        
        $row = $dao->where('departmentid', '=', $departtid)
        ->where('status', '=', 'normal')
        ->find();
        $reqd->setId($departtid);
        $respd = $c->execute($reqd, $access_token, "https://oapi.dingtalk.com/department/get");
        if ($respd == null) {
            return;
        }
        $param = [
            'remoteversion' => $remoteversion
        ];
        static::pp($param, $respd, 'name');
        static::pp($param, $respd, 'ext');
        static::pp($param, $respd, 'userPermits');
        static::pp($param, $respd, 'userPerimits');
        static::pp($param, $respd, 'orgDeptOwner');
        static::pp($param, $respd, 'outerDept');
        static::pp($param, $respd, 'deptManagerUseridList');
        static::pp($param, $respd, 'groupContainSubDept');
        static::pp($param, $respd, 'outerPermitUsers');
        static::pp($param, $respd, 'outerPermitDepts');
        static::pp($param, $respd, 'deptPerimits');
        static::pp($param, $respd, 'createDeptGroup');
        static::pp($param, $respd, 'deptGroupChatId');
        static::pp($param, $respd, 'id', 'departmentid');
        static::pp($param, $respd, 'autoAddUser');
        static::pp($param, $respd, 'deptHiding');
        static::pp($param, $respd, 'deptPermits');
        static::pp($param, $respd, 'order');
        static::pp($param, $respd, 'parentid');
        
        // 处理父节点
        if (isset($param['parentid'])) {
            $rowparent = $dao->where('departmentid', '=', $param['parentid'])
            ->where('status', '=', 'normal')
            ->find();
            if ($rowparent != null) {
                $param['parentid'] = $rowparent->id;
            }
        }
        
        // $csmding_dddepartment_id = null;
        // 更新部门清单
        if ($row != null) {
            $param['updatetime'] = time();
            $dao->where("id", "=", $row->id)->update($param);
            static::p("更新部门: " . $respd->name);
            // $csmding_dddepartment_id = $row->id;
        } else {
            $param['createtime'] = time();
            static::p("创建部门: " . $respd->name);
            $dao->create($param);
            // $csmding_dddepartment_id = $dao->getLastInsID();
        }
        
        $this->cliuser($c, $access_token, $departtid, $remoteversion, 0);
        
    }

    public static function p($str)
    {
        echo $str . "<BR>";
    }
}
