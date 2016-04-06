<?php

class Controller {
    private $data;
    private $controller_name;
    private $view_name;
    private $view_dir;
    private $path;


    public function __construct($controller_name, $view_name) {
        $controller_name = str_replace("_", "/", $controller_name);
        $this->controller_name = $controller_name;
        $this->view_name = $view_name;
        $this->view_dir = Application::getInstance()->base_dir.'/App/Views';
    }

    /**
     * 传递变量给模板
     * 当传递的数据类型为数组时(即只传一个数组参数)，数组的key为数据变量名,对应的值为变量值
     * 当传递两个参数时，$key为变量名，$value为变量值
     * @param unknown $key
     * 
     */
    protected function assign($key, $value = null) {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->data[$k] = $v;
            }
        } else {
            $this->data[$key] = $value;
        }
        
    }
    /**
     * 模板渲染
     * @param string $file
     * 
     */
    protected function display($file = '') {
        if (empty($file)) {
            $file = strtolower($this->controller_name).'/'.$this->view_name.'.phtml';
        }
        $this->path = $this->view_dir.'/'.$file;
        unset($file); //销毁其他变量，以保证当前符号表中的变量都来自extract($this->data);
        extract($this->data);
        // var_dump(get_defined_vars());
        include $this->path;
        // require_once $path;
    }

    /**
     * 获取参数,如果$key为空则获取所有参数
     * 
     */
    protected function getParam($key = null) {
        $params = array_merge($_GET ,$_POST);
        if ($key !== null) {
            return $params[$key];
        } else {
            return $params;
        }
    }

}