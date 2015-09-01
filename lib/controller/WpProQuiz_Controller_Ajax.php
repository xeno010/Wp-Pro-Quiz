<?php

/**
 * @since 0.23
 */
class WpProQuiz_Controller_Ajax
{

    private $_adminCallbacks = array();
    private $_frontCallbacks = array();

    public function init()
    {
        $this->initCallbacks();

        add_action('wp_ajax_wp_pro_quiz_admin_ajax', array($this, 'adminAjaxCallback'));
        add_action('wp_ajax_nopriv_wp_pro_quiz_admin_ajax', array($this, 'frontAjaxCallback'));
    }

    public function adminAjaxCallback()
    {
        $this->ajaxCallbackHandler(true);
    }

    public function frontAjaxCallback()
    {
        $this->ajaxCallbackHandler(false);
    }

    private function ajaxCallbackHandler($admin)
    {
        $func = isset($_POST['func']) ? $_POST['func'] : '';
        $data = isset($_POST['data']) ? $_POST['data'] : null;
        $calls = $admin ? $this->_adminCallbacks : $this->_frontCallbacks;

        if (isset($calls[$func])) {
            $r = call_user_func($calls[$func], $data, $func);

            if ($r !== null) {
                echo $r;
            }
        }

        exit;
    }

    private function initCallbacks()
    {
        $this->_adminCallbacks = array(
            'categoryAdd' => array('WpProQuiz_Controller_Category', 'ajaxAddCategory'),
            'categoryDelete' => array('WpProQuiz_Controller_Category', 'ajaxDeleteCategory'),
            'categoryEdit' => array('WpProQuiz_Controller_Category', 'ajaxEditCategory'),
            'statisticLoadHistory' => array('WpProQuiz_Controller_Statistics', 'ajaxLoadHistory'),
            'statisticLoadUser' => array('WpProQuiz_Controller_Statistics', 'ajaxLoadStatisticUser'),
            'statisticResetNew' => array('WpProQuiz_Controller_Statistics', 'ajaxRestStatistic'),
            'statisticLoadOverviewNew' => array('WpProQuiz_Controller_Statistics', 'ajaxLoadStatsticOverviewNew'),
            'templateEdit' => array('WpProQuiz_Controller_Template', 'ajaxEditTemplate'),
            'templateDelete' => array('WpProQuiz_Controller_Template', 'ajaxDeleteTemplate'),
            'quizLoadData' => array('WpProQuiz_Controller_Front', 'ajaxQuizLoadData'),
            'setQuizMultipleCategories' => array('WpProQuiz_Controller_Quiz', 'ajaxSetQuizMultipleCategories'),
            'setQuestionMultipleCategories' => array(
                'WpProQuiz_Controller_Question',
                'ajaxSetQuestionMultipleCategories'
            ),
            'loadQuestionsSort' => array('WpProQuiz_Controller_Question', 'ajaxLoadQuestionsSort'),
            'questionSaveSort' => array('WpProQuiz_Controller_Question', 'ajaxSaveSort'),
            'questionaLoadCopyQuestion' => array('WpProQuiz_Controller_Question', 'ajaxLoadCopyQuestion'),
            'loadQuizData' => array('WpProQuiz_Controller_Quiz', 'ajaxLoadQuizData'),
            'resetLock' => array('WpProQuiz_Controller_Quiz', 'ajaxResetLock'),
            'adminToplist' => array('WpProQuiz_Controller_Toplist', 'ajaxAdminToplist'),
            'completedQuiz' => array('WpProQuiz_Controller_Quiz', 'ajaxCompletedQuiz'),
            'quizCheckLock' => array('WpProQuiz_Controller_Quiz', 'ajaxQuizCheckLock'),
            'addInToplist' => array('WpProQuiz_Controller_Toplist', 'ajaxAddInToplist'),
            'showFrontToplist' => array('WpProQuiz_Controller_Toplist', 'ajaxShowFrontToplist')
        );

        //nopriv
        $this->_frontCallbacks = array(
            'quizLoadData' => array('WpProQuiz_Controller_Front', 'ajaxQuizLoadData'),
            'loadQuizData' => array('WpProQuiz_Controller_Quiz', 'ajaxLoadQuizData'),
            'completedQuiz' => array('WpProQuiz_Controller_Quiz', 'ajaxCompletedQuiz'),
            'quizCheckLock' => array('WpProQuiz_Controller_Quiz', 'ajaxQuizCheckLock'),
            'addInToplist' => array('WpProQuiz_Controller_Toplist', 'ajaxAddInToplist'),
            'showFrontToplist' => array('WpProQuiz_Controller_Toplist', 'ajaxShowFrontToplist')
        );
    }
}