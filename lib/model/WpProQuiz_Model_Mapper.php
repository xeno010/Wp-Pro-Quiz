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
	
	/**
	 * @var string
	 */
	protected $_tableQuestion;
	protected $_tableMaster;
	
	
	function __construct() {
		global $wpdb;
		
		$this->_wpdb = $wpdb;
		$this->_prefix = $wpdb->prefix.'wp_pro_quiz_';
		
		$this->_tableQuestion = $this->_prefix.'question';
		$this->_tableMaster = $this->_prefix.'master';
	}
}