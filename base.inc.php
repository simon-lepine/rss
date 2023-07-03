<?php

use \simon_lepines_log AS simon_lepines_log;

/*
{
	'docu': {
		'Type': 'file',
		
		'Short Purpose': 'Base file for easy code reuse and defining docroot/webroot.',
		'Long Purpose': 'Loops through each parent directory looking for af_head.inc.php to figure out Lakebed web/doc roots.',
		'Tags': '',
		'Intranet Tag': '',
		'Accepts': '',
		'Returns': '',
		'Input Required': '',
		'Input Optional': '',
		'Special Dev': ''
	}
}
*/

/*
 * handle errors
 * We do not set log errors here as the sysadmin needs to make that decision
 */
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);//debug

/*
 * Check if file was called directly and error out because file should never be called directly
 */
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])){
	$tmp = basename(__FILE__) . ':' . __LINE__;
	error_log("[ LAKEBED ][ {$tmp} ] File was accessed directly, which should never happen.");
	echo 'Something went terribly wrong. :(';
	
	header('Status: 404 Not Found', false);
	header('Location: https://lakebed.io', false);
	die;
}

/*
 * init commonly used vars
 * to reduce 'undefined' errors
 */
$curl=// cURL pointer
$file_name=
$file_path=
$html=
$inc=
$query=//store SQL query (text)
$row=
$result=
$return=
$tmp=
null;

/*
 * set timezone
 * //future allow users to set their own timezone in JWT
 */
date_default_timezone_set('America/Vancouver');


/*
{"docu": {"Type": 			"ChangeLog", 
		"2022-01-21 18:53:50": {
			"Time": "2022-01-21 18:53:50", 
			"Type": "Added",
			"Components": "all",
			"Description": "Added _SESSION[next_url] and will slowly transition to using it to allow for ->write_csrf->login->next_url",
			"Made By": "slepine"
		}
}}
 */
if (
	(is_array($_SESSION))
	&&
	(empty($_SESSION['next_url']))
){
	$_SESSION['next_url']='';
}

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * HEADER CODE BLOCK
 * create Auato LOADer and set
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

function aload($class){

/*
 * confirm we can load class
 * //note this does not work with composer since the composer classes are not {$class}.php
 *
if (
	(!$_SERVER['class']['constants']->forbidden_dir) 
	||
	(!file_exists("{$_SERVER['class']['constants']->forbidden_dir}/classes/{$class}.php"))
){
	echo "Something went terribly wrong loading {$class}.";
	die;
}


/*
 * load class
 */
if (
	($_SERVER['class']['constants']->forbidden_dir) 
	&&
	(file_exists("{$_SERVER['class']['constants']->forbidden_dir}/classes/{$class}.php"))
){
if (!include("{$_SERVER['class']['constants']->forbidden_dir}/classes/{$class}.php")){
	$_SERVER['return']['message']['error'][] = 'Something went terribly wrong :(';
	echo json_encode($_SERVER['return']);
	
	header('Status: 404 Not Found', false);
	header('Location: https://lakebed.io', false);
	die;
}
	return $class;
}

/*
 * return fail
 */
return false;

/*
 * done func
 */
}

if (function_exists('aload')){
	spl_autoload_register('aload');
}

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * HEADER CODE BLOCK
 * parse _POST[json] out
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

/*
 * json_decode _POST
 */
if (
	(isset($_POST['json']))
	&&
	(count($_POST) == 1)
	&&
	($tmp = json_decode($_POST['json'], true))
	&&
	(is_array($tmp))
	&&
	(count($tmp))
){
	$_POST = $tmp;
}
/*
 * json_decode _GET
 */
if (
	(isset($_GET['jwt']))
	&&
	(is_string($_GET['jwt']))
	&&
	(strlen($_GET['jwt']))
	&&
	(substr_count($_GET['jwt'], '.') == 2)
){
if (
	(!isset($_POST))
	||
	(!is_array($_POST))
	||
	(empty($_POST))
){
	$_POST=$_GET;
}
}

/*
 * change _POST to lowercase so always the same
 */
$_POST = array_change_key_case($_POST);

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * HEADER CODE BLOCK
 * get settings if available
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

