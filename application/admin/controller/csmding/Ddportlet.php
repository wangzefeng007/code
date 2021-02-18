<?php
namespace app\admin\controller\csmding;

use app\common\controller\Backend;

class Ddportlet extends Backend
{

    protected $noNeedLogin = [
        "*"
    ];

    // http://127.0.0.1/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmding/ddportlet/index
    public function index()
    {
        $useragent = $this->request->header("User-Agent");
        if (strpos($useragent, 'DingTalk') === false) {
            $url = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] . ( ($_SERVER["SERVER_PORT"]=='80'||$_SERVER["SERVER_PORT"]=='443')?'':':'.$_SERVER["SERVER_PORT"]) . $this->request->baseUrl();
            $this->error("请使用钉钉访问本页面,链接地址：<BR>" . $url, null, null, 600);
        }

        $dao = new \app\admin\model\csmding\Porletbanner();
        $ll = $dao->where('status', '=', 'normal')
            ->order('weigh', 'desc')
            ->select();
        $this->assign('banners', $ll);

        $dao = new \app\admin\model\csmding\Porletfunc();
        $ll = $dao->where('status', '=', 'normal')
            ->order('weigh', 'desc')
            ->select();
        $this->assign('funcs', $ll);

        $dao = new \app\admin\model\csmding\Porletarticle();
        $ll = $dao->where('status', '=', 'normal')
            ->order('weigh', 'desc')
            ->select();
        $this->assign('articles', $ll);
        
        
        $config = get_addon_config("csmding");
        $portletwebtitle = $config["portletwebtitle"];
        $this->assign('title', $portletwebtitle);
        return $this->view->fetch();
    }

    public function mngapps()
    {
        return $this->view->fetch();
    }

    public function article()
    {
        $id = $this->request->request('id');
        $dao = new \app\admin\model\csmding\Porletarticle();
        $row = $dao->where('id', '=', $id)->find();
        $this->assign("row", $row);
        return $this->view->fetch();
    }
}
