<?php
class WpProQuiz_Controller_Admin {
		
	public function __construct() {
		
		add_action('wp_ajax_wp_pro_quiz_update_sort', array($this, 'updateSort'));
		add_action('wp_ajax_wp_pro_quiz_load_question', array($this, 'updateSort'));
		
		add_action('wp_ajax_wp_pro_quiz_load_statistics', array($this, 'loadStatistics'));
		add_action('wp_ajax_wp_pro_quiz_statistics', array($this, 'loadStatistics'));
		
		add_action('wp_ajax_wp_pro_quiz_reset_lock', array($this, 'resetLock'));
				
		
		add_action('wp_ajax_wp_pro_quiz_completed_quiz', array($this, 'completedQuiz'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_completed_quiz', array($this, 'completedQuiz'));
		
		add_action('wp_ajax_wp_pro_quiz_check_lock', array($this, 'QuizCheckLock'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_check_lock', array($this, 'QuizCheckLock'));
		
		add_action('admin_menu', array($this, 'register_page'));
	}
	
	public function resetLock() {
		$c = new WpProQuiz_Controller_Quiz();
		$c->route();
	}
	
	public function QuizCheckLock() {
		$quizController = new WpProQuiz_Controller_Quiz();
		
		echo json_encode($quizController->isLockQuiz($_POST['quizId']));
		
		exit;
	}
	
	public function updateSort() {
		$c = new WpProQuiz_Controller_Question();
		$c->route();
	}
	
	public function loadStatistics() {
		$c = new WpProQuiz_Controller_Statistics();
		$c->route();
	}
	
	public function completedQuiz() {
		$quiz = new WpProQuiz_Controller_Quiz();
		$quiz->completedQuiz();
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
			'no_selected_quiz' => __('No quiz selected', 'wp-pro-quiz'),
			'reset_statistics_msg' => __('Do you really want to reset the statistic?', 'wp-pro-quiz')
		);
		
		wp_localize_script('wpProQuiz_admin_javascript', 'wpProQuizLocalize', $translation_array);
	}
	
	public function enqueueScript() {
		wp_enqueue_script(
			'wpProQuiz_admin_javascript', 
			plugins_url('js/wpProQuiz_admin.min.js', WPPROQUIZ_FILE),
			array('jquery', 'jquery-ui-sortable'),
			WPPROQUIZ_VERSION
		);
		
		$this->localizeScript();		
	}
	
	public function register_page() {
		$page = add_menu_page(
					'WP-Pro-Quiz',
					'WP-Pro-Quiz',
					'wpProQuiz_show',
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
				$c = new WpProQuiz_Controller_Preview();
				break;
			case 'statistics':
				$c = new WpProQuiz_Controller_Statistics();
				break;
			case 'importExport':
				$c = new WpProQuiz_Controller_ImportExport();
				break;
			case 'globalSettings':
				$c = new WpProQuiz_Controller_GlobalSettings();
				break;
			case 'styleManager':
				$c = new WpProQuiz_Controller_StyleManager();
				break;
		}

		if($c !== null) {
			$c->route();
		}
	}
}