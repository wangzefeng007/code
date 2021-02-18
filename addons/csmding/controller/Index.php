<?php
namespace addons\csmding\controller;

use addons\csmding\library\Csmding;
use think\addons\Controller;

class Index extends Controller
{

    public function index()
    {
        $this->error("当前插件暂无前台页面");
    }

    // http://127.0.0.1/fastadmin_plugin_csmmeet/public/addons/csmding/index/cli
    public function cli()
    {
        $assess_token = Csmding::getAccessToken();

        $this->_clidepart($assess_token);
    }

    // http://127.0.0.1/fastadmin_plugin_csmmeet/public/addons/csmding/index/appendnamespace
    public function appendnamespace()
    {
        if (true) {
            return;
        }
        $path = 'D:\abc_work\source\gitee.com\jpeanut\fastadmin_plugin_csmmeet\source\fastadmin_plugin_csmmeet\addons\csmding\library\dingtalk';
        $prepend = "addons\csmding\library\dingtalk";
        $this->writePrepend($path, $prepend);
    }

    private function writePrepend($path, $prepend)
    {
        $temp = scandir($path);
        foreach ($temp as $v) {
            $filename = $path . DS . $v;
            if (is_dir($filename)) {
                if ($v == '.' || $v == '..') {
                    continue;
                }
                self::writePrepend($filename, $prepend);
            } else {
                $content = file_get_contents($filename);
                $content = str_replace('<?php', '//<?php', $content);
                $content = "<?php \r\nnamespace {$prepend};\r\nuse \Exception;\r\n" . $content;
                // var_dump($content);
                file_put_contents($filename, $content);
            }
        }
    }

    
}
