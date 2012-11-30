<?php
/*
Plugin Name: WP-Pro-Quiz
Plugin URI: http://wordpress.org/extend/plugins/wp-pro-quiz
Description: A powerful and beautiful quiz plugin for WordPress.
Version: 0.7
Author: Julius Fischer
Author URI: http://www.it-gecko.de
*/

define('WPPROQUIZ_VERSION', '0.7');

include_once 'lib/controller/WpProQuiz_Controller_Admin.php';
include_once 'lib/helper/WpProQuiz_Helper_DbUpgrade.php';

register_activation_hook(__FILE__, array('WpProQuiz_Controller_Admin', 'install'));

add_action('plugins_loaded', array('WpProQuiz_Controller_Admin', 'install'));

load_plugin_textdomain('wp-pro-quiz', false, dirname(plugin_basename(__FILE__)).'/languages');

if(is_admin()) {
	new WpProQuiz_Controller_Admin(dirname(__FILE__));
} else {
	require_once 'lib/controller/WpProQuiz_Controller_Front.php';
	
	new WpProQuiz_Controller_Front(dirname(__FILE__));
}