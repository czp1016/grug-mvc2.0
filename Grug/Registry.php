<?php

class Grug_Registry {
    private static $registry = array();

    protected function __construct() {
        
    }

    public static function get($key, $value) {
        return self::$registry[$key];
    }
    
    public static function set($key, $value) {
        self::$registry[$key] = $value;
    }
}