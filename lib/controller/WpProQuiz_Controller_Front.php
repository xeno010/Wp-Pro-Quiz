<?php
class WpProQuiz_Controller_Front {
	
	private $_plugin_dir;
	private $_plugin_file;
	
	public function __construct($plugin_dir) {
		$this->_plugin_dir = $plugin_dir;
		$this->_plugin_file = $this->_plugin_dir.'/wp-pro-quiz.php';
		
		spl_autoload_register(array($this, 'autoload'));
		
		add_action('wp_enqueue_scripts', array($this, 'loadDefaultScripts'));
		add_shortcode('WpProQuiz', array($this, 'shortcode'));
	}
	
	public function loadDefaultScripts() {
		wp_enqueue_script('jquery');
		
		wp_enqueue_style(
			'wpProQuiz_front_style', 
			plugins_url('css/wpProQuiz_front.min.css', $this->_plugin_file),
			array(),
			WPPROQUIZ_VERSION
		);
	}
	
	public function shortcode($attr) {
		$id = $attr[0];
		$content = '';
		
		wp_enqueue_script(
			'wpProQuiz_front_javascript', 
			plugins_url('js/wpProQuiz_front.min.js', $this->_plugin_file),
			array('jquery-ui-sortable'),
			WPPROQUIZ_VERSION
		);
		
		
		if(is_numeric($id)) {
			ob_start();
			
			$this->handleShortCode($id);
			
			$content = ob_get_contents();
			
			ob_end_clean();
		}
		
		return $content;
	}
	
	public function handleShortCode($id) {
		$view = new WpProQuiz_View_FrontQuiz();
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		
		$quiz = $quizMapper->fetch($id);
		$question = $questionMapper->fetchAll($id);
		
		if(empty($quiz) || empty($question))
			echo '';
		
		$view->quiz = $quiz;
		$view->question = $question;
		
		$view->show();		
	}
	
	public function autoload($class) {
		$c = explode("_", $class);
	
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
			case 'Controller':
				$dir = 'controller';
				break;
		}
	
		if(file_exists($this->_plugin_dir.'/lib/'.$dir.'/'.$class.'.php'))
			include_once $this->_plugin_dir.'/lib/'.$dir.'/'.$class.'.php';
	}
}