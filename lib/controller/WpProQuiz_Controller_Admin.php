<?php
class WpProQuiz_Controller_Admin {
	
	private $_plugin_dir;
	private $_plugin_file;
		
	public function __construct($plugin_dir) {
		spl_autoload_register(array($this, 'autoload'));
		
		$this->_plugin_dir = $plugin_dir;
		$this->_plugin_file = $this->_plugin_dir.'/wp-pro-quiz.php';
		
		add_action('admin_init', array($this, 'upgradePlugin'));
		add_action('wp_ajax_update_sort', array($this, 'route') );
		add_action('admin_menu', array($this, 'register_page'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScript') );
	}
	
	private function localizeScript() {
		$translation_array = array(
			'delete_msg' => __('Do you really want to delete the quiz/question?', 'wp-pro-quiz'),
			'no_title_msg' => __('Title is not filled!', 'wp-pro-quiz'),
			'no_question_msg' => __('No question deposited!', 'wp-pro-quiz'),
			'no_correct_msg' => __('Correct answer was not selected!', 'wp-pro-quiz'),
			'no_answer_msg' => __('No answer deposited!', 'wp-pro-quiz')
		);
		
		wp_localize_script('wpProQuiz_admin_javascript', 'wpProQuizLocalize', $translation_array);
	}
	
	public function enqueueScript() {
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('wpProQuiz_admin_javascript', plugins_url('js/wpProQuiz_admin.min.js', $this->_plugin_file));
		$this->localizeScript();
		
	}
	
	public function upgradePlugin() {
		$db = new WpProQuiz_Helper_DbUpgrade();
		$v = $db->upgrade(get_option('wpProQuiz_dbVersion', false));
		
		add_option('wpProQuiz_dbVersion', $v);
	}
	
	public static function install() {
		$db = new WpProQuiz_Helper_DbUpgrade();
		$v = $db->upgrade(get_option('wpProQuiz_dbVersion', false));
		
		add_option('wpProQuiz_dbVersion', $v);
	}
	
	public function register_page() {
		add_menu_page(
			'WP-Pro-Quiz',
			'WP-Pro-Quiz',
			'administrator',
			'wpProQuiz',
			array($this, 'route'));
	}
	
	public function route() {
		$_POST = stripslashes_deep($_POST);
			
		$module = isset($_GET['module']) ? $_GET['module'] : 'overallView';
		switch ($module) {
			case 'overallView':
				new WpProQuiz_Controller_Quiz();
				break;
			case 'question':
				new WpProQuiz_Controller_Question();
				break;
			case 'preview':
				new WpProQuiz_Controller_Preview($this->_plugin_file);
				break;
		}
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
			case 'Helper':
				$dir = 'helper';
				break;
			case 'Controller':
				$dir = 'controller';
				break;
		}
		
		if(file_exists($this->_plugin_dir.'/lib/'.$dir.'/'.$class.'.php'))
			include_once $this->_plugin_dir.'/lib/'.$dir.'/'.$class.'.php';
	}
}