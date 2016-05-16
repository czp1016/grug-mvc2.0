<?php

class Grug_Dispatch {
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
        $this->_beforeRoute($uri);
        $uri_arr = explode('/', trim($uri, '/'));
        $method_param = array_pop($uri_arr); //取出method和参数
        $method = array_shift(explode('?', $method_param));
        if (!$method) {
            $controller = $method = 'index';
        } elseif (!$controller = array_pop($uri_arr)) { //取出controller
            $controller = $method;
            $method = 'index';    
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
        $this->_afterRoute($module, $controller, $method);
        // var_dump($class, $method);
        $obj = new $class($module.$controller, $method);
        if (method_exists($obj,'init')) {
            $obj->init();
        }
        $obj->$method();
    }

    private function _beforeRoute(&$uri) {
        if (strtolower(trim($uri, '/')) == 'wordpress') {
            require($tihs->base_dir.'Wordpress/index.php');
            exit();
        }
    }

    private function _afterRoute(&$module, &$controller, &$method) {
        if ($controller == 'Blog') {
            if (isset(BlogController::$HTML_ACTION[$method])) {
                $method = BlogController::$HTML_ACTION[$method]['method'];
            }
            return true;
        }
        if ($controller == 'Wordpress') {
            require($tihs->base_dir.'Wordpress/index.php');
            exit();
        }
    }
}