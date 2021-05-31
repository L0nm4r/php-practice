<?php
namespace mytp;

class Request
{
    protected $pathinfo=null;

    protected $path;

    protected $config = [
        'pathinfo_fetch'   => ['PATH_INFO','ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
        'url_html_suffix'  => 'html' // 伪静态
    ];

    protected $module;
    protected $controller;
    protected $action;
   

    public function path()
    {
        if (is_null($this->path)) {
            $pathinfo = $this->pathinfo();
            $suffix = $this->config['url_html_suffix'];

            // 去除伪静态
            $this->path = preg_replace('/\.(' . $suffix . ')$/i', '', $pathinfo);
        }
        return $this->path;
    }

    public function pathinfo()
    {
        if (is_null($this->pathinfo)) {
            foreach ($this->config['pathinfo_fetch'] as $type) {
                if ($this->server($type)) {
                    $pathinfo = $this->server($type);
                    break;
                }
            }
            $this->pathinfo = empty($pathinfo) ? '' : ltrim($pathinfo, '/');
        }
        return $this->pathinfo;
    }
    
    /**
     * 获取$_SERVER变量值
     */
    public function server($name, $default = null)
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }

    public function setModule($module)
    {
        $this->module = $module;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function module()
    {
        return $this->module ?: '';
    }

    public function controller()
    {
        return $this->controller ?: '';
    }
    
    public function action()
    {
        return $this->action ?: '';
    }

    public function get($name = '', $default = null, $filter = '')
    {
        return $this->input($_GET, $name, $default, $filter);
    }

    public function post($name = '', $default = null, $filter = '')
    {
        return $this->input($_POST, $name, $default, $filter);
    }

    public function input($data = [], $name = '', $default = '', $filter = null)
    {
        if ($name === '') { // 返回全部参数
            return $data;
        }
        $type = 's'; // 默认type String
        if (strpos($name, '/')) {
            list($name, $type) = explode('/', $name); // 调用方式 'name/s'
        }
        $value = isset($data[$name]) ? $data[$name] : $default;
        $value = $this->typeCast($value, $type); // 类型转换

        // 输入过滤
        if ($filter) {
            foreach (explode(',', $filter) as $v) {
                $value = $v($value);
            }
        }
        return $value;
    }

    /**
     * 强制类型转换
     */
    private function typeCast($data, $type)
    {
        switch ($type) {
            case 'a':   // 数组
                $data = (array) $data;
                break;
            case 'd':   // 整型
                $data = (int) $data;
                break;
            case 'f':   // 浮点型
                $data = (float) $data;
                break;
            case 'b':   // 布尔型
                $data = (boolean) $data;
                break;
            case 's':   // 字符串（默认）
            default:
                $data = is_scalar($data) ? (string) $data : '';
        }
        return $data;
    }
}
