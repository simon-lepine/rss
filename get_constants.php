<?php

header('Access-Control-Allow-Origin:*');
ini_set('display_errors', 1);

/**
 * get file head
 */
if (
	(!file_exists('file_head.inc.php'))
	||
	(!include('file_head.inc.php'))
){
	$_SERVER['return']['message']['error'][] = 'Something went terribly wrong :(';
	$_SERVER['class']['error']->out('', 200);
}

/**
 * build return
 */
$_SERVER['return']['result'] = array(
	'server_url' => $_SERVER['class']['constants']->server_url, 
	'error_url' => $_SERVER['class']['constants']->error_url, 
	'webroot' => $_SERVER['class']['constants']->webroot, 
	'remote_addr' => $_SERVER['class']['constants']->remote_addr, 
	'day' => $_SERVER['class']['constants']->day, 
	'version_number' => $_SERVER['class']['constants']->version_number, 
	'version_name' => $_SERVER['class']['constants']->version_name, 
);

/**
 * build and output success
 */
$_SERVER['return']['success']=1;
$_SERVER['class']['error']->out('', 200);
