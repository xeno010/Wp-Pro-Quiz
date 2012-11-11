<?php
class WpProQuiz_Model_Mapper {
	/**
	 * Wordpress Datenbank Object 
	 * @var wpdb
	 */
	protected $_wpdb;
	
	/**
	 * @var string
	 */
	protected $_prefix;
	
	function __construct() {
		global $wpdb;
		
		$this->_wpdb = $wpdb;
		$this->_prefix = $wpdb->prefix.'wp_pro_quiz_';
	}
}