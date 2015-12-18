<?php

ini_set("memory_limit","100M");
ini_set('display_errors', 'On');
error_reporting(1);
define("BASEDIR", realpath(dirname(__FILE__) . "/../../../")); //定位到框架根目录
include BASEDIR."/Grug/Loader.php";
spl_autoload_register("\\Grug\\Loader::autoload");
// var_dump($argv);
// Application::getInstance(BASEDIR)->execute("test", $argv);
Application::getInstance(BASEDIR)->execute(array('testclass', 'test'), $argv);

function test() {
	$obj = new TestModel();
	var_dump($obj->getInfo());
	$param = func_get_args();
	var_dump($param);
}

class testclass {
	function test($param) {
		echo "hahaclass\n";
		var_dump($param);
	}
}