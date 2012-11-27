<?php
class WpProQuiz_Controller_Admin {
	
	private $_plugin_dir;
	private $_plugin_file;
		
	public function __construct($plugin_dir) {
		spl_autoload_register(array($this, 'autoload'));
		
		$this->_plugin_dir = $plugin_dir;
		$this->_plugin_file = $this->_plugin_dir.'/wp-pro-quiz.php';
		
		add_action('wp_ajax_wp_pro_quiz_update_sort', array($this, 'updateSort'));
		add_action('wp_ajax_wp_pro_quiz_statistics_save', array($this, 'statisticsSave'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_statistics_save', array($this, 'statisticsSave'));
		add_action('admin_menu', array($this, 'register_page'));
	}
	
	public function updateSort() {
		
		if(!current_user_can('administrator'))
			exit;
			
		$c = new WpProQuiz_Controller_Question();
		$c->route();
	}
	
	public function statisticsSave() {
		$statistics = new WpProQuiz_Controller_Statistics();
		$statistics->save();
	}
	
	private function localizeScript() {
		$translation_array = array(
			'delete_msg' => __('Do you really want to delete the quiz/question?', 'wp-pro-quiz'),
			'no_title_msg' => __('Title is not filled!', 'wp-pro-quiz'),
			'no_question_msg' => __('No question deposited!', 'wp-pro-quiz'),
			'no_correct_msg' => __('Correct answer was not selected!', 'wp-pro-quiz'),
			'no_answer_msg' => __('No answer deposited!', 'wp-pro-quiz'),
			'no_quiz_start_msg' => __('No quiz description filled!', 'wp-pro-quiz'),
			'fail_grade_result' => __('The percent values in result text are incorrect.', 'wp-pro-quiz'),
			'no_nummber_points' => __('No number in the field "Points" or less than 1', 'wp-pro-quiz'),
			'no_selected_quiz' => __('No quiz selected', 'wp-pro-quiz')
		);
		
		wp_localize_script('wpProQuiz_admin_javascript', 'wpProQuizLocalize', $translation_array);
	}
	
	public function enqueueScript() {
		wp_enqueue_script(
			'wpProQuiz_admin_javascript', 
			plugins_url('js/wpProQuiz_admin.min.js', $this->_plugin_file),
			array('jquery', 'jquery-ui-sortable'),
			WPPROQUIZ_VERSION
		);
		
		$this->localizeScript();		
	}
	
	public static function install() {
		
		$db = new WpProQuiz_Helper_DbUpgrade();
		$v = $db->upgrade(get_option('wpProQuiz_dbVersion', false));
		
		if(add_option('wpProQuiz_dbVersion', $v) === false)
			update_option('wpProQuiz_dbVersion', $v);
	}
	
	public function register_page() {
		$page = add_menu_page(
					'WP-Pro-Quiz',
					'WP-Pro-Quiz',
					'administrator',
					'wpProQuiz',
					array($this, 'route'));

		add_action('admin_print_scripts-'.$page, array($this, 'enqueueScript'));
	}
	
	public function route() {
		$module = isset($_GET['module']) ? $_GET['module'] : 'overallView';
		
		$c = null;
		
		switch ($module) {
			case 'overallView':
				$c = new WpProQuiz_Controller_Quiz();
				break;
			case 'question':
				$c = new WpProQuiz_Controller_Question();
				break;
			case 'preview':
				$c = new WpProQuiz_Controller_Preview($this->_plugin_file);
				break;
			case 'statistics':
				$c = new WpProQuiz_Controller_Statistics();
				break;
			case 'importExport':
				$c = new WpProQuiz_Controller_ImportExport();
				break;
		}
		
		if($c !== null) {
			$c->route();
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