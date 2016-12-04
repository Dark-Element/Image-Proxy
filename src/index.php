<?php
/**
 * Created by PhpStorm.
 * User: mint
 * Date: 8/27/16
 * Time: 10:11 AM
 */

require 'config.php';
require 'Classes/Autoloader.php';
require '../vendor/autoload.php';

if(!isset($_REQUEST['params']) && !isset($_REQUEST['source_url'])){
	die('');
}

if(!isset($_REQUEST['source_url'])){
	die('');
}
$response = new Response();