if (
	(!empty($_SERVER['class']))
	&&
	(!empty($_SERVER['class']['constants']))
	&&
	(!empty($_SERVER['class']['constants']->forbidden_dir))
	&&
	(file_exists($_SERVER['class']['constants']->forbidden_dir . 'settings.inc.php'))
){
if (!include($_SERVER['class']['constants']->forbidden_dir . 'settings.inc.php')){
	$tmp = basename(__FILE__) . ':' . __LINE__;
	error_log("[ LAKEBED ][ {$tmp} ] Failed to include settings.inc.php.");
	echo 'Something went terribly wrong. :(';
	die;
}
}

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * HEADER CODE BLOCK
 * include/set general.inc.php
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

//live copy/paste from general.inc.php
if (file_exists("{$_SERVER['class']['constants']->docroot}/general.inc.php")){
	include_once("{$_SERVER['class']['constants']->docroot}/general.inc.php");
}


/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * HEADER CODE BLOCK
 * include/set err.inc.php
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

//live copy/paste from err.inc.php
if (file_exists("{$_SERVER['class']['constants']->docroot}/error.inc.php")){
	//debug include_once("{$_SERVER['class']['constants']->docroot}/error.inc.php");
}
if (file_exists("{$_SERVER['class']['constants']->docroot}/output.inc.php")){
	include_once("{$_SERVER['class']['constants']->docroot}/output.inc.php");
}

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * HEADER CODE BLOCK
 * include/set log class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * //future remove references to log everywhere and namespace at the top of this file

//live copy/paste from check_input_function.inc.php
if (file_exists("{$_SERVER['class']['constants']->forbidden_dir}/simon_lepines_log/master.class.php")){
	include_once("{$_SERVER['class']['constants']->forbidden_dir}/simon_lepines_log/master.class.php");
	$_SERVER['class']['log'] = new \simon_lepines_log\master;
}

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * HEADER CODE BLOCK
 * include/set microservices class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

 //live copy/paste from check_input_function.inc.php
if (file_exists("{$_SERVER['class']['constants']->docroot}/microservices.inc.php")){
	include_once("{$_SERVER['class']['constants']->docroot}/microservices.inc.php");
}

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * HEADER CODE BLOCK
 * include/set lakebed_settings
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

//live copy/paste from lakebed.inc.php
if (file_exists("{$_SERVER['class']['constants']->docroot}/lakebed.inc.php")){
	include_once("{$_SERVER['class']['constants']->docroot}/lakebed.inc.php");
}


/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * HEADER CODE BLOCK
 * include/set check_input_function
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

//live copy/paste from check_input_function.inc.php
if (file_exists("{$_SERVER['class']['constants']->docroot}/check_input_function.inc.php")){
	include_once("{$_SERVER['class']['constants']->docroot}/check_input_function.inc.php");
}


/**
 * //live allow remote requests for dev so we can run dev UI servers
 * //note we check environment so even if this makes it to prod it shouldn't matter
 */
if (
	(!empty($_SERVER['class']['constants']))
	&&
	(!empty($_SERVER['class']['constants']->environment))
	&&
	(stripos($_SERVER['class']['constants']->environment, 'dev') !== false)
){
if (!empty($_SERVER['HTTP_ORIGIN'])){
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}", false);
}
	header('Access-Control-Allow-Credentials: true', false);
	header("Access-Control-Allow-Methods: GET, POST", false);
	header("Access-Control-Allow-Headers: *", false);
}
/**
 * done //live
 */


/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{"docu": {"Type": "snippet",
		"Short Purpose": "Component Docu", 	"Version": "2019.07.17",
		"Old Versions": ""}}
{
	"docu": {
		"Type": "component", 
		"Short Purpose": "Lakebed header file.",
		"Long Purpose": "A single file with some basic, required functions (log_add, etc.). Also provides a way to determine LB app root by recursively searching for this file.",
		"Tags": "inc",
		"Intranet Tag": "lb_head.inc.php",

		"Users Goal": "ALL: Simple and easy way to determine document root and a single code base of basic functions/variables.",

		"User Memory Expectation": "None?",

		"Visit Frequency": "ALL: Very regularly.",

		"Special Dev": "",

		"SysAdmin Only": "", 

		"Work/Process Flow": {
			"1": "Page/Script Load",
			"2": "Step 2",
			"3": "Step 3"
		}
    }
}
 */
