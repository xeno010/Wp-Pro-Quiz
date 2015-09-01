<?php

class WpProQuiz_Controller_Admin
{

    protected $_ajax;

    public function __construct()
    {

        $this->_ajax = new WpProQuiz_Controller_Ajax();
        $this->_ajax->init();

        add_action('admin_menu', array($this, 'register_page'));

        add_filter('set-screen-option', array($this, 'setScreenOption'), 10, 3);
    }

    public function setScreenOption($status, $option, $value)
    {
        if (in_array($option, array('wp_pro_quiz_quiz_overview_per_page', 'wp_pro_quiz_question_overview_per_page'))) {
            return $value;
        }

        return $status;
    }

    private function localizeScript()
    {
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
            'closeText' => __('Close', 'wp-pro-quiz'),
            'currentText' => __('Today', 'wp-pro-quiz'),
            'monthNames' => array_values($wp_locale->month),
            'monthNamesShort' => array_values($wp_locale->month_abbrev),
            'dayNames' => array_values($wp_locale->weekday),
            'dayNamesShort' => array_values($wp_locale->weekday_abbrev),
            'dayNamesMin' => array_values($wp_locale->weekday_initial),
//			'dateFormat'        => WpProQuiz_Helper_Until::convertPHPDateFormatToJS(get_option('date_format', 'm/d/Y')),
            //e.g. "9 de setembro de 2014" -> change to "hard" dateformat
            'dateFormat' => 'mm/dd/yy',
            'firstDay' => get_option('start_of_week'),
            'isRTL' => $isRtl
        );

        wp_localize_script('wpProQuiz_admin_javascript', 'wpProQuizLocalize', $translation_array);
    }

    public function enqueueScript()
    {
        wp_enqueue_script(
            'wpProQuiz_admin_javascript',
            plugins_url('js/wpProQuiz_admin' . (WPPROQUIZ_DEV ? '' : '.min') . '.js', WPPROQUIZ_FILE),
            array('jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker'),
            WPPROQUIZ_VERSION
        );

        wp_enqueue_style('jquery-ui',
            'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        $this->localizeScript();
    }

    public function register_page()
    {
        $pages = array();

        $pages[] = add_menu_page(
            'WP-Pro-Quiz',
            'WP-Pro-Quiz',
            'wpProQuiz_show',
            'wpProQuiz',
            array($this, 'route'));

        $pages[] = add_submenu_page(
            'wpProQuiz',
            __('Global settings', 'wp-pro-quiz'),
            __('Global settings', 'wp-pro-quiz'),
            'wpProQuiz_change_settings',
            'wpProQuiz_glSettings',
            array($this, 'route'));

        $pages[] = add_submenu_page(
            'wpProQuiz',
            __('Support & More', 'wp-pro-quiz'),
            __('Support & More', 'wp-pro-quiz'),
            'wpProQuiz_show',
            'wpProQuiz_wpq_support',
            array($this, 'route'));

        foreach ($pages as $p) {
            add_action('admin_print_scripts-' . $p, array($this, 'enqueueScript'));
            add_action('load-' . $p, array($this, 'routeLoadAction'));
        }
    }

    public function routeLoadAction()
    {
        $screen = get_current_screen();

        if (!empty($screen)) {
            // Workaround for wp_ajax_hidden_columns() with sanitize_key()
            $name = strtolower($screen->id);

            if (!empty($_GET['module'])) {
                $name .= '_' . strtolower($_GET['module']);
            }

            set_current_screen($name);

            $screen = get_current_screen();
        }

        $helperView = new WpProQuiz_View_GlobalHelperTabs();

        $screen->add_help_tab($helperView->getHelperTab());
        $screen->set_help_sidebar($helperView->getHelperSidebar());

        $this->_route(true);
    }

    public function route()
    {
        $this->_route();
    }

    private function _route($routeAction = false)
    {
        $module = isset($_GET['module']) ? $_GET['module'] : 'overallView';

        if (isset($_GET['page'])) {
            if (preg_match('#wpProQuiz_(.+)#', trim($_GET['page']), $matches)) {
                $module = $matches[1];
            }
        }

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
            case 'glSettings':
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
            case 'info_adaptation':
                $c = new WpProQuiz_Controller_InfoAdaptation();
                break;
        }

        if ($c !== null) {
            if ($routeAction) {
                if (method_exists($c, 'routeAction')) {
                    $c->routeAction();
                }
            } else {
                $c->route();
            }
        }
    }
}