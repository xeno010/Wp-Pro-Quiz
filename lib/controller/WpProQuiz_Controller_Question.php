<?php

class WpProQuiz_Controller_Question extends WpProQuiz_Controller_Controller
{
    private $_quizId;

    public function route()
    {
        if (!isset($_GET['quiz_id']) || empty($_GET['quiz_id'])) {
            WpProQuiz_View_View::admin_notices(__('Quiz not found', 'wp-pro-quiz'), 'error');

            return;
        }

        $this->_quizId = (int)$_GET['quiz_id'];
        $action = isset($_GET['action']) ? $_GET['action'] : 'show';

        $m = new WpProQuiz_Model_QuizMapper();

        if ($m->exists($this->_quizId) == 0) {
            WpProQuiz_View_View::admin_notices(__('Quiz not found', 'wp-pro-quiz'), 'error');

            return;
        }

        switch ($action) {
            case 'show':
                $this->showAction();
                break;
            case 'addEdit':
                $this->addEditQuestion((int)$_GET['quiz_id']);
                break;
            case 'delete':
                $this->deleteAction($_GET['id']);
                break;
            case 'delete_multi':
                $this->deleteMultiAction();
                break;
            case 'save_sort':
                $this->saveSort();
                break;
            case 'load_question':
                $this->loadQuestion($_GET['quiz_id']);
                break;
            case 'copy_question':
                $this->copyQuestion($_GET['quiz_id']);
                break;
            default:
                $this->showAction();
                break;
        }
    }

    public function routeAction()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'show';

