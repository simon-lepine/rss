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
	array('security_token', 'user_id'), 
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
 * //todo
if (
	(!$_SERVER['class']['db']->set_perms(array('//todo')))
	||
	(!$_SERVER['class']['db']->set_tbls(array('//todo')))
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
 * //todo
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

$tmp = array();
$tmp[ '117dc1cbe7b1f7cdaaef6942104d80a5' ] = array (
	'url' => 'https://www.schneier.com/blog/atom.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '22590abd6be17e44915fbe8df1c7de0c' ] = array (
	'url' => 'http://feeds.newscientist.com/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'fcdbb199d6c78a2244c83572a1f09fd6' ] = array (
	'url' => 'http://www.newscientist.com/feed.ns?index=online-news', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '99a79ed43e6ef4af71c8c78a2bd23f9d' ] = array (
	'url' => 'http://feeds.feedburner.com/darknethackers', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '8a74522e7dd4456059f22107c079ffcf' ] = array (
	'url' => 'http://www.hackingarticles.in/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '6af4f8e44ce57a6fcdfa6c1c504bc319' ] = array (
	'url' => 'http://feeds.feedburner.com/flyingpenguin', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '91302dcb6b1f16a437e847925003f414' ] = array (
	'url' => 'https://blog.sqreen.io/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'f6f2fbd009d601b220965d9264f1eb01' ] = array (
	'url' => 'http://advances.sciencemag.org/rss/current.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '664c9cb0a76381848e38cc5e0b38b8fc' ] = array (
	'url' => 'https://www.grahamcluley.com/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '8e1edbbd09c2ae33e2a5e08348107e6d' ] = array (
	'url' => 'https://jsnews.io/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'c8657966300aaaf9f32a878748db0277' ] = array (
	'url' => 'https://medium.com/feed/javascript-scene', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '045004273612c481538bc3d71d3c4c27' ] = array (
	'url' => 'https://digitalguardian.com/rss.xml?_ga=1.87533023.614669708.1486326076', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '26b63cb701bc04b3a139fb48d48f3064' ] = array (
	'url' => 'http://www.investing.com/rss/market_overview_Technical.rss', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '25537e62728649f6ed9705f0388ad3c0' ] = array (
	'url' => 'http://www.twis.org/feed', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '49360694c0b5ee0d02ff8e12c8bb2917' ] = array (
	'url' => 'http://www.cnbc.com/id/20910258/device/rss/rss.html', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '8614e54f2f4c4f460c2b6eb6cb115d7b' ] = array (
	'url' => 'http://www.itnews.com.au/RSS/rss.ashx?type=Category&ID=37', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '2d16d8065ff3cee34dddb31f8bc943b9' ] = array (
	'url' => 'http://feeds.feedburner.com/Liquidmatrix', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'bc583bd5d246b0525927deb5b64e3732' ] = array (
	'url' => 'http://feeds.feedblitz.com/thesecurityledger', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '583f9feea885f78a7eeb2165d0662cc9' ] = array (
	'url' => 'https://isc.sans.edu/dailypodcast.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '789f306485fe9bda9efa364ac3cf22db' ] = array (
	'url' => 'http://www.darkreading.com/rss_simple.asp', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '6d12d566fe5ec4efadf01890d0b5a9ac' ] = array (
	'url' => 'http://ideas.ted.com/feed', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '53ced5cab0d7ea6cae1a62cb40264552' ] = array (
	'url' => 'https://blog.skullsecurity.org/feed', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '2843174b7bf56d85b800cfa75782529d' ] = array (
	'url' => 'http://feeds.feedburner.com/infosecResources', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'fe1464f2da6b355308476454ce27c149' ] = array (
	'url' => 'http://www.social-engineer.org/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '22a54ec22270983fd8723e3a585fb2c9' ] = array (
	'url' => 'http://bhconsulting.ie/securitywatch/?feed=rss2', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'df2afd8895e49e00648ca2321ab99fd8' ] = array (
	'url' => 'http://feeds.marketwatch.com/marketwatch/topstories', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '00a471dc770e06d873139529b1b13dbd' ] = array (
	'url' => 'http://www.livescience.com/home/feed/site.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '879c2bcf08a36c8875d0084b1c310cac' ] = array (
	'url' => 'http://robert.penz.name/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '62ebd1f89e7a1bdd30c149f2f125457d' ] = array (
	'url' => 'http://www.theglobeandmail.com/globe-investor/investment-ideas/?service=rss', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'd32a29359f6b0516950169eea9274704' ] = array (
	'url' => 'http://feeds.feedburner.com/ITSecurity_co_uk', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '0aecd46aa94ffda992bed2c8e6371210' ] = array (
	'url' => 'http://feeds2.feedburner.com/RogersInfosecBlog', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'f4892d5e3a260addabe40000bf1505f2' ] = array (
	'url' => 'http://www.bankofcanada.ca/content_type/publications/feed', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '8af1c9ff613c98b3a0d188f964ee858e' ] = array (
	'url' => 'http://threatpost.com/feed', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '2faa4dbf967381e3786fad91f2da30f5' ] = array (
	'url' => 'http://feeds.feedburner.com/andrewhayca', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '1d7676c6397579a80e119c137cab0034' ] = array (
	'url' => 'http://www.economist.com/topics/economics/index.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '9e72e78c7f73bf735f6b56996117dc67' ] = array (
	'url' => 'https://facebook.github.io/react/feed.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '84bba1ac7640a5ae8c79b46464fc56d9' ] = array (
	'url' => 'https://www.wired.com/category/security/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'f240820418cfd488606d814ca431818f' ] = array (
	'url' => 'http://krebsonsecurity.com/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '57b4ca9281a2171be00d2345c1907b7b' ] = array (
	'url' => 'http://feeds.feedburner.com/securityweekly/XBIC', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '4f4cd1097038fe4d475087578bc98419' ] = array (
	'url' => 'https://flypchart.co/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'abdb83c4717546122ea6ff9096e7bab8' ] = array (
	'url' => 'https://www.leadingagile.com/blog/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '6d7c4e90baa2b4d53d0b04391de186e5' ] = array (
	'url' => 'https://www.youtube.com/feeds/videos.xml?channel_id=UCAuUUnT6oDeKwE6v1NGQxug', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '198d527828b69530dca1f9a30b39a1bf' ] = array (
	'url' => 'https://medium.com/feed/free-code-camp/tagged/data-science', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'e2b6c9401540e54ec13b1d27f1212735' ] = array (
	'url' => 'https://www.helpnetsecurity.com/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '0ab52621a7deb6f5b3b6c6db4137eea0' ] = array (
	'url' => 'http://feeds.feedburner.com/auth0', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '57c6ffd1b98c0a4dc9a02c5835d8b641' ] = array (
	'url' => 'http://news.mit.edu/rss/research', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '57a0ef5d23bead4a5fe9145a22c7d15b' ] = array (
	'url' => 'https://us6.campaign-archive.com/feed?u=84bd941af2f1389b9b88c66bb&id=3391a19d97', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '2ebb036abf88c2840e3f447e04429bfe' ] = array (
	'url' => 'http://blog.risingstack.com/rss/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'bc5526f6bdb8a479173d58b751c026aa' ] = array (
	'url' => 'http://googleonlinesecurity.blogspot.com/atom.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '069fc87ac136816af3728dbaa04ef16e' ] = array (
	'url' => 'http://www.bankofcanada.ca/content_type/research/feed', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'c802c75060e441d4679d8b78c1331f37' ] = array (
	'url' => 'http://www.theglobeandmail.com/technology/?service=rss', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'c54765b639e2a117d73030f73bc2692d' ] = array (
	'url' => 'http://feeds.feedburner.com/Securityweek', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'd2d9ddc1de4963b223ef54fd7cb3b661' ] = array (
	'url' => 'http://nakedsecurity.sophos.com/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'f4fef4289b8f44cb551f0ebc7c78e604' ] = array (
	'url' => 'http://javascriptweekly.com/rss/1pf092oe', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'ea29d390984536f90ec831d39fc2a2b6' ] = array (
	'url' => 'http://feeds.feedburner.com/BusinessInsightsInVirtualizationAndCloudSecurity', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '2007be96ac1f3ea790b69fcf13f0b87e' ] = array (
	'url' => 'http://feeds.reuters.com/reuters/scienceNews', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '551f70ad7dd0df27713ab9b8bbc03f8f' ] = array (
	'url' => 'http://feeds.feedburner.com/sciencealert-latestnews', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'f944c509fb5581d1f881f493ec9bc52d' ] = array (
	'url' => 'http://news.mit.edu/rss/feed', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '7f140d86c3d4600c23c78d8229f4d4d4' ] = array (
	'url' => 'https://taosecurity.blogspot.com/feeds/posts/default', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'df49127a350b03637b7f8ab6f535099a' ] = array (
	'url' => 'http://feeds.feedburner.com/SecurityIntelligence', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '346a1bcfe350fe2a1953cd7fbdc86186' ] = array (
	'url' => 'http://feeds.reuters.com/reuters/technologyNews', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '909ccb892d4611329a7f9eeb26cf4953' ] = array (
	'url' => 'http://feeds.feedburner.com/blogspot/CqwP', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'c07a0b3240b0762abcc7ace3fd8831d3' ] = array (
	'url' => 'http://www.itnews.com.au/RSS/rss.ashx?type=Category&ID=406', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'e8bfa07c35773bc4b60b2dff07824b53' ] = array (
	'url' => 'http://feeds.feedburner.com/NoticeBored', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '198d527828b69530dca1f9a30b39a1bf' ] = array (
	'url' => 'https://medium.com/feed/free-code-camp/tagged/data-science', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '57c6ffd1b98c0a4dc9a02c5835d8b641' ] = array (
	'url' => 'http://news.mit.edu/rss/research', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '57a0ef5d23bead4a5fe9145a22c7d15b' ] = array (
	'url' => 'https://us6.campaign-archive.com/feed?u=84bd941af2f1389b9b88c66bb&id=3391a19d97', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'df49127a350b03637b7f8ab6f535099a' ] = array (
	'url' => 'http://feeds.feedburner.com/SecurityIntelligence', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '6d12d566fe5ec4efadf01890d0b5a9ac' ] = array (
	'url' => 'http://ideas.ted.com/feed', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'f240820418cfd488606d814ca431818f' ] = array (
	'url' => 'http://krebsonsecurity.com/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '91302dcb6b1f16a437e847925003f414' ] = array (
	'url' => 'https://blog.sqreen.io/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'abdb83c4717546122ea6ff9096e7bab8' ] = array (
	'url' => 'https://www.leadingagile.com/blog/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'a2a1684232281c329b742507606c65ab' ] = array (
	'url' => 'http://www.povertyactionlab.org/news.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'd8fba6b96618ae688ff4f57f47ff5265' ] = array (
	'url' => 'https://www.huffingtonpost.com/topic/good-news/feed', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '17ee1349cee17db12b4cafc5ce2e8826' ] = array (
	'url' => 'https://www.goodnewsnetwork.org/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '6d12d566fe5ec4efadf01890d0b5a9ac' ] = array (
	'url' => 'http://ideas.ted.com/feed', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '524cbaea9042cab12de9847686043d52' ] = array (
	'url' => 'http://ideas.ted.com/feed/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '25537e62728649f6ed9705f0388ad3c0' ] = array (
	'url' => 'http://www.twis.org/feed', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '13fd187fbd04ecff369c1fb3a8fac1e6' ] = array (
	'url' => 'http://feeds.soundcloud.com/users/soundcloud:users:237456957/sounds.rss', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '896b3e29e9af57f159048f15cff11df3' ] = array (
	'url' => 'http://www.sciencemag.org/rss/podcast.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'e355445cdc48c239b15cf6ef32c7c391' ] = array (
	'url' => 'http://feeds.feedburner.com/ProgrammingThrowdown', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'f0f4db831df8e56bcb7d53d44ccd403f' ] = array (
	'url' => 'http://feeds.feedburner.com/freakonomicsradio', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '2990fd7199a45e70075c0ddd53d8bded' ] = array (
	'url' => 'https://www.theguardian.com/science/series/science/podcast.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '222fae1fab2ad7c525d3a193cab6550e' ] = array (
	'url' => 'https://podcasts.jellystyle.com/mobilecouch/rss', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '2c612c1e72ab8f54f4019208e6c1c464' ] = array (
	'url' => 'http://leoville.tv/podcasts/twit.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '56200effeaaf322de1f933f85cc00c9e' ] = array (
	'url' => 'http://www.sciencefriday.com/audio/scifriaudio.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '3cfac920d9a242d783fe4f8f77f5040a' ] = array (
	'url' => 'http://learningmachines101.libsyn.com/rss', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '498625658ff48b078ddb4813891042ed' ] = array (
	'url' => 'http://www.phpclasses.org/blog/category/podcast/post/latest.rss', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '6bb560405f6384626dd56d8304d6438e' ] = array (
	'url' => 'https://feeds.feedwrench.com/JavaScriptJabber.rss', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'b9b59bb344d7871e242fed395f00d163' ] = array (
	'url' => 'http://feeds.feedburner.com/InformationIsBeautiful', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'a5b59b9c38451337cf343ce9cce5206b' ] = array (
	'url' => 'http://feeds.podtrac.com/q8s8ba9YtM6r', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '00af6eca9567a490fe62860611c87bcc' ] = array (
	'url' => 'http://www.npr.org/rss/rss.php?id=93559255', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'aa8f858845d6292c0846f4bd35ab280b' ] = array (
	'url' => 'http://thewebplatform.libsyn.com/rss', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '13a584a9bbd4abef135ef32f785325ae' ] = array (
	'url' => 'http://www.thenakedscientists.com/feed/7/rss.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '1e5117d7a88198dacc2a5db967a8780f' ] = array (
	'url' => 'https://reactjsnews.com/feed.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '94ac90e6d548f6fe138a1ff125ff02c3' ] = array (
	'url' => 'http://feeds.feedwrench.com/react-native-radio.rss', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '33d8916be2a7cb6bea9a39afd4dfe08e' ] = array (
	'url' => 'http://digital-magic.tv/digitalplanet/thepositivemind/podcast.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '378f6bcda9d961996e03fed34fb868b8' ] = array (
	'url' => 'http://www.npr.org/rss/podcast.php?id=510298', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '944de0f29427362ab9771d9035cab94d' ] = array (
	'url' => 'http://www.mindhacks.com/atom.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '93aa026c17ea7773e7997b0fb437c9d2' ] = array (
	'url' => 'http://www.startalkradio.net/feed/shows/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'a3861d8c2219a31d2d5a3dd1c2975d4d' ] = array (
	'url' => 'http://www.physicscentral.com/about/feed/podcasts.cfm?outputXML', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'bbb2c41d13f028f7c83784d5430d3012' ] = array (
	'url' => 'http://feeds.5by5.tv/webahead', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '494726446065df78bea6991fb3552cc8' ] = array (
	'url' => 'https://facebook.github.io/react-native/blog/feed.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '5aec0477b0ee1d1beda0f45e0c798450' ] = array (
	'url' => 'http://www.cbc.ca/podcasting/includes/quirksaio.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '859f22a1beef400247aea56897bc4c28' ] = array (
	'url' => 'http://datastori.es/feed/podcast/', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '171225707a0073453dbe3969422b5280' ] = array (
	'url' => 'http://simpleprogrammer.libsyn.com/rss', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'feba9bcb90c64467dba3d2a74f9f1bf0' ] = array (
	'url' => 'http://rss.sciam.com/sciam/60secsciencepodcast', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'c10170bf843057fd8b0b60330d6cb6af' ] = array (
	'url' => 'http://feeds.feedburner.com/developertea', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '04de03aa9eeb5da8956aee2a85d9d65a' ] = array (
	'url' => 'https://www.oreilly.com/topics/web-programming/feed.atom', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '0565eeefc862c8a75674c7f76fbd291c' ] = array (
	'url' => 'http://feeds.feedburner.com/SoftSkillsEngineering', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '4f515a1ccb27ca8c5481b67ec7f4e19d' ] = array (
	'url' => 'http://feeds.nature.com/nature/podcast/current', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ '27b6304fbb6d7f5e574a71408593f701' ] = array (
	'url' => 'http://leoville.tv/podcasts/sn.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'd027ae0c3bde518f6b4032ba5f7dcc59' ] = array (
	'url' => 'http://feeds.feedburner.com/NodeUp', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'bf5e59cc7294f7fa9a66d21fd9167469' ] = array (
	'url' => 'http://feeds.feedburner.com/se-radio', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'c8a6cb3aa600b14a2d87a1f75adaa200' ] = array (
	'url' => 'http://feeds.harvardbusiness.org/harvardbusiness/exponential-view', 
	'date_added' => '', 
	//todo more data here
);

/**
$tmp[ '583f9feea885f78a7eeb2165d0662cc9' ] = array (
	'url' => 'https://isc.sans.edu/dailypodcast.xml', 
	'date_added' => '', 
	//todo more data here
);

$tmp[ 'de4aa415c55f5fb000a7780ecc463f90' ] = array (
	'url' => 'http://simplecast.fm/podcasts/279/rss', 
	'date_added' => '', 
	//todo more data here
);

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






























