<?php
namespace app\admin\controller\csmding;

use app\common\controller\Backend;
use addons\csmding\library\Csmding;

class Ddcode extends Backend
{

    // http://127.0.0.1/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmding/ddcode/index
    public function index()
    {
        if(\addons\csmding\library\Csmding::assertconfig()===false){
            $this->error("您[钉钉对接套件]插件配置不完整,请到插件管理中配置;如您已经配置,请刷新本页面.",null,null,300);
        }
        
        $config = get_addon_config("csmding");
        $debugmode = $config["debugmode"];
        if ($debugmode == 'N') {
            $this->error('请开启调试模式才可以使用本功能，具体开通：插件管理-钉钉对接套件-调试模式', null, null, 60);
        }

        if ($this->request->isPost()) {
            $code = $this->request->request('code');
            $result = '运行结果:' . PHP_EOL . $this->evalcode($code);
            $generatecode = '运行代码:' . PHP_EOL . $this->generatecode($code);
            $this->success(null, null, array(
                'result' => $result,
                'generatecode' => $generatecode
            ));
        }
        $this->assignconfig("helpurl", "csmding/ddcode/help");
        return $this->fetch();
    }

    public function help()
    {
        $this->assign("helpimg", "/assets/addons/csmding/img/help1.png");
        return $this->fetch();
    }

    private function generatecode($code)
    {
        $lines = explode(PHP_EOL, $code);

        $processedcode = "\$access_token = \\addons\\csmding\\library\\Csmding::getAccessToken();" . PHP_EOL . PHP_EOL;
        foreach ($lines as $line) {
            $line = $this->processgenerateline($line);
            if ($line != null) {
                $processedcode .= $line . PHP_EOL;
            }
        }
        return $processedcode;
    }

    private function processgenerateline($line)
    {
        $line = trim($line);
        if ($line == null || $line == '') {
            return null;
        }
        if (static::startsWith($line, "include")) {
            return null;
        }
        if (static::startsWith($line, "require")) {
            return null;
        }
        if (static::startsWith($line, "\$c = new") || static::startsWith($line, "\$req = new") || static::startsWith($line, "\$req->") || static::startsWith($line, "\$resp =")) {
            $line = str_replace(';', '', $line) . ';';
            return $line;
        }
        return null;
    }

    private function evalcode($code)
    {
        $access_token = Csmding::getAccessToken();
        $lines = explode(PHP_EOL, $code);

        $processedcode = "
            use addons\csmding\library\dingtalk\DingTalkConstant;
            use addons\csmding\library\dingtalk\DingTalkClient;
        ";
        foreach ($lines as $line) {
            $line = $this->processevalline($line);
            if ($line != null) {
                $processedcode .= $line . PHP_EOL;
            }
        }
        $processedcode .= "\$s = var_export(\$resp,true);" . PHP_EOL . "return \$s;" . PHP_EOL;
        trace($processedcode);
        eval($processedcode);
        return $s;
    }

    private function processevalline($line)
    {
        $line = trim($line);
        if ($line == null || $line == '') {
            return null;
        }
        if (static::startsWith($line, "include")) {
            return null;
        }
        if (static::startsWith($line, "require")) {
            return null;
        }
        if (static::startsWith($line, "\$c = new") || static::startsWith($line, "\$req = new") || static::startsWith($line, "\$req->") || static::startsWith($line, "\$resp =")) {
            $line = str_replace(';', '', $line) . ';';
            $line = str_replace('$req = new ', '$req = new \\addons\\csmding\\library\\dingtalk\\', $line) . ';';
            return $line;
        }
        return null;
    }

    static function startsWith($haystack, $needle)
    {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
