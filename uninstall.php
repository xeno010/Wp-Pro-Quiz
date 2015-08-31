<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

include_once 'lib/helper/WpProQuiz_Helper_DbUpgrade.php';

$db = new WpProQuiz_Helper_DbUpgrade();
$db->delete();

delete_option('wpProQuiz_dbVersion');
delete_option('wpProQuiz_version');

delete_option('wpProQuiz_addRawShortcode');
delete_option('wpProQuiz_jsLoadInHead');
delete_option('wpProQuiz_touchLibraryDeactivate');
delete_option('wpProQuiz_corsActivated');
delete_option('wpProQuiz_toplistDataFormat');
delete_option('wpProQuiz_emailSettings');
delete_option('wpProQuiz_statisticTimeFormat');