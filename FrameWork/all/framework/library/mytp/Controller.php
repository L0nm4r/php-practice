<?php
namespace mytp;

use Smarty;

class Controller
{
    protected $app;
    protected $request;
    protected $Smarty;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $app->request;
        $template_dir = $app->getAppPath() . $this->request->module() . '/view/';
        $template_dir .= strtolower($this->request->controller()) . '/';
        $compile_dir = $app->getRootPath() . 'runtime/temp/';
        $this->Smarty = new Smarty();
        $this->Smarty->template_dir = $template_dir;
        $this->Smarty->compile_dir = $compile_dir;
    }

    public function assign($name, $value = '')
    {
        $this->Smarty->assign($name, $value);
    }
    
    public function fetch($template = '')
    {
        if ($template === '') {
            $template = $this->request->action();
        }
        return $this->Smarty->fetch($template . '.html');
    }
}
