<?php
/**
 * Created by PhpStorm.
 * User: mint
 * Date: 8/27/16
 * Time: 10:46 AM
 */

class Autoloader {
	static public function loader($className) {
		$filename = "Classes/" . str_replace('\\', '/', $className) . ".php";
        if (file_exists($filename)) {
            include($filename);
            if (class_exists($className)) {
                return TRUE;
            }
        }
        return FALSE;
    }
}
spl_autoload_register('Autoloader::loader');