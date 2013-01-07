<?php

if(!defined('WP_UNINSTALL_PLUGIN'))
	exit();

include_once 'lib/helper/WpProQuiz_Helper_DbUpgrade.php';
include_once 'lib/model/WpProQuiz_Model_GlobalSettingsMapper.php';

$db = new WpProQuiz_Helper_DbUpgrade();
$db->delete();

delete_option('wpProQuiz_dbVersion');
delete_option('wpProQuiz_version');

$settings = new WpProQuiz_Model_GlobalSettingsMapper();
$settings->delete();
