<?php
namespace app\admin\controller\csmding;

use app\common\controller\Backend;

class Ddmessage extends Backend
{

    // http://127.0.0.1/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmding/ddmessage/media
    public function index()
    {
        return $this->view->fetch();
    }

    public function media()
    {
        if ($this->request->isPost()) {
            $file = $this->request->file('file');
            if (empty($file)) {
                $this->error(__('No file upload or server upload limit exceeded'));
            }
        }

        return $this->view->fetch();
    }
}
