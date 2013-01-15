<?php
class WpProQuiz_Helper_Upgrade {
		
	public static function upgrade() {
		
		WpProQuiz_Helper_Upgrade::updateDb();
		
		$oldVersion = get_option('wpProQuiz_version');

		switch($oldVersion) {
			case '0.17':
				break;
			default:
				WpProQuiz_Helper_Upgrade::install();
				break;
		}
		
		if(add_option('wpProQuiz_version', WPPROQUIZ_VERSION) === false) {
			update_option('wpProQuiz_version', WPPROQUIZ_VERSION);
		}
	}
	
	private static function install() {
		$role = get_role('administrator');
		
		$role->add_cap('wpProQuiz_show');
		$role->add_cap('wpProQuiz_add_quiz');
		$role->add_cap('wpProQuiz_edit_quiz');
		$role->add_cap('wpProQuiz_delete_quiz');
		$role->add_cap('wpProQuiz_show_statistics');
		$role->add_cap('wpProQuiz_reset_statistics');
		$role->add_cap('wpProQuiz_import');
		$role->add_cap('wpProQuiz_export');
		$role->add_cap('wpProQuiz_change_settings');

		//ACHIEVEMENTS Version 2.x.x
		if(defined('ACHIEVEMENTS_IS_INSTALLED') && ACHIEVEMENTS_IS_INSTALLED === 1 && defined('ACHIEVEMENTS_VERSION')) {
			$version = ACHIEVEMENTS_VERSION;
			if($version{0} == '2') {
				WpProQuiz_Plugin_BpAchievementsV2::install();
			}
		}
	}
	
	private static function updateDb() {
		$db = new WpProQuiz_Helper_DbUpgrade();
		$v = $db->upgrade(get_option('wpProQuiz_dbVersion', false));
		
		if(add_option('wpProQuiz_dbVersion', $v) === false)
			update_option('wpProQuiz_dbVersion', $v);
	}
	
	public static function deinstall() {
		
	}
}