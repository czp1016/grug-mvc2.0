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
        $uri = $_SERVER['REQUEST_URI']; //获取URL链接
        $uri_arr = explode('/', trim($uri, '/'));
        $method_param = array_pop($uri_arr);
        $method = array_shift(explode('?', $method_param));
        $controller = array_pop($uri_arr);
        $controller_low = strtolower($controller);
        $controller = ucwords($controller_low);
        if (empty($uri_arr)) {
            $module = '';
        } else {
            $module = array_pop($uri_arr);
            if (strstr($module, ".")) {
                $module = '';
            } else {
                $module_low = strtolower($module);
                $module = ucwords($module_low);
                $module .= "_";
            }
        }
        $class = $module.$controller.'Controller';
        $obj = new $class($module.$controller, $method);
        if (method_exists($obj,'init')) {
            $obj->init();
        }
        $obj->$method();
    }
}