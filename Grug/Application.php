<?php

class Application {
    public $base_dir;
    protected static $instance;
    public static $config;

    protected function __construct($base_dir) {
        $this->base_dir = $base_dir;
    }

    static function getInstance($base_dir = '') {
        if (empty(self::$instance)) {
            self::$instance = new self($base_dir);
        }
        return self::$instance;
    }

    public function dispatch() {
        $uri = $_SERVER['REQUEST_URI']; //获取URI
        // var_dump($uri);
        $uri_arr = explode('/', trim($uri, '/'));
        $method_param = array_pop($uri_arr); //取出method和参数
        $method = array_shift(explode('?', $method_param));
        if (!$method) {
            $controller = $method = 'index';
        } elseif (!$controller = array_pop($uri_arr)) { //取出controller
            $controller = 'index';    
        } else{
            $module = array_pop($uri_arr);
        }
        $method = strtolower($method); //method取小写，因此controller里的method命名都必须为小写
        $controller = ucwords(strtolower($controller)); //controller首字母大写，其余小写
        if ($module) {
            $module = ucwords(strtolower($module)); //module首字母大写，其余小写
            $module .= "_";
        } else {
            $module = '';
        }
        $class = $module.$controller.'Controller';
        var_dump($class);
        $obj = new $class($module.$controller, $method);
        if (method_exists($obj,'init')) {
            $obj->init();
        }
        $obj->$method();
    }

    //命令行支持
    public function execute($func, $params) {
        return call_user_func_array($func, $params);
    }
}