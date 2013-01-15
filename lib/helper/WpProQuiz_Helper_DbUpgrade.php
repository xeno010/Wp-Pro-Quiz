<?php
class WpProQuiz_Helper_DbUpgrade {
	
	const WPPROQUIZ_DB_VERSION = 13;
	
	private $_wpdb;
	private $_prefix;

	public function __construct() {
		global $wpdb;
		
		$this->_wpdb = $wpdb;
	}
	
	public function upgrade($version) {
		
		if($version === false || ((int)$version) > WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION) {
			$this->install();
			return WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION;
		}
		
		$version = (int) $version;
		
		if($version === WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION)
			return WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION;
				
		do {
			$f = 'upgradeDbV'.$version;
			
			if(method_exists($this, $f)) {
				$version = $this->$f();
			} else {
				die("WpProQuiz upgrade error");
			}
		} while ($version < WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION);
		
		return WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION;
	}
	
	public function delete() {
		$this->_wpdb->query('DROP TABLE IF EXISTS `'.$this->_wpdb->prefix.'wp_pro_quiz_master`');
		$this->_wpdb->query('DROP TABLE IF EXISTS `'.$this->_wpdb->prefix.'wp_pro_quiz_question`');
		$this->_wpdb->query('DROP TABLE IF EXISTS `'.$this->_wpdb->prefix.'wp_pro_quiz_lock`');
		$this->_wpdb->query('DROP TABLE IF EXISTS `'.$this->_wpdb->prefix.'wp_pro_quiz_statistic`');
	}
	
