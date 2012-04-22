<?php

define('zoop_dir', dirname(__file__) . '/../zoop-budget');
define('app_dir', dirname(__file__));
//define_once('app_status', 'live');
//define('logprofile', 'print');
//define('app_url_rewrite', true);
//ini_set('include_path',ini_get('include_path').':'. zoop_dir . '/lib/pear:'); // FOR UNIX


//define('app_status', 'dev');
//define('app_temp_dir', dirname(__file__) . '/tmp');
$secretCodes[] = '191';
//*
define('db_RDBMS', 'pgsql');
define('db_Server', 'localhost');
define('db_Port', '5432');
define('db_Username', 'postgres');
define('db_Password', '');
define('db_Database', 'zbudget');

define('session_type', 'files');
//define('session_server', 'localhost');

define('app_error_reporting', E_ALL & ~E_WARNING | E_NOTICE);

//define('LOG_FILE', 'LOG_FILE.html');
//define('LOG_FILE', 'php://output');
define('app_temp_dir', app_dir . '/tmp');

//*/
/*
define('db_RDBMS', 'mysqli');
define('db_Server', 'localhost');
define('db_Port', 3306);
define('db_Username', 'steve');
define('db_Password', 'redhat');
define('db_Database', 'budget');
*/

