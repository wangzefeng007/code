<?php
namespace addons\csmding;

use app\common\library\Menu;
use think\Addons;
use think\response\Redirect;
use think\exception\HttpResponseException;
use app\admin\library\Auth;
use think\Request;

/**
 * 插件
 */
class Csmding extends Addons
{

    /**
     * 插件安装方法
     *
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name' => 'csmding',
                'title' => '钉钉对接套件',
                'sublist' => [
                    [
                        'name' => 'csmding/ddcode',
                        'title' => '代码生成器',
                        'icon' => 'fa fa-meetup',
                        'sublist' => [
                            [
                                'name' => 'csmding/ddcode/index',
                                'title' => '查看'
                            ],
                            [
                                'name' => 'csmding/ddcode/help',
                                'title' => '帮助文档'
                            ]
                        ]
                    ],
                    [
                        'name' => 'csmding/dduser',
                        'title' => '钉钉组织架构',
                        'icon' => 'fa fa-meetup',
                        'sublist' => [
                            [
                                'name' => 'csmding/dduser/index',
                                'title' => '查看'
                            ],
                            [
                                'name' => 'csmding/dduser/getUsersByDepartsAjax',
                                'title' => '组织树'
                            ],
                            [
                                'name' => 'csmding/dduser/getdeparttreeAjax',
                                'title' => '组织树获取帐号'
                            ]
                        ]
                    ],
                    [
                        'name' => 'csmding/msgsend',
                        'title' => '图文消息推送',
                        'icon' => 'fa fa-meetup',
                        'sublist' => [
                            [
                                'name' => 'csmding/msgsend/index',
                                'title' => '查看'
                            ],
                            [
                                'name' => 'csmding/msgsend/add',
                                'title' => '新增'
                            ],
                            [
                                'name' => 'csmding/msgsend/edit',
                                'title' => '修改'
                            ],
                            [
                                'name' => 'csmding/msgsend/sendmsg',
                                'title' => '推送'
                            ],
                            [
                                'name' => 'csmding/msgsend/del',
                                'title' => '删除'
                            ]
                        ]
                    ],
                    [
                        'name' => 'csmding/ddportlet/mngapps',
                        'title' => '微应用管理',
                        'icon' => 'fa fa-meetup',
                        'sublist' => [
                            [
                                'name' => 'csmding/ddportlet',
                                'title' => '微应用首页(手机钉钉访问)',
                                'sublist' => [
                                    [
                                        'name' => 'csmding/ddportlet/index',
                                        'title' => '查看'
                                    ],
                                    [
                                        'name' => 'csmding/ddportlet/article',
                                        'title' => '资讯'
                                    ]
                                ]
                            ],
                            [
                                'name' => 'csmding/porletbanner',
                                'title' => '微应用Banner管理',
                                'sublist' => [
                                    [
                                        'name' => 'csmding/porletbanner/index',
                                        'title' => '查看'
                                    ],
                                    [
                                        'name' => 'csmding/porletbanner/add',
                                        'title' => '新增'
                                    ],
                                    [
                                        'name' => 'csmding/porletbanner/edit',
                                        'title' => '修改'
                                    ],
                                    [
                                        'name' => 'csmding/porletbanner/del',
                                        'title' => '删除'
                                    ]
                                ]
                            ],
                            [
                                'name' => 'csmding/porletfunc',
                                'title' => '微应用功能点配置',
                                'sublist' => [
                                    [
                                        'name' => 'csmding/porletfunc/index',
                                        'title' => '查看'
                                    ],
                                    [
                                        'name' => 'csmding/porletfunc/add',
                                        'title' => '新增'
                                    ],
                                    [
                                        'name' => 'csmding/porletfunc/edit',
                                        'title' => '修改'
                                    ],
                                    [
                                        'name' => 'csmding/porletfunc/del',
                                        'title' => '删除'
                                    ]
                                ]
                            ],
                            [
                                'name' => 'csmding/porletarticle',
                                'title' => '微应用资讯管理',
                                'sublist' => [
                                    [
                                        'name' => 'csmding/porletarticle/index',
                                        'title' => '查看'
                                    ],
                                    [
                                        'name' => 'csmding/porletarticle/add',
                                        'title' => '新增'
                                    ],
                                    [
                                        'name' => 'csmding/porletarticle/edit',
                                        'title' => '修改'
                                    ],
                                    [
                                        'name' => 'csmding/porletarticle/del',
                                        'title' => '删除'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'csmding/msgmedia',
                        'title' => '图片转MediaID',
                        'icon' => 'fa fa-meetup',
                        'sublist' => [
                            [
                                'name' => 'csmding/msgmedia/index',
                                'title' => '查看'
                            ],
                            [
                                'name' => 'csmding/msgmedia/add',
                                'title' => '新增'
                            ],
                            [
                                'name' => 'csmding/msgmedia/edit',
                                'title' => '修改'
                            ],
                            [
                                'name' => 'csmding/msgmedia/del',
                                'title' => '删除'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     *
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('csmding');
        return true;
    }

    /**
     * 插件启用方法
     *
     * @return bool
     */
    public function enable()
    {
        Menu::enable('csmding');
        return true;
    }

    /**
     * 插件禁用方法
     *
     * @return bool
     */
    public function disable()
    {
        Menu::disable('csmding');
        return true;
    }

    public function moduleInit(&$request)
    {
        $request = Request::instance();
        // 判断是否在钉钉容器,如果是则自动完成登录

        $module = $request->module();
        if ($module == 'admin') {
            // 如果是钉钉入口,则跳转
            $useragent = $request->header("User-Agent");
            if (strpos($useragent, 'DingTalk') === false) {
                return;
            } else {
                // 如果已经登录过,则不处理
                $uid = Auth::instance()->id;
                if ($uid != null) {
                    return;
                }

                // 如果是钉钉登录页面,则不处理
                $currenturl = $request->baseUrl();
                if (strpos($currenturl, "ajax/lang") !== false || strpos($currenturl, "csmding/ddlogin/mobilelogin") !== false || strpos($currenturl, "csmding/ddlogin/dobydd") !== false) {
                    return;
                }

                $redirecturl = $request->baseFile() . '/csmding/ddlogin/mobilelogin?url=' . urlencode($request->url());
                // var_dump($redirecturl);
                $response = new Redirect($redirecturl);
                $response->code(302)
                    ->params([])
                    ->with([]);
                throw new HttpResponseException($response);
                // die();
            }
        }
    }

    public function adminLoginAfter(&$request)
    {
        $dao = new \app\admin\model\csmding\Dduser();
        $uid = Auth::instance()->id;
        $dao->where('faadmin_id', '=',$uid)->where('status','=','normal')->update([
            'faisactivie'=>'true',
            'updatetime'=>time()
        ]);
    }
}
