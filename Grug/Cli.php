<?php

class Grug_Cli {
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
    //命令行支持
    public function execute($func, $params) {
        return call_user_func_array($func, $params);
    }
}