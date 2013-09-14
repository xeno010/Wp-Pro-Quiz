<?php
class WpProQuiz_Controller_Admin {
	
	protected $_ajax;
	
	public function __construct() {
		
		$this->_ajax = new WpProQuiz_Controller_Ajax();
		
		$this->_ajax->init();
		
		//deprecated - use WpProQuiz_Controller_Ajax
		add_action('wp_ajax_wp_pro_quiz_update_sort', array($this, 'updateSort'));
		add_action('wp_ajax_wp_pro_quiz_load_question', array($this, 'updateSort'));
		
		add_action('wp_ajax_wp_pro_quiz_reset_lock', array($this, 'resetLock'));
		
		add_action('wp_ajax_wp_pro_quiz_load_toplist', array($this, 'adminToplist'));
				
		add_action('wp_ajax_wp_pro_quiz_completed_quiz', array($this, 'completedQuiz'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_completed_quiz', array($this, 'completedQuiz'));
		
		add_action('wp_ajax_wp_pro_quiz_check_lock', array($this, 'quizCheckLock'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_check_lock', array($this, 'quizCheckLock'));
		
		//0.19
		add_action('wp_ajax_wp_pro_quiz_add_toplist', array($this, 'addInToplist'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_add_toplist', array($this, 'addInToplist'));
		
		add_action('wp_ajax_wp_pro_quiz_show_front_toplist', array($this, 'showFrontToplist'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_show_front_toplist', array($this, 'showFrontToplist'));
		
		add_action('wp_ajax_wp_pro_quiz_load_quiz_data', array($this, 'loadQuizData'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_load_quiz_data', array($this, 'loadQuizData'));
		
		
		add_action('admin_menu', array($this, 'register_page'));
	}
	
	public function loadQuizData() {
		$q = new WpProQuiz_Controller_Quiz();
		
		echo json_encode($q->loadQuizData());
		
		exit;
	}
	
	public function adminToplist() {
		$t = new WpProQuiz_Controller_Toplist();
		$t->route();
		
		exit;
	}
	
	public function showFrontToplist() {
		$t = new WpProQuiz_Controller_Toplist();
		
		$t->showFrontToplist();
		
		exit;
	}
	
	public function addInToplist() {
		$t = new WpProQuiz_Controller_Toplist();
		
		$t->addInToplist();
		
		exit;
	}
	
	public function resetLock() {
		$c = new WpProQuiz_Controller_Quiz();
		$c->route();
	}
	
	public function quizCheckLock() {
		$quizController = new WpProQuiz_Controller_Quiz();
		
		echo json_encode($quizController->isLockQuiz($_POST['quizId']));
		
		exit;
	}
	
	public function updateSort() {
		$c = new WpProQuiz_Controller_Question();
		$c->route();
	}
	
	public function completedQuiz() {
		$quiz = new WpProQuiz_Controller_Quiz();
		$quiz->completedQuiz();
	}
	
	private function localizeScript() {
		global $wp_locale;
		
		$isRtl = isset($wp_locale->is_rtl) ? $wp_locale->is_rtl : false;
		
		$translation_array = array(
			'delete_msg' => __('Do you really want to delete the quiz/question?', 'wp-pro-quiz'),
			'no_title_msg' => __('Title is not filled!', 'wp-pro-quiz'),
			'no_question_msg' => __('No question deposited!', 'wp-pro-quiz'),
			'no_correct_msg' => __('Correct answer was not selected!', 'wp-pro-quiz'),
			'no_answer_msg' => __('No answer deposited!', 'wp-pro-quiz'),
			'no_quiz_start_msg' => __('No quiz description filled!', 'wp-pro-quiz'),
			'fail_grade_result' => __('The percent values in result text are incorrect.', 'wp-pro-quiz'),
			'no_nummber_points' => __('No number in the field "Points" or less than 1', 'wp-pro-quiz'),
			'no_nummber_points_new' => __('No number in the field "Points" or less than 0', 'wp-pro-quiz'),
			'no_selected_quiz' => __('No quiz selected', 'wp-pro-quiz'),
			'reset_statistics_msg' => __('Do you really want to reset the statistic?', 'wp-pro-quiz'),
			'no_data_available' => __('No data available', 'wp-pro-quiz'),
			'no_sort_element_criterion' => __('No sort element in the criterion', 'wp-pro-quiz'),
			'dif_points' => __('"Different points for every answer" is not possible at "Free" choice', 'wp-pro-quiz'),
			'category_no_name' => __('You must specify a name.', 'wp-pro-quiz'),
			'confirm_delete_entry' => __('This entry should really be deleted?', 'wp-pro-quiz'),
			'not_all_fields_completed' => __('Not all fields completed.', 'wp-pro-quiz'),
			'temploate_no_name' => __('You must specify a template name.', 'wp-pro-quiz'),
				
				
			'closeText'         => __('Close', 'wp-pro-quiz'),
			'currentText'       => __('Today', 'wp-pro-quiz'),
			'monthNames'        => array_values($wp_locale->month),
			'monthNamesShort'   => array_values($wp_locale->month_abbrev),
			'dayNames'          => array_values($wp_locale->weekday),
			'dayNamesShort'     => array_values($wp_locale->weekday_abbrev),
			'dayNamesMin'       => array_values($wp_locale->weekday_initial),
			'dateFormat'        => WpProQuiz_Helper_Until::convertPHPDateFormatToJS(get_option('date_format', 'm/d/Y')),
			'firstDay'          => get_option('start_of_week'),
			'isRTL'             => $isRtl
		);
		
		
		
		
		wp_localize_script('wpProQuiz_admin_javascript', 'wpProQuizLocalize', $translation_array);
	}
	
	public function enqueueScript() {
		wp_enqueue_script(
			'wpProQuiz_admin_javascript', 
			plugins_url('js/wpProQuiz_admin'.(WPPROQUIZ_DEV ? '' : '.min').'.js', WPPROQUIZ_FILE),
			array('jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker'),
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
			case 'toplist':
				$c = new WpProQuiz_Controller_Toplist();
				break;
			case 'wpq_support':
				$c = new WpProQuiz_Controller_WpqSupport();
				break;
		}

		if($c !== null) {
			$c->route();
		}
	}
}