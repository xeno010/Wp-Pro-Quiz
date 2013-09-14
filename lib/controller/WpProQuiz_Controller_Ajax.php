<?php
/**
 * @since 0.23 
 */
class WpProQuiz_Controller_Ajax {
	
	private $_adminCallbacks = array();
	private $_frontCallbacks = array();
	
	public function init() {
		$this->initCallbacks();

		add_action('wp_ajax_wp_pro_quiz_admin_ajax', array($this, 'adminAjaxCallback'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_admin_ajax', array($this, 'frontAjaxCallback'));
	}
	
	public function adminAjaxCallback() {
		$this->ajaxCallbackHandler(true);
	}
	
	public function frontAjaxCallback() {
		$this->ajaxCallbackHandler(false);
	}
	
	private function ajaxCallbackHandler($admin) {
		$func 	= isset($_POST['func']) ? $_POST['func'] : '';
		$data 	= isset($_POST['data']) ? $_POST['data'] : null;
		$calls = $admin ? $this->_adminCallbacks : $this->_frontCallbacks;
		
		if(isset($calls[$func])) {
			$r = call_user_func($calls[$func], $data, $func);
			
			if($r !== null)
				echo $r;
		}
		
		exit;
	}
	
	private function initCallbacks() {
		$this->_adminCallbacks = array(
			'categoryAdd' => array('WpProQuiz_Controller_Category', 'ajaxAddCategory'),
			'categoryDelete' => array('WpProQuiz_Controller_Category', 'ajaxDeleteCategory'),
			'categoryEdit' => array('WpProQuiz_Controller_Category', 'ajaxEditCategory'),
				
			'statisticLoad' => array('WpProQuiz_Controller_Statistics', 'ajaxLoadStatistic'), /** @deprecated **/
			'statisticLoadOverview' => array('WpProQuiz_Controller_Statistics', 'ajaxLoadStatsticOverview'), /** @deprecated **/
			'statisticReset' => array('WpProQuiz_Controller_Statistics', 'ajaxReset'), /** @deprecated **/
			'statisticLoadFormOverview' => array('WpProQuiz_Controller_Statistics', 'ajaxLoadFormOverview'), /** @deprecated **/
				
			'statisticLoadHistory' => array('WpProQuiz_Controller_Statistics', 'ajaxLoadHistory'),
			'statisticLoadUser' => array('WpProQuiz_Controller_Statistics', 'ajaxLoadStatisticUser'),
			'statisticResetNew' => array('WpProQuiz_Controller_Statistics', 'ajaxRestStatistic'),
			'statisticLoadOverviewNew' => array('WpProQuiz_Controller_Statistics', 'ajaxLoadStatsticOverviewNew'),
				
			'templateEdit' => array('WpProQuiz_Controller_Template', 'ajaxEditTemplate'),
			'templateDelete' => array('WpProQuiz_Controller_Template', 'ajaxDeleteTemplate'),
				
			'quizLoadData' => array('WpProQuiz_Controller_Front', 'ajaxQuizLoadData')
		);
		
		//nopriv
		$this->_frontCallbacks = array(
			'quizLoadData' => array('WpProQuiz_Controller_Front', 'ajaxQuizLoadData')
		);
	}
}