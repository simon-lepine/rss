<?php

//future {snippets['File Checklist']['snip']}

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{"docu": {"Type": "snippet",
		"Short Purpose": "PHP File Header","Version": "2022.02.05",
		"Old Versions": "2019.07.11,2019.08.23"}}
 */

/*
 * make accessible from anywhere with CORS 
 * //todo API only
header('Access-Control-Allow-Origin:*');

/*
{"docu": {"Type": "snippet",
		"Short Purpose": "Scheduled Jobs Ignore Abort", "Version": "2019.06.29",
		"Old Versions": ""}}
 */
/*
 * if user is not waiting for a return then continue running even if user disconnects
 * and increase mex execution time to 60 minutes
 * 
 * //todo schedules jobs only
 * 
if (
	(empty($_POST['ret']))
	&&
	(empty($_POST['return']))
){
	ob_end_clean();
	header('Connection: close\r\n');
	header('Content-Encoding: none\r\n');
	header('Content-Length: 1');
	ignore_user_abort(true);
}
ini_set('max_execution_time', 3600);

/*
 * Check if file was called directly and error out because file should never be called directly
 * 
 * //todo class files only
 * 
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])){
	echo 'Something went terribly wrong. :(';
	die;
}

/*
 * get file head
 */
if (
	(!file_exists('file_head.inc.php'))
	||
	(!include('file_head.inc.php'))
){
	echo 'Something went terribly wrong. :(';
	die;
}

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * SETUP CLASSES EVENT LOG BLOCK
 * Everything below this point is setting up simon_lepines_log
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

$_SERVER['class']['log']->add->default_source='//todo';
$_SERVER['class']['log']->add->default_title='Error';//debug change this if needed
$_SERVER['class']['log']->add->default_message='Something went terribly wrong :(';
$_SERVER['class']['output']->browser_flag=0;
//$_SERVER['class']['error']->browser_flag=0;//debug change this if needed

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * SETUP CLASSES CODE BLOCK
 * Everything below this point is setting up classes used in this file
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

/*
 * Classes used in this file
 */
if (
	(!$_SERVER['class']['csrf'] = new csrf)
	||
	(!$_SERVER['class']['jwt'] = new jwt)
	||
	(!$_SERVER['class']['microservices'] = new microservices)
	||
	(!$_SERVER['class']['db'] = new db)
	||
	(!$_SERVER['class']['blog_feed'] = new blog_feed)
){
	$tmp = basename(__FILE__) . ':' . __LINE__;
	error_log("[ LAKEBED ][ {$tmp} ] Failed to init classes.");
	$_SERVER['class']['log']->add->entry(array(
		'admin_only_message' => 'Unable to load required classes/files.',
		'type' => 'fatal,error,',
		'severity' => 'fatal',
		'notifications' => 'error,failure,security,potentially_malicious,',
	));
	$_SERVER['return']['message']['error'][] = $_SERVER['class']['log']->last_entry['message'];
	$_SERVER['class']['error']->out($_SERVER['class']['constants']->settings['error_url'], 200);
}

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * CSRF/TOKEN CODE BLOCK
 * Everything below this point is checking CSRF/API token
 * No point going further if token is not provided or not valid
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

/*
 * ensure we have a CSRF cookie or if not redirect
 *
if (
	($_POST['csrf'] != $_SERVER['class']['general']->internal_hash)
	&&
	($_POST['csrf'] != $_SERVER['class']['general']->old_internal_hash)
){
if (
	(!isset($_COOKIE['csrf']))
	||
	(!is_string($_COOKIE['csrf']))
	||
	(!$_COOKIE['csrf'])
	||
	(!isset($_SESSION['csrf']))
	||
	(!is_string($_SESSION['csrf']))
	||
	(!$_SESSION['csrf'])
){
	$_SERVER['class']['log']->add->entry(array(
		'message' => 'The security token is not valid.',
		'type' => 'fatal',
		'severity' => 'fatal',
		'notifications' => 'error,security,potentially_malicious,', 
	));
	$_SERVER['return']['message']['error'][] = $_SERVER['class']['log']->last_entry['message'];
	$_SERVER['class']['error']->out("{$_SERVER['class']['constants']->server_url}/write_csrf.php?next_url=//todo", 200);
}
}

/*
 * //input_required _POST[csrf] - //special for microservices, csrf must match [class][general]->internal_hash since this is a microservice only available to other server-side code.
 *
if (
	($_POST['csrf'] != $_SERVER['class']['general']->internal_hash)
	&&
	($_POST['csrf'] != $_SERVER['class']['general']->old_internal_hash)
){
	$_SERVER['class']['log']->add->entry(array(
		'admin_only_message' => 'CSRF token is not valid.',
		'type' => 'fatal',
		'severity' => 'fatal',
		'notifications' => 'error,security,potentially_malicious,', 
	));
	$_SERVER['return']['message']['error'][] = $_SERVER['class']['log']->last_entry['message'];
	$_SERVER['class']['error']->out('write_csrf.php?next_url=/error/', 200);
}

/*
 * //input_required _POST[csrf] - string/hash, written by /write_csrf.php and stored in cookie and session
 *
if (!$_SERVER['class']['csrf']->compare($_POST['csrf'])){
	$_SERVER['class']['log']->add->entry(array(
		'admin_only_message' => 'CSRF token is not valid.',
		'type' => 'fatal',
		'severity' => 'fatal',
		'notifications' => 'error,security,potentially_malicious,', 
	));
	$_SERVER['return']['message']['error'][] = $_SERVER['class']['log']->last_entry['message'];
	$_SERVER['class']['error']->out('write_csrf.php?next_url=/error', 200);
}

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * _POST ARGS CODE BLOCK
 * Everything below this point is checking API args
 * No point going further if required args are not set or extra args are set
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

/*
 * check provided _POST args against required and optional
 */
