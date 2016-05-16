<?php

class Grug_Bootstrap {
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

    public function init() {
        $this->_initDbConfig();
    }
    
    private function _initDbConfig() {
        //导入配置文件
        $db_conf = parse_ini_file($this->base_dir."/Conf/db.conf", true); //此配置文件最好不要和代码放在一起，可放在服务器其他位置
        foreach ($db_conf as $key => $value) {
            Grug_Registry::set($key, $value);
        }
    }

    
}