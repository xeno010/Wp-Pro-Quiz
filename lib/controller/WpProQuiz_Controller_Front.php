<?php
class WpProQuiz_Controller_Front {
		
	/**
	 * @var WpProQuiz_Model_GlobalSettings
	 */
	private $_settings = null;
	
	public function __construct() {
		$this->loadSettings();
				
		add_action('wp_enqueue_scripts', array($this, 'loadDefaultScripts'));
		add_shortcode('WpProQuiz', array($this, 'shortcode'));
	}
	
	public function loadDefaultScripts() {
		wp_enqueue_script('jquery');
		
		wp_enqueue_style(
			'wpProQuiz_front_style', 
			plugins_url('css/wpProQuiz_front.min.css', WPPROQUIZ_FILE),
			array(),
			WPPROQUIZ_VERSION
		);
		
		if($this->_settings->isJsLoadInHead()) {
			$this->loadJsScripts(false);
		}
	}
	
	private function loadJsScripts($footer = true) {
		wp_enqueue_script(
			'wpProQuiz_front_javascript',
			plugins_url('js/wpProQuiz_front.min.js', WPPROQUIZ_FILE),
			array('jquery-ui-sortable'),
			WPPROQUIZ_VERSION,
			$footer
		);
		
		if(!$this->_settings->isTouchLibraryDeactivate()) {
			wp_enqueue_script(
				'jquery-ui-touch-punch',
				plugins_url('js/jquery.ui.touch-punch.min.js', WPPROQUIZ_FILE),
				array('jquery-ui-sortable'),
				'0.2.2',
				$footer
			);
		}
	}
	
	public function shortcode($attr) {
		$id = $attr[0];
		$content = '';
		
		if(!$this->_settings->isJsLoadInHead()) {
			$this->loadJsScripts();
		}

		if(is_numeric($id)) {
			ob_start();
			
			$this->handleShortCode($id);
			
			$content = ob_get_contents();
			
			ob_end_clean();
		}

		if($this->_settings->isAddRawShortcode()) {
			return '[raw]'.$content.'[/raw]';
		} 
		
		return $content;
	}
	
	public function handleShortCode($id) {
		$view = new WpProQuiz_View_FrontQuiz();
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		
		$quiz = $quizMapper->fetch($id);
		$question = $questionMapper->fetchAll($id);
		
		if(empty($quiz) || empty($question)) {			
			echo '';
			
			return;
		}
		
		$view->quiz = $quiz;
		$view->question = $question;
		
		$view->show();		
	}
	
	private function loadSettings() {
		$mapper = new WpProQuiz_Model_GlobalSettingsMapper();
		
		$this->_settings = $mapper->fetchAll();
	}
}