        switch ($action) {
            default:
                $this->showActionHook();
                break;
        }
    }

    private function showActionHook()
    {
        if (!empty($_REQUEST['_wp_http_referer'])) {
            wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI'])));
            exit;
        }

        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }

        add_filter('manage_' . get_current_screen()->id . '_columns',
            array('WpProQuiz_View_QuestionOverallTable', 'getColumnDefs'));

        add_screen_option('per_page', array(
            'label' => __('Questions', 'wp-pro-quiz'),
            'default' => 20,
            'option' => 'wp_pro_quiz_question_overview_per_page'
        ));
    }

    private function addEditQuestion($quizId)
    {
        $questionId = isset($_GET['questionId']) ? (int)$_GET['questionId'] : 0;

        if ($questionId) {
            if (!current_user_can('wpProQuiz_edit_quiz')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
        } else {
            if (!current_user_can('wpProQuiz_add_quiz')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
        }

        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $questionMapper = new WpProQuiz_Model_QuestionMapper();
        $cateoryMapper = new WpProQuiz_Model_CategoryMapper();
        $templateMapper = new WpProQuiz_Model_TemplateMapper();

        if ($questionId && $questionMapper->existsAndWritable($questionId) == 0) {
            WpProQuiz_View_View::admin_notices(__('Question not found', 'wp-pro-quiz'), 'error');

            return;
        }

        $question = new WpProQuiz_Model_Question();

        if (isset($this->_post['template']) || (isset($this->_post['templateLoad']) && isset($this->_post['templateLoadId']))) {
            if (isset($this->_post['template'])) {
                $template = $this->saveTemplate();
            } else {
                $template = $templateMapper->fetchById($this->_post['templateLoadId']);
            }

            $data = $template->getData();

            if ($data !== null) {
                /** @var WpProQuiz_Model_Question $question */
                $question = $data['question'];
                $question->setId($questionId);
                $question->setQuizId($quizId);
            }
        } else {
            if (isset($this->_post['submit'])) {
                if ($questionId) {
                    WpProQuiz_View_View::admin_notices(__('Question edited', 'wp-pro-quiz'), 'info');
                } else {
                    WpProQuiz_View_View::admin_notices(__('Question added', 'wp-pro-quiz'), 'info');
                }

                $question = $questionMapper->save($this->getPostQuestionModel($quizId, $questionId), true);
                $questionId = $question->getId();

            } else {
                if ($questionId) {
                    $question = $questionMapper->fetch($questionId);
                }
            }
        }

        $view = new WpProQuiz_View_QuestionEdit();
        $view->categories = $cateoryMapper->fetchAll();
        $view->quiz = $quizMapper->fetch($quizId);
        $view->templates = $templateMapper->fetchAll(WpProQuiz_Model_Template::TEMPLATE_TYPE_QUESTION, false);
        $view->question = $question;
        $view->answerData = $this->setAnswerObject($question);

        $view->header = $questionId ? __('Edit question', 'wp-pro-quiz') : __('New question', 'wp-pro-quiz');

        if ($view->question->isAnswerPointsActivated()) {
            $view->question->setPoints(1);
        }

        $view->show();
    }

    private function saveTemplate()
    {
        $questionModel = $this->getPostQuestionModel(0, 0);

        $templateMapper = new WpProQuiz_Model_TemplateMapper();
        $template = new WpProQuiz_Model_Template();

        if ($this->_post['templateSaveList'] == '0') {
            $template->setName(trim($this->_post['templateName']));
        } else {
            $template = $templateMapper->fetchById($this->_post['templateSaveList'], false);
        }

        $template->setType(WpProQuiz_Model_Template::TEMPLATE_TYPE_QUESTION);

        $template->setData(array(
            'question' => $questionModel
        ));

        return $templateMapper->save($template);
    }

    private function getPostQuestionModel($quizId, $questionId)
    {
        $questionMapper = new WpProQuiz_Model_QuestionMapper();

        $post = WpProQuiz_Controller_Request::getPost();

        $post['id'] = $questionId;
        $post['quizId'] = $quizId;
        $post['title'] = isset($post['title']) ? trim($post['title']) : '';

        $clearPost = $this->clearPost($post);

        $post['answerData'] = $clearPost['answerData'];

        if (empty($post['title'])) {
            $count = $questionMapper->count($quizId);

            $post['title'] = sprintf(__('Question: %d', 'wp-pro-quiz'), $count + 1);
        }

        if ($post['answerType'] === 'assessment_answer') {
            $post['answerPointsActivated'] = 1;
        }

        if (isset($post['answerPointsActivated'])) {
            if (isset($post['answerPointsDiffModusActivated'])) {
                $post['points'] = $clearPost['maxPoints'];
            } else {
                $post['points'] = $clearPost['points'];
            }
        }

        $post['categoryId'] = $post['category'] > 0 ? $post['category'] : 0;

        return new WpProQuiz_Model_Question($post);
    }

    public function copyQuestion($quizId)
    {

        if (!current_user_can('wpProQuiz_edit_quiz')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $m = new WpProQuiz_Model_QuestionMapper();

        $questions = $m->fetchById($this->_post['copyIds']);

        foreach ($questions as $question) {
            $question->setId(0);
            $question->setQuizId($quizId);

            $m->save($question);
        }

        WpProQuiz_View_View::admin_notices(__('questions copied', 'wp-pro-quiz'), 'info');

        $this->showAction();
    }

    public function loadQuestion($quizId)
    {

        if (!current_user_can('wpProQuiz_edit_quiz')) {
            echo json_encode(array());
            exit;
        }

        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $questionMapper = new WpProQuiz_Model_QuestionMapper();
        $data = array();

        $quiz = $quizMapper->fetchAll();

        foreach ($quiz as $qz) {

            if ($qz->getId() == $quizId) {
                continue;
            }

            $question = $questionMapper->fetchAll($qz->getId());
            $questionArray = array();

            foreach ($question as $qu) {
                $questionArray[] = array(
                    'name' => $qu->getTitle(),
                    'id' => $qu->getId()
                );
            }

            $data[] = array(
                'name' => $qz->getName(),
                'id' => $qz->getId(),
                'question' => $questionArray
            );
        }

        echo json_encode($data);

        exit;
    }

    public function saveSort()
    {

        if (!current_user_can('wpProQuiz_edit_quiz')) {
            exit;
        }

        $mapper = new WpProQuiz_Model_QuestionMapper();
        $map = $this->_post['sort'];

        foreach ($map as $k => $v) {
            $mapper->updateSort($v, $k);
        }

        exit;
    }

    public function deleteAction($id)
    {

        if (!current_user_can('wpProQuiz_delete_quiz')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $mapper = new WpProQuiz_Model_QuestionMapper();
        $mapper->setOnlineOff($id);

        $this->showAction();
    }

    public function deleteMultiAction()
    {
        if (!current_user_can('wpProQuiz_delete_quiz')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $mapper = new WpProQuiz_Model_QuestionMapper();

        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $id) {
                $mapper->setOnlineOff($id);
            }
        }

        $this->showAction();
    }

    private function setAnswerObject(WpProQuiz_Model_Question $question = null)
    {
        //Defaults
        $data = array(
            'sort_answer' => array(new WpProQuiz_Model_AnswerTypes()),
            'classic_answer' => array(new WpProQuiz_Model_AnswerTypes()),
            'matrix_sort_answer' => array(new WpProQuiz_Model_AnswerTypes()),
            'cloze_answer' => array(new WpProQuiz_Model_AnswerTypes()),
            'free_answer' => array(new WpProQuiz_Model_AnswerTypes()),
            'assessment_answer' => array(new WpProQuiz_Model_AnswerTypes())
        );

        if ($question !== null) {
            $type = $question->getAnswerType();
            $type = ($type == 'single' || $type == 'multiple') ? 'classic_answer' : $type;
            $answerData = $question->getAnswerData();

            if (isset($data[$type]) && $answerData !== null) {
                $data[$type] = $question->getAnswerData();
            }
        }

        return $data;
    }

    public function clearPost($post)
    {

        if ($post['answerType'] == 'cloze_answer' && isset($post['answerData']['cloze'])) {
            preg_match_all('#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', $post['answerData']['cloze']['answer'], $matches);

            $points = 0;
            $maxPoints = 0;

            foreach ($matches[2] as $match) {
                if (empty($match)) {
                    $match = 1;
                }

                $points += $match;
                $maxPoints = max($maxPoints, $match);
            }

            return array(
                'points' => $points,
                'maxPoints' => $maxPoints,
                'answerData' => array(new WpProQuiz_Model_AnswerTypes($post['answerData']['cloze']))
            );
        }

        if ($post['answerType'] == 'assessment_answer' && isset($post['answerData']['assessment'])) {
            preg_match_all('#\{(.*?)\}#im', $post['answerData']['assessment']['answer'], $matches);

            $points = 0;
            $maxPoints = 0;

            foreach ($matches[1] as $match) {
                preg_match_all('#\[([^\|\]]+)(?:\|(\d+))?\]#im', $match, $ms);

                $points += count($ms[1]);
                $maxPoints = max($maxPoints, count($ms[1]));
            }

            return array(
                'points' => $points,
                'maxPoints' => $maxPoints,
                'answerData' => array(new WpProQuiz_Model_AnswerTypes($post['answerData']['assessment']))
            );
        }

        unset($post['answerData']['cloze']);
        unset($post['answerData']['assessment']);

        if (isset($post['answerData']['none'])) {
            unset($post['answerData']['none']);
        }

        $answerData = array();
        $points = 0;
        $maxPoints = 0;

        foreach ($post['answerData'] as $k => $v) {
            if (trim($v['answer']) == '') {
                if ($post['answerType'] != 'matrix_sort_answer') {
                    continue;
                } else {
                    if (trim($v['sort_string']) == '') {
                        continue;
                    }
                }
            }

            $answerType = new WpProQuiz_Model_AnswerTypes($v);
            $points += $answerType->getPoints();

            $maxPoints = max($maxPoints, $answerType->getPoints());

            $answerData[] = $answerType;
        }

        return array('points' => $points, 'maxPoints' => $maxPoints, 'answerData' => $answerData);
    }

    public function clear($a)
    {
        foreach ($a as $k => $v) {
            if (is_array($v)) {
                $a[$k] = $this->clear($a[$k]);
            }

            if (is_string($a[$k])) {
                $a[$k] = trim($a[$k]);

                if ($a[$k] != '') {
                    continue;
                }
            }

            if (empty($a[$k])) {
                unset($a[$k]);
            }
        }

        return $a;
    }

    private function getCurrentPage()
    {
        $pagenum = isset($_REQUEST['paged']) ? absint($_REQUEST['paged']) : 0;

        return max(1, $pagenum);
    }

    public function showAction()
    {
        if (!current_user_can('wpProQuiz_show')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $m = new WpProQuiz_Model_QuizMapper();
        $mm = new WpProQuiz_Model_QuestionMapper();
        $categoryMapper = new WpProQuiz_Model_CategoryMapper();

        $view = new WpProQuiz_View_QuestionOverall();
        $view->quiz = $m->fetch($this->_quizId);

        $per_page = (int)get_user_option('wp_pro_quiz_question_overview_per_page');
        if (empty($per_page) || $per_page < 1) {
            $per_page = 20;
        }

        $current_page = $this->getCurrentPage();
        $search = isset($_GET['s']) ? trim($_GET['s']) : '';
        $orderBy = isset($_GET['orderby']) ? trim($_GET['orderby']) : '';
        $order = isset($_GET['order']) ? trim($_GET['order']) : '';
        $offset = ($current_page - 1) * $per_page;
        $limit = $per_page;
        $filter = array();

        if (isset($_GET['cat'])) {
            $filter['cat'] = $_GET['cat'];
        }

        $result = $mm->fetchTable($this->_quizId, $orderBy, $order, $search, $limit, $offset, $filter);

        $view->questionItems = $result['questions'];
        $view->questionCount = $result['count'];
        $view->categoryItems = $categoryMapper->fetchAll(WpProQuiz_Model_Category::CATEGORY_TYPE_QUESTION);
        $view->perPage = $per_page;

        $view->show();
    }

    public static function ajaxSetQuestionMultipleCategories($data)
    {
        if (!current_user_can('wpProQuiz_edit_quiz')) {
            return json_encode(array());
        }

        $quizMapper = new WpProQuiz_Model_QuestionMapper();

        $quizMapper->setMultipeCategories($data['questionIds'], $data['categoryId']);

        return json_encode(array());
    }

    public static function ajaxLoadQuestionsSort($data)
    {
        if (!current_user_can('wpProQuiz_edit_quiz')) {
            return json_encode(array());
        }

        $quizMapper = new WpProQuiz_Model_QuestionMapper();

        $questions = $quizMapper->fetchAllList($data['quizId'], array('id', 'title'), true);

        return json_encode($questions);
    }

    public static function ajaxSaveSort($data)
    {
        if (!current_user_can('wpProQuiz_edit_quiz')) {
            return json_encode(array());
        }

        $mapper = new WpProQuiz_Model_QuestionMapper();

        foreach ($data['sort'] as $k => $v) {
            $mapper->updateSort($v, $k);
        }

        return json_encode(array());
    }

    public static function ajaxLoadCopyQuestion($data)
    {
        if (!current_user_can('wpProQuiz_edit_quiz')) {
            echo json_encode(array());
            exit;
        }

        $quizId = $data['quizId'];
        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $questionMapper = new WpProQuiz_Model_QuestionMapper();
        $data = array();

        $quiz = $quizMapper->fetchAll();

        foreach ($quiz as $qz) {

            if ($qz->getId() == $quizId) {
                continue;
            }

            $question = $questionMapper->fetchAll($qz->getId());
            $questionArray = array();

            foreach ($question as $qu) {
                $questionArray[] = array(
                    'name' => $qu->getTitle(),
                    'id' => $qu->getId()
                );
            }

            $data[] = array(
                'name' => $qz->getName(),
                'id' => $qz->getId(),
                'question' => $questionArray
            );
        }

        return json_encode($data);
    }
}