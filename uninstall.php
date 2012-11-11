<?php

if(!defined('WP_UNINSTALL_PLUGIN'))
	exit();

include_once 'lib/helper/WpProQuiz_Helper_DbUpgrade.php';

$db = new WpProQuiz_Helper_DbUpgrade();
$db->delete();

delete_option('wpProQuiz_dbVersion');