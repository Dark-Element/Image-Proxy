<?php
/**
 * Created by PhpStorm.
 * User: mint
 * Date: 8/27/16
 * Time: 10:46 AM
 */

function __autoload($classname) {
    $filename = 'classes/'. $classname .".php";
    include_once($filename);
}