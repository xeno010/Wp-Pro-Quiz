<?php
/*
Plugin Name: WP-Pro-Quiz
Plugin URI: http://wordpress.org/extend/plugins/wp-pro-quiz
Description: A powerful and beautiful quiz plugin for WordPress.
Version: 0.28
Author: Julius Fischer
Author URI: http://www.it-gecko.de
Text Domain: wp-pro-quiz
Domain Path: /languages
*/

define('WPPROQUIZ_VERSION', '0.28');

define('WPPROQUIZ_DEV', false);

define('WPPROQUIZ_PATH', dirname(__FILE__));
define('WPPROQUIZ_URL', plugins_url('', __FILE__));
define('WPPROQUIZ_FILE', __FILE__);
define('WPPROQUIZ_PPATH', dirname(plugin_basename(__FILE__)));
define('WPPROQUIZ_PLUGIN_PATH', WPPROQUIZ_PATH.'/plugin');

$uploadDir = wp_upload_dir();

define('WPPROQUIZ_CAPTCHA_DIR', $uploadDir['basedir'].'/wp_pro_quiz_captcha');
define('WPPROQUIZ_CAPTCHA_URL', $uploadDir['baseurl'].'/wp_pro_quiz_captcha');

spl_autoload_register('wpProQuiz_autoload');

register_activation_hook(__FILE__, array('WpProQuiz_Helper_Upgrade', 'upgrade'));

add_action('plugins_loaded', 'wpProQuiz_pluginLoaded');

if(is_admin()) {
	new WpProQuiz_Controller_Admin();
} else {
	new WpProQuiz_Controller_Front();
}

function wpProQuiz_autoload($class) {
	$c = explode('_', $class);

	if($c === false || count($c) != 3 || $c[0] !== 'WpProQuiz')
		return;

	$dir = '';

	switch ($c[1]) {
		case 'View':
			$dir = 'view';
			break;
		case 'Model':
			$dir = 'model';
			break;
		case 'Helper':
			$dir = 'helper';
			break;
		case 'Controller':
			$dir = 'controller';
			break;
		case 'Plugin':
			$dir = 'plugin';
			break;
		default:
			return;
	}

	if(file_exists(WPPROQUIZ_PATH.'/lib/'.$dir.'/'.$class.'.php'))
		include_once WPPROQUIZ_PATH.'/lib/'.$dir.'/'.$class.'.php';
}

function wpProQuiz_pluginLoaded() {
	
	load_plugin_textdomain('wp-pro-quiz', false, WPPROQUIZ_PPATH.'/languages');
	
	if(get_option('wpProQuiz_version') !== WPPROQUIZ_VERSION) {
		WpProQuiz_Helper_Upgrade::upgrade();
	}
	
	
	
// 	//ACHIEVEMENTS Version 2.x.x
// 	if(defined('ACHIEVEMENTS_IS_INSTALLED') && ACHIEVEMENTS_IS_INSTALLED === 1 && defined('ACHIEVEMENTS_VERSION')) {
// 		$version = ACHIEVEMENTS_VERSION;
// 		if($version{0} == '2') {
// 			new WpProQuiz_Plugin_BpAchievementsV2();
// 		}
// 	}

	
}

function wpProQuiz_achievementsV3() {
	achievements()->extensions->wp_pro_quiz = new WpProQuiz_Plugin_BpAchievementsV3();

	do_action('wpProQuiz_achievementsV3');
}

add_action('dpa_ready', 'wpProQuiz_achievementsV3');

// //ACHIEVEMENTS Version 2.x.x
// $bpAchievementsV2_path = realpath(ABSPATH.PLUGINDIR.'/achievements/loader.php');

// if($bpAchievementsV2_path !== false) {
// 	register_deactivation_hook($bpAchievementsV2_path, array('WpProQuiz_Plugin_BpAchievementsV2', 'deinstall'));
// 	register_activation_hook($bpAchievementsV2_path, array('WpProQuiz_Plugin_BpAchievementsV2', 'install'));
// }
