<?php
/**
 * @since 0.23 
 */
class WpProQuiz_Controller_Ajax {
	
	private $_callbacks = array();
	
	public function init() {
		$this->initCallbacks();
		
		add_action('wp_ajax_wp_pro_quiz_admin_ajax', array($this, 'adminAjaxCallback'));
	}
	
	public function adminAjaxCallback() {
		$func = isset($_POST['func']) ? $_POST['func'] : '';
		$data = isset($_POST['data']) ? $_POST['data'] : null;
		
		if(isset($this->_callbacks[$func])) {
			echo call_user_func($this->_callbacks[$func], $data, $func);
		}
		
		exit;
	}
	
	private function initCallbacks() {
		$this->_callbacks = array(
			'categoryAdd' => array('WpProQuiz_Controller_Category', 'ajaxAddCategory'),
			'categoryDelete' => array('WpProQuiz_Controller_Category', 'ajaxDeleteCategory'),
			'categoryEdit' => array('WpProQuiz_Controller_Category', 'ajaxEditCategory'),
			'statisticLoad' => array('WpProQuiz_Controller_Statistics', 'ajaxLoadStatistic'),
			'statisticLoadOverview' => array('WpProQuiz_Controller_Statistics', 'ajaxLoadStatsticOverview'),
			'statisticReset' => array('WpProQuiz_Controller_Statistics', 'ajaxReset')
		);
	}
}