if (!$tmp = $_SERVER['class']['csrf']->check_post(
	$_POST,
	array('security_token', 'user_id', 'feed_url'), 
	array()
)){
	$_SERVER['class']['log']->add->entry(array(
		'admin_only_message' => '_POST input does not match what was expected.',
		'additional' => array(
			'message_array' => $_SERVER['class']['csrf']->message_array, 
			'result' => $tmp
		), 
		'type' => 'fatal',
		'severity' => 'fatal',
		'notifications' => 'error,permissions,potentially_malicious,',
	));
	$_SERVER['return']['message']['error'][] = $_SERVER['class']['log']->last_entry['message'];
	$_SERVER['class']['error']->out('error', 200);
}

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * SECURITY CODE BLOCK
 * Everything below this point is setting up security in the file
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

/*
 * confirm http_referer matches
 *
if ($_SERVER['class']['files']('http_referer')){
if (!strpos(" {$_SERVER['HTTP_REFERER']} ", $_SERVER['class']['constants']->webroot)){
	$_SERVER['class']['log']->add->entry(array(
		'admin_only_message' => "The HTTP referrer ({$_SERVER['HTTP_REFERER']}) does not match the Lakebed installation ({$_SERVER['class']['constants']->webroot}).",
		'type' => 'error',
		'severity' => 'high',
		'notifications' => 'error,security,potentially_malicious,',
	));
	$_SERVER['return']['message']['error'][] = $_SERVER['class']['log']->last_entry['message'];
	$_SERVER['class']['error']->out('login/logout.php', 200);
}
}


/*
 * wipe _GET[jwt] so people must actually login
 * //special for admin pages
 *
 * //todo
 *
$_GET['jwt']='';

/*
 * confirm if JWT is valid
 *
if (
	(!$_SERVER['class']['jwt']->is_valid())
	||
	(!$_SERVER['class']['jwt']->parse_payload())
	||
	(!$_SERVER['class']['jwt']->arr['us_id'])
){
	$_SERVER['class']['log']->add->entry(array(
		'message' => 'You must login to access this resource.',
		'type' => 'error',
		'severity' => 'high',
		'notifications' => 'error,security,potentially_malicious,',
	));
	$_SERVER['return']['message']['error'][] = $_SERVER['class']['log']->last_entry['message'];
	$_SERVER['class']['error']->out('login/logout.php', 200);
}

/*
 * set default user ID for log entries going forward
 */
$_SERVER['class']['log']->add->default_us_id = $_SERVER['class']['jwt']->arr['us_id'];

/*
 * confirm permissions are set
 */
if (
	(!isset($_SERVER['class']['jwt']->arr['us_permissions']))
	||
	(!is_array($_SERVER['class']['jwt']->arr['us_permissions']))
){
	$_SERVER['class']['log']->add->entry(array(
		'message' => 'You must login to access this resource.',
		'type' => 'error',
		'severity' => 'high',
		'notifications' => 'error,security,potentially_malicious,',
	));
	$_SERVER['return']['message']['error'][] = $_SERVER['class']['log']->last_entry['message'];
	$_SERVER['class']['error']->out('login/logout.php', 200);
}

/*
 * check permissions from microservice
 * //note we do this so permissions are always updated
 * //todo
$tmp = $_SERVER['class']['general']->microservice_call(
	'read/users/check_permission.php',
	array(
		'csrf' => $_SERVER['class']['general']->internal_hash,
		'us_id' => $_SERVER['class']['jwt']->arr['us_id'],
		'permissions_to_check' => json_encode(array(
			'//todo', 
			'//todo admin'
		)),
	)
);
if (
	(!$tmp)
	||
	(!is_array($tmp))
	||
	(empty($tmp['success']))
	||
	(empty($tmp['has_permission']))
	||
	(!$_SERVER['class']['jwt']->arr['us_permissions'] = $tmp['result'])
){
	$_SERVER['class']['log']->add->entry(array(
		'message' => 'You must login to access this resource.',
		'type' => 'error',
		'severity' => 'high',
		'notifications' => 'error,security,potentially_malicious,',
	));
	$_SERVER['return']['message']['error'][] = $_SERVER['class']['log']->last_entry['message'];
	$_SERVER['class']['error']->out('login/logout.php', 200);
}

/*
 * reload HTML now that we have permissions
 * //todo for UIs
$_SERVER['class']['html']->us_permissions='';
$_SERVER['class']['html']->__construct();

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * DB CONNECTION CODE BLOCK
 * Everything below this point is DB permissions/connection 
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

/*
 * set DB perms and tbls
 */
