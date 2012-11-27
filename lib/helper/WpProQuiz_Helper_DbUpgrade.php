<?php
class WpProQuiz_Helper_DbUpgrade {
	
	const WPPROQUIZ_DB_VERSION = 7;
	
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
				  `question_random` tinyint(1) NOT NULL,
				  `answer_random` tinyint(1) NOT NULL,
				  `check_answer` tinyint(1) NOT NULL,
				  `back_button` tinyint(1) NOT NULL,
				  `time_limit` int(11) NOT NULL,
				  `statistics_on` tinyint(1) NOT NULL,
				  `statistics_ip_lock` int(10) unsigned NOT NULL,
				  `show_points` tinyint(1) NOT NULL,
				  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
				
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
				  PRIMARY KEY (`id`),
				  KEY `quiz_id` (`quiz_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
		');
		
		$this->_wpdb->query('
			CREATE TABLE IF NOT EXISTS `'.$this->_wpdb->prefix.'wp_pro_quiz_lock` (
				`quiz_id` int(11) NOT NULL,
				`lock_ip` varchar(100) NOT NULL,
				`lock_date` int(11) NOT NULL,
				PRIMARY KEY (`quiz_id`,`lock_ip`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
}