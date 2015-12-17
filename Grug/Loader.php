<?php
namespace Grug;

class Loader {
	static $Grug = array('Application','Controller');

	static function autoload($classname) {
		if (in_array($classname, self::$Grug)) {
			require_once BASEDIR."/Grug/".$classname.".php";
		} elseif (preg_match('#Model$#', $classname)) {
			$classname = str_replace("_", "/", $classname);
			require_once BASEDIR."/App/Models/".$classname.".php";
		} elseif (preg_match('#Controller$#', $classname)) {
			$classname = str_replace("_", "/", $classname);
			require_once BASEDIR."/App/Controllers/".$classname.".php";
		} else {
			require_once BASEDIR."/Library/".$classname.".php";
		}
	}

	static function autoloadFrame($classname) {
		require_once BASEDIR."/".str_replace("\\", "/", $classname).".php";
	}
}