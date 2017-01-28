<?php 
if(defined('CMS_DEBUG_MODE') && CMS_DEBUG_MODE) {
	ini_set('display_errors', 1);
	ini_set('disply_startup_errors', 1);
	error_reporting(E_ALL);
}

define('CMS_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
define('CMS_DATA_PATH', CMS_ROOT_PATH.'data/');
define('CMS_CORE_PATH', dirname(__FILE__).'/');
define('CMS_NAMESPACE_PATH', CMS_ROOT_PATH.'components/');
define('CMS_LANG_PATH', CMS_DATA_PATH.'langs/');
define('CMS_LOG_PATH', CMS_CORE_PATH.'logs/');

$loader_config = require(CMS_CORE_PATH.'libs/load/config.php');
require_once(CMS_CORE_PATH.'classes/class.class.php');
require_once(CMS_CORE_PATH.'libs/load/classes/loader.class.php');
$loader = new myLoader($loader_config);

$loader->myclass('singleton');
$loader->myclass('cms');

$cms_config = $loader->file('config.inc.php', CMS_CORE_PATH, myLoader::L_RETURNED);
$cms_config['loader_object'] = $loader;

myCMS::getInstance($cms_config);