	private function install() {
		
		$this->delete();
		
		$this->_wpdb->query('
			CREATE TABLE IF NOT EXISTS `'.$this->_wpdb->prefix.'wp_pro_quiz_master` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(200) NOT NULL,
				  `text` text NOT NULL,
				  `result_text` text NOT NULL,
				  `result_grade_enabled` tinyint(1) NOT NULL,
				  `title_hidden` tinyint(1) NOT NULL,
				  `btn_restart_quiz_hidden` tinyint(1) NOT NULL,
				  `btn_view_question_hidden` tinyint(1) NOT NULL,
				  `question_random` tinyint(1) NOT NULL,
				  `answer_random` tinyint(1) NOT NULL,
				  `check_answer` tinyint(1) NOT NULL,
				  `back_button` tinyint(1) NOT NULL,
				  `time_limit` int(11) NOT NULL,
				  `statistics_on` tinyint(1) NOT NULL,
				  `statistics_ip_lock` int(10) unsigned NOT NULL,
				  `show_points` tinyint(1) NOT NULL,
				  `quiz_run_once` tinyint(1) NOT NULL,
				  `quiz_run_once_type` tinyint(4) NOT NULL,
				  `quiz_run_once_cookie` tinyint(1) NOT NULL,
				  `quiz_run_once_time` int(10) unsigned NOT NULL,
				  `question_on_single_page` tinyint(1) NOT NULL,
				  `numbered_answer` tinyint(1) NOT NULL,
				  `hide_answer_message_box` tinyint(1) NOT NULL,
				  `disabled_answer_mark` tinyint(1) NOT NULL,
				  `show_max_question` tinyint(1) NOT NULL,
				  `show_max_question_value` int(10) unsigned NOT NULL,
				  `show_max_question_percent` tinyint(1) NOT NULL,
				  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
		');
		
		$this->_wpdb->query('
			CREATE TABLE IF NOT EXISTS `'.$this->_wpdb->prefix.'wp_pro_quiz_question` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `quiz_id` int(11) NOT NULL,
				  `sort` tinyint(3) unsigned NOT NULL,
				  `title` varchar(200) NOT NULL,
				  `points` int(11) NOT NULL,
				  `question` text NOT NULL,
				  `correct_msg` text NOT NULL,
				  `incorrect_msg` text NOT NULL,
				  `correct_same_text` tinyint(1) NOT NULL,
				  `correct_count` int(10) unsigned NOT NULL,
				  `incorrect_count` int(10) unsigned NOT NULL,
				  `tip_enabled` tinyint(1) NOT NULL,
				  `tip_msg` text NOT NULL,
				  `tip_count` int(11) NOT NULL,
				  `answer_type` varchar(50) NOT NULL,
				  `answer_json` text NOT NULL,
				  `points_per_answer` tinyint(1) NOT NULL,
				  `points_answer` int(10) unsigned NOT NULL,
				  `show_points_in_box` tinyint(1) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `quiz_id` (`quiz_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
		');
		
		$this->_wpdb->query('
			CREATE TABLE IF NOT EXISTS `'.$this->_wpdb->prefix.'wp_pro_quiz_lock` (
				  `quiz_id` int(11) NOT NULL,
				  `lock_ip` varchar(100) NOT NULL,
				  `user_id` bigint(20) unsigned NOT NULL,
				  `lock_type` tinyint(3) unsigned NOT NULL,
				  `lock_date` int(11) NOT NULL,
				  PRIMARY KEY (`quiz_id`,`lock_ip`,`user_id`,`lock_type`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		');
		
		$this->_wpdb->query('
			CREATE TABLE IF NOT EXISTS `'.$this->_wpdb->prefix.'wp_pro_quiz_statistic` (
				  `quiz_id` int(11) NOT NULL,
				  `question_id` int(11) NOT NULL,
				  `user_id` bigint(20) unsigned NOT NULL,
				  `correct_count` int(10) unsigned NOT NULL,
				  `incorrect_count` int(10) unsigned NOT NULL,
				  `hint_count` int(10) unsigned NOT NULL,
				  `correct_answer_count` int(10) unsigned NOT NULL,
				  PRIMARY KEY (`quiz_id`,`question_id`,`user_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		');
	}
	
	private function upgradeDbV1() {
		
		$this->_wpdb->query('
			ALTER TABLE `'.$this->_wpdb->prefix.'wp_pro_quiz_master` 
				ADD  `back_button` TINYINT( 1 ) NOT NULL AFTER  `answer_random`,
				ADD  `check_answer` TINYINT( 1 ) NOT NULL AFTER  `answer_random`,
				ADD  `result_text` TEXT NOT NULL AFTER  `text`
		');
		
		return 2;
	}
	
	private function upgradeDbV2() {
		return 3;
	}
	
	private function upgradeDbV3() {
		
		$this->_wpdb->query('
			ALTER TABLE `'.$this->_wpdb->prefix.'wp_pro_quiz_question`
				ADD  `incorrect_count` INT UNSIGNED NOT NULL AFTER  `incorrect_msg` ,
				ADD  `correct_count` INT UNSIGNED NOT NULL AFTER  `incorrect_msg` ,
				ADD  `correct_same_text` TINYINT( 1 ) NOT NULL AFTER  `incorrect_msg`
		');
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_master` 
 				ADD  `statistics_on` TINYINT( 1 ) NOT NULL ,
 				ADD  `statistics_ip_lock` INT UNSIGNED NOT NULL
		');
		
		$this->_wpdb->query('
			CREATE TABLE IF NOT EXISTS `'.$this->_wpdb->prefix.'wp_pro_quiz_lock` (
				`quiz_id` int(11) NOT NULL,
				`lock_ip` varchar(100) NOT NULL,
				`lock_date` int(11) NOT NULL,
				PRIMARY KEY (`quiz_id`,`lock_ip`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		');
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_question` 
				ADD INDEX (  `quiz_id` )
		');
		
		return 4;
	}
	
	private function upgradeDbV4() {
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_question` 
				ADD  `tip_enabled` TINYINT( 1 ) NOT NULL AFTER  `incorrect_count` ,
				ADD  `tip_msg` TEXT NOT NULL AFTER  `tip_enabled` ,
				ADD  `tip_count` INT NOT NULL AFTER  `tip_msg`
		');
				
		return 5;
	}
	
	private function upgradeFixDbV4() {
		if($this->_wpdb->prefix != 'wp_') {
			$this->_wpdb->query('SELECT * FROM `'.$this->_wpdb->prefix.'wp_pro_quiz_question` LIMIT 0,1');
		
			$names = $this->_wpdb->get_col_info('name');
		
			if(!in_array('tip_enabled', $names)) {
				$this->_wpdb->query('ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_question` ADD `tip_enabled` TINYINT( 1 ) NOT NULL AFTER  `incorrect_count`');
			}
		
			if(!in_array('tip_msg', $names)) {
				$this->_wpdb->query('ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_question` ADD `tip_msg` TEXT NOT NULL AFTER  `tip_enabled`');
			}
		
			if(!in_array('tip_count', $names)) {
				$this->_wpdb->query('ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_question` ADD  `tip_count` INT NOT NULL AFTER `tip_msg`');
			}
		}
	}
	
	private function upgradeDbV5() {
		
		$this->upgradeFixDbV4();
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_master`
				ADD  `result_grade_enabled` TINYINT( 1 ) NOT NULL AFTER  `result_text`
		');
		
		return 6;
	}
	
	private function upgradeDbV6() {
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_question`
				ADD  `points` INT NOT NULL AFTER  `title`
		');
		
		$this->_wpdb->query('
			UPDATE `'.$this->_wpdb->prefix.'wp_pro_quiz_question` SET `points` = 1
		');
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_master`
				ADD  `show_points` TINYINT( 1 ) NOT NULL
		');
		
		return 7;
	}
	
	private function upgradeDbV7() {
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_master` 
				CHANGE  `name`  `name` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
				CHANGE  `text`  `text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
				CHANGE  `result_text`  `result_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
		');
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_question` 
				CHANGE  `title`  `title` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
				CHANGE  `question`  `question` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
				CHANGE  `correct_msg`  `correct_msg` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
				CHANGE  `incorrect_msg`  `incorrect_msg` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
				CHANGE  `tip_msg`  `tip_msg` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
				CHANGE  `answer_type`  `answer_type` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
				CHANGE  `answer_json`  `answer_json` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
		');
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_lock` 
				CHANGE  `lock_ip`  `lock_ip` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
		');
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_lock` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
		');
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_master` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
		');
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_question` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
		');
			
		return 8;
	}
	
	private function upgradeDbV8() {
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_master` 
				ADD  `btn_restart_quiz_hidden` TINYINT( 1 ) NOT NULL AFTER  `title_hidden` ,
				ADD  `btn_view_question_hidden` TINYINT( 1 ) NOT NULL AFTER  `btn_restart_quiz_hidden`
		');
		
		return 9;
	}
	
	private function upgradeFixDbV8() {
		if($this->_wpdb->prefix != 'wp_') {
			$this->_wpdb->query('SELECT * FROM `'.$this->_wpdb->prefix.'wp_pro_quiz_master` LIMIT 0,1');
		
			$names = $this->_wpdb->get_col_info('name');
		
			if(!in_array('btn_restart_quiz_hidden', $names)) {
				$this->_wpdb->query('
					ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_master` 
						ADD  `btn_restart_quiz_hidden` TINYINT( 1 ) NOT NULL AFTER  `title_hidden`
				');
			}
		
			if(!in_array('btn_view_question_hidden', $names)) {
				$this->_wpdb->query('
					ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_master` 
						ADD  `btn_view_question_hidden` TINYINT( 1 ) NOT NULL AFTER  `btn_restart_quiz_hidden` 
				');
			}
		}
	}
	
	private function upgradeDbV9() {
		
		$this->upgradeFixDbV8();
		
		$this->_wpdb->query('
			TRUNCATE `'.$this->_wpdb->prefix.'wp_pro_quiz_lock`
		');
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_lock` 
				ADD  `user_id` BIGINT UNSIGNED NOT NULL AFTER  `lock_ip` ,
				ADD  `lock_type` TINYINT UNSIGNED NOT NULL AFTER  `user_id`
		');
		
		$this->_wpdb->query('
			ALTER TABLE `'.$this->_wpdb->prefix.'wp_pro_quiz_lock` 
				DROP PRIMARY KEY ,
				ADD PRIMARY KEY (  `quiz_id` ,  `lock_ip` ,  `user_id` ,  `lock_type` )
		');
				
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_master` 
				ADD  `quiz_run_once` TINYINT( 1 ) NOT NULL ,
				ADD  `quiz_run_once_type` TINYINT NOT NULL ,
				ADD  `quiz_run_once_cookie` TINYINT( 1 ) NOT NULL ,
				ADD  `quiz_run_once_time` INT UNSIGNED NOT NULL 
		');
		
		$this->_wpdb->query('
			CREATE TABLE IF NOT EXISTS `'.$this->_wpdb->prefix.'wp_pro_quiz_statistic` (
				  `quiz_id` int(11) NOT NULL,
				  `question_id` int(11) NOT NULL,
				  `user_id` bigint(20) unsigned NOT NULL,
				  `correct_count` int(10) unsigned NOT NULL,
				  `incorrect_count` int(10) unsigned NOT NULL,
				  `hint_count` int(10) unsigned NOT NULL,
				  PRIMARY KEY (`quiz_id`,`question_id`,`user_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		');
		
		$this->_wpdb->query('
			INSERT INTO `'.$this->_wpdb->prefix.'wp_pro_quiz_statistic` (quiz_id, question_id, user_id, correct_count, incorrect_count, hint_count)
				SELECT
					question.quiz_id, id, 0, question.correct_count, question.incorrect_count, tip_count
				FROM 
					`'.$this->_wpdb->prefix.'wp_pro_quiz_question` as question
				WHERE
					question.correct_count > 0 OR question.incorrect_count > 0 OR tip_count > 0
		');
				
		return 10;
	}
	
	private function upgradeDbV10() {
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_master`
				ADD  `question_on_single_page` TINYINT( 1 ) NOT NULL ,
				ADD  `numbered_answer` TINYINT( 1 ) NOT NULL 
		');
				
		return 11;
	}
	
	private function upgradeDbV11() {
		
		$this->_wpdb->query('
			ALTER TABLE `'.$this->_wpdb->prefix.'wp_pro_quiz_question` 
				ADD  `points_per_answer` TINYINT( 1 ) NOT NULL ,
				ADD  `points_answer` INT UNSIGNED NOT NULL , 
				ADD  `show_points_in_box` TINYINT( 1 ) NOT NULL 
		');
		
		$this->_wpdb->query('
			ALTER TABLE `'.$this->_wpdb->prefix.'wp_pro_quiz_statistic`
				ADD  `correct_answer_count` INT UNSIGNED NOT NULL
		');
		
		$this->_wpdb->query('UPDATE `'.$this->_wpdb->prefix.'wp_pro_quiz_statistic` SET `correct_answer_count` = `correct_count`');
		
		$this->_wpdb->query('UPDATE `'.$this->_wpdb->prefix.'wp_pro_quiz_question` SET `points_answer` = `points`');
		
		return 12;
	}
	
	private function upgradeDbV12() {
		
		$this->_wpdb->query('
			ALTER TABLE  `'.$this->_wpdb->prefix.'wp_pro_quiz_master`
				ADD  `hide_answer_message_box` TINYINT( 1 ) NOT NULL ,
				ADD  `disabled_answer_mark` TINYINT( 1 ) NOT NULL ,
				ADD  `show_max_question` TINYINT( 1 ) NOT NULL ,
				ADD  `show_max_question_value` INT UNSIGNED NOT NULL ,
				ADD  `show_max_question_percent` TINYINT( 1 ) NOT NULL
		');
		
		return 13;
	}
}