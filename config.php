<?php
require 'vendor/autoload.php';

function doDefine($name, $value){
	if(!defined($name)){ define($name, $value); }
}

error_reporting(E_ALL);
ini_set('display_errors', 'On');
session_start();

//'real' paths
doDefine('REAL_PATH', realpath(dirname(__FILE__)).'/');
doDefine('HTMLS_PATH', REAL_PATH.'htmls/');

//setup logging
doDefine('EOL', "\r\n");
doDefine('DEBUG_LOG', REAL_PATH.'logs/myDebug.log');
doDefine('DEBUG_TIMESTAMP', 'D M d H:i:s');
date_default_timezone_set('America/Los_Angeles');
if(!function_exists('logDebug')){
	function logDebug($text1, $text2=false){
		if($text2){//log an error
			error_log('['.date(DEBUG_TIMESTAMP).'] DAVERROR '.$text2.PHP_EOL, 3, DEBUG_LOG);
		}else{//log a debug
			error_log('['.date(DEBUG_TIMESTAMP).'] '.$text1.PHP_EOL, 3, DEBUG_LOG);
		}
	}
}

doDefine('WWW_DIR', REAL_PATH.'public_html/');
//paths from public_html/
doDefine('CSS_URL_PATH', WWW_DIR.'css/');
doDefine('JS_URL_PATH', WWW_DIR.'js/');

if(!function_exists('ourautoload')){
	function ourautoload($classname){
		if(file_exists(REAL_PATH."classes/". $classname .".php")){
			require_once("classes/". $classname .".php");
		}
		if(file_exists(REAL_PATH."classes/core/". $classname .".php")){
			require_once("classes/core/". $classname .".php");
		}
	}
}
spl_autoload_register('ourautoload');
//see if autoload works:
//logDebug('new Artist: '.var_export(new Artist('testing'), true));

$db = new DB();

$sources = array();
$sourceIdentities = $db->getSourceIdentities();
//logDebug('sourceIdentities: '.var_export($sourceIdentities, true));
foreach($sourceIdentities as $sourceArray){
	$sources[$sourceArray['id']] = $sourceArray['sourcetext'];
}
//logDebug('sources: '.var_export($sources, true));

logDebug('config complete');