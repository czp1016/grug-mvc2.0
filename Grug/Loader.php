<?php
namespace Grug;

class Grug_Loader {
	static function autoload($classname) {
		if (preg_match('#Model$#', $classname)) {
			$classname = str_replace('_', '/', $classname);
			require_once BASEDIR.'/App/Models/'.$classname.'.php';
		} elseif (preg_match('#Controller$#', $classname)) {
			$classname = str_replace('_', '/', $classname);
			require_once BASEDIR.'/App/Controllers/'.$classname.'.php';
		} elseif (preg_match('#^Grug_#', $classname)) {
			$classname = str_replace('_', '/', $classname);
			require_once BASEDIR.'/'.$classname.'.php';
		} else {
			require_once BASEDIR.'/Library/'.$classname.'.php';
		}
	}

}