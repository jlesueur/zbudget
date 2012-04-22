<?php


include_once(dirname(__file__) . "/config.php");
include_once(zoop_dir . "/zoop.php");
$zoop = new Zoop(app_dir);

$zoop->addComponent('gui');
$zoop->addComponent('zone');
$zoop->addComponent('db');

$zoop->addComponent('forms');

$zoop->addComponent('chart');

//$zoop->addComponent('projax');

$zoop->addZone('default');
$zoop->addZone('expenses');
$zoop->addZone('budget');
$zoop->addZone('register');
$zoop->addZone('accounts');


$zoop->addInclude('category', app_dir . '/objects/category.php');
$zoop->addInclude('expense', app_dir . '/objects/expense.php');
$zoop->addInclude('account', app_dir . '/objects/account.php');
$zoop->addInclude('csvexpenseparser', app_dir . '/objects/csvexpenseparser.php');
$zoop->addInclude('wfexpenseparser', app_dir . '/objects/wfexpenseparser.php');
$zoop->addInclude('mbnaexpenseparser', app_dir . '/objects/mbnaexpenseparser.php');
$zoop->addInclude('capitaloneexpenseparser', app_dir . '/objects/capitaloneexpenseparser.php');
$zoop->addInclude('ofxexpenseparser', app_dir . '/objects/ofxexpenseparser.php');
$zoop->addInclude('user', app_dir . '/objects/user.php');
include_once(app_dir . '/strings/strings.php');
require_once(app_dir . '/misc.php');

$zoop->init();