if (
	(!$_SERVER['class']['db']->set_perms(array('UPDATE', 'INSERT')))
	||
	(!$_SERVER['class']['db']->set_tbls(array('')))
){
	$_SERVER['class']['log']->add->entry(array(
		'admin_only_message' => 'Failed to set database permissions and/or tables.',
		'type' => 'error',
		'severity' => 'high',
		'notifications' => 'error,security,potentially_malicious,',
	));
	$_SERVER['return']['message']['error'][] = $_SERVER['class']['log']->last_entry['message'];
	$_SERVER['class']['error']->out('login/logout.php', 200);
}

/*
{"docu": {"Type": "snippet",
		"Short Purpose": "Database Connection", "Version": "2020.08.18",
		"Old Versions": "2019.06.17"}}
 */
if (!$_SERVER['class']['db']->connect()){
	$_SERVER['class']['log']->add->entry(array(
		'admin_only_message' => 'Failed to connect to the DB.',
		'type' => 'fatal',
		'severity' => 'fatal',
		'notifications' => 'error,potentially_malicious,database,canada_scores',
	));
	$_SERVER['return']['message']['error'][] = $_SERVER['class']['log']->last_entry['message'];
	$_SERVER['class']['error']->out('error', 200);
}



/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * END INPUT BLOCK
 * Everything below this point is validating/sanitizing user input
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

/*
 * next_url
 * used for the next URL after a form completes successfully
 * //todo
$_POST['next_url'] = $_SERVER['class']['gen']->next_url($_POST['next_url']);

/*
 * f_name
 * used for any file name
 * //todo
$_POST['f_nmae'] = $_SERVER['class']['san']->f_name($_POST['f_name']);//todo need to switch everything to this

/*
 * f_line
 * used for the integer line in any file
 * //todo
$_POST['f_line'] = floatval($_POST['f_line']);//todo need to switch everything to this

/*
 * MESSage
 * used for any long text message such as emails and errors
 * //todo
$_POST['mess'] = $_SERVER['class']['san']->l_text($_POST['mess']);

/*
 * SUBject
 * used for any short text email subject
 * //todo
$_POST['sub'] = $_SERVER['class']['san']->sh_text($_POST['sub']);

/*
 * EMail
 * or TO
 * used users email or to address of emails
 * //todo
$_POST['email'] = $_SERVER['class']['san']->email($_POST['email']);
$_POST['to'] = $_SERVER['class']['san']->email($_POST['to']);

/*
 * FORMat
 * used for any API return format
 * //todo
$_POST['form'] = $_SERVER['class']['san']->db_val($_POST['form']);

/*
 * COLumnS
 * used for CSV/array of column nmes
 * //todo
$_POST['cols'] = $_SERVER['class']['san']->db_val($_POST['cols']);//todo all ['class']['san'] should accept and return CSV/array

//leftoff identify other common vars and add here

/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * END HEADER CODE BLOCK
 * Everything above this point is the same in every file and commented in/out as needed
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

/**
 * parse feed entires
 */
$tmp = $_SERVER['class']['blog_feed']->parse($_POST['feed_url']);

//$tmp = $_SERVER['class']['db']->query('SELECT * FROM `20220728lakebed_app`.`users`');

/**
 * build return
 */
$_SERVER['return']['result'] = $tmp;
$_SERVER['return']['success'] = 1;
$_SERVER['class']['output']->send();

/*
{"docu": {"Type": "snippet",
		"Short Purpose": "Component Docu", 	"Version": "2020.07.26",
		"Old Versions": "2019.07.17"}}
{
	"docu": {
		"Type": "component",
		
		"Short Purpose": "",
		"Long Purpose": "",
		"Tags": "",
		"Intranet Tag": "",

		"Users Goal": "What is the users goal or purpose when accessing/using this component? \nALL \nBuying Decision Maker \nBuying Approver \nIT Manager \nSysAdmin \nEmployee \nManager \nInternal Analyst \nExternal Analyst \nInternal Programmer \nExternal Programmer \nMalicious User",

		"User Memory Expectation": "What are we expecting the user to remember when accessing/using this component?",

		"Visit Frequency": "How often are each of the following users likely to visit this component? \nALL \nBuying Decision Maker \nBuying Approver \nIT Manager \nSysAdmin \nEmployee \nManager \nInternal Analyst \nExternal Analyst \nInternal Programmer \nExternal Programmer \nMalicious User",

		"Special Dev": "",

		"SysAdmin Only": "1/0", 

		"Work/Process Flow": [
			"Page/Script Load",
			"Step 2",
			"Step 3",
			""
		]
	}
}
 */






























