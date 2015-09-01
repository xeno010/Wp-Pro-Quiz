<?php

class WpProQuiz_Controller_Statistics extends WpProQuiz_Controller_Controller
{
    public function route()
    {
        $action = (isset($_GET['action'])) ? $_GET['action'] : 'show';

        switch ($action) {
            case 'show':
            default:
                $this->showNew($_GET['id']);
        }
    }

    public function getAverageResult($quizId)
    {
        $statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();

        $result = $statisticRefMapper->fetchFrontAvg($quizId);

        if (isset($result['g_points']) && $result['g_points']) {
            return round(100 * $result['points'] / $result['g_points'], 2);
        }

        return 0;
    }

    private function showNew($quizId)
    {
        if (!current_user_can('wpProQuiz_show_statistics')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $view = new WpProQuiz_View_StatisticsNew();

        $quizMapper = new WpProQuiz_Model_QuizMapper();

        $quiz = $quizMapper->fetch($quizId);

        if (has_action('pre_user_query', 'ure_exclude_administrators')) {
            remove_action('pre_user_query', 'ure_exclude_administrators');

            $users = get_users(array('fields' => array('ID', 'user_login', 'display_name')));

            add_action('pre_user_query', 'ure_exclude_administrators');

        } else {
            $users = get_users(array('fields' => array('ID', 'user_login', 'display_name')));
        }

        $view->quiz = $quiz;
        $view->users = $users;
        $view->show();
    }

    /**
     *
     * @param WpProQuiz_Model_Quiz $quiz
     * @return void|boolean
     */
    public function save($quiz = null)
    {
        $quizId = $this->_post['quizId'];
        $array = $this->_post['results'];
        $lockIp = $this->getIp();
        $userId = get_current_user_id();

        if ($lockIp === false) {
            return false;
        }

        if ($quiz === null) {
            $quizMapper = new WpProQuiz_Model_QuizMapper();
            $quiz = $quizMapper->fetch($quizId);
        }

        if (!$quiz->isStatisticsOn()) {
            return false;
        }

        $values = $this->makeDataList($quizId, $array, $quiz->getQuizModus());
        $formValues = $this->makeFormData($quiz, isset($this->_post['forms']) ? $this->_post['forms'] : null);

        if ($values === false) {
            return false;
        }

        if ($quiz->getStatisticsIpLock() > 0) {
            $lockMapper = new WpProQuiz_Model_LockMapper();
            $lockTime = $quiz->getStatisticsIpLock() * 60;

            $lockMapper->deleteOldLock($lockTime, $quiz->getId(), time(), WpProQuiz_Model_Lock::TYPE_STATISTIC);

            if ($lockMapper->isLock($quizId, $lockIp, $userId, WpProQuiz_Model_Lock::TYPE_STATISTIC)) {
                return false;
            }

            $lock = new WpProQuiz_Model_Lock();
            $lock->setQuizId($quizId)
                ->setLockIp($lockIp)
                ->setUserId($userId)
                ->setLockType(WpProQuiz_Model_Lock::TYPE_STATISTIC)
                ->setLockDate(time());

            $lockMapper->insert($lock);
        }

        $statisticRefModel = new WpProQuiz_Model_StatisticRefModel();

        $statisticRefModel->setCreateTime(time());
        $statisticRefModel->setUserId($userId);
        $statisticRefModel->setQuizId($quizId);
        $statisticRefModel->setFormData($formValues);

        $statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();
        $statisticRefMapper->statisticSave($statisticRefModel, $values);

        return true;
    }

    /**
     * @param WpProQuiz_Model_Quiz $quiz
     * @param $data
     * @return array|null
     */
    private function makeFormData($quiz, $data)
    {
        if (!$quiz->isFormActivated() || empty($data)) {
            return null;
        }

        $formMapper = new WpProQuiz_Model_FormMapper();

        $forms = $formMapper->fetch($quiz->getId());

        if (empty($forms)) {
            return null;
        }

        $formArray = array();

        foreach ($forms as $form) {
            if ($form->getType() != WpProQuiz_Model_Form::FORM_TYPE_DATE) {
                $str = isset($data[$form->getFormId()]) ? $data[$form->getFormId()] : '';

                if (!WpProQuiz_Helper_Form::valid($form, $str)) {
                    return null;
                }

                $formArray[$form->getFormId()] = trim($str);
            } else {
                $date = isset($data[$form->getFormId()]) ? $data[$form->getFormId()] : array();

                $dateStr = WpProQuiz_Helper_Form::validData($form, $date);

                if ($dateStr === null) {
                    return null;
                }

                $formArray[$form->getFormId()] = $dateStr;
            }
        }

        return $formArray;
    }

    private function makeDataList($quizId, $array, $modus)
    {
        $questionMapper = new WpProQuiz_Model_QuestionMapper();

        $question = $questionMapper->fetchAllList($quizId, array('id', 'points'));

        $ids = array();

        foreach ($question as $q) {
            if (!isset($array[$q['id']])) {
                continue;
            }

            $ids[] = $q['id'];
            $v = $array[$q['id']];

            if (!isset($v) || $v['points'] > $q['points'] || $v['points'] < 0) {
                return false;
            }
        }

        $avgTime = null;

        if ($modus == WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE) {
            $avgTime = ceil($array['comp']['quizTime'] / count($question));
        }

        unset($array['comp']);

        $ak = array_keys($array);

        if (array_diff($ids, $ak) !== array_diff($ak, $ids)) {
            return false;
        }

        $values = array();

        foreach ($array as $k => $v) {
            $s = new WpProQuiz_Model_Statistic();
            $s->setQuestionId($k);
            $s->setHintCount(isset($v['tip']) ? 1 : 0);
            $s->setSolvedCount(isset($v['solved']) && $v['solved'] ? 1 : 0);
            $s->setCorrectCount($v['correct'] ? 1 : 0);
            $s->setIncorrectCount($v['correct'] ? 0 : 1);
            $s->setPoints($v['points']);
            $s->setQuestionTime($avgTime === null ? $v['time'] : $avgTime);
            $s->setAnswerData(isset($v['data']) ? $v['data'] : null);

            $values[] = $s;
        }

        return $values;
    }

    private function getIp()
    {
        if (get_current_user_id() > 0) {
            return '0';
        } else {
            return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        }
    }

    public static function ajaxLoadHistory($data)
    {
        if (!current_user_can('wpProQuiz_show_statistics')) {
            return json_encode(array());
        }

        $statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();
        $formMapper = new WpProQuiz_Model_FormMapper();

        $quizId = $data['quizId'];

        $forms = $formMapper->fetch($quizId);

        $page = (isset($data['page']) && $data['page'] > 0) ? $data['page'] : 1;
        $limit = $data['pageLimit'];
        $start = $limit * ($page - 1);

        $startTime = (int)$data['dateFrom'];
        $endTime = (int)$data['dateTo'] ? $data['dateTo'] + 86400 : 0;

        $statisticModel = $statisticRefMapper->fetchHistory($quizId, $start, $limit, $data['users'], $startTime,
            $endTime);

        foreach ($statisticModel as $model) {
            /* @var $model WpProQuiz_Model_StatisticHistory */

            if (!$model->getUserId()) {
                $model->setUserName(__('Anonymous', 'wp-pro-quiz'));
            } else {
                if ($model->getUserName() == '') {
                    $model->setUserName(__('Deleted user', 'wp-pro-quiz'));
                }
            }

            $sum = $model->getCorrectCount() + $model->getIncorrectCount();
            $result = round(100 * $model->getPoints() / $model->getGPoints(), 2) . '%';

            $model->setResult($result);
            $model->setFormatTime(WpProQuiz_Helper_Until::convertTime($model->getCreateTime(),
                get_option('wpProQuiz_statisticTimeFormat', 'Y/m/d g:i A')));

            $model->setFormatCorrect($model->getCorrectCount() . ' (' . round(100 * $model->getCorrectCount() / $sum,
                    2) . '%)');
            $model->setFormatIncorrect($model->getIncorrectCount() . ' (' . round(100 * $model->getIncorrectCount() / $sum,
                    2) . '%)');

            $formData = $model->getFormData();
            $formOverview = array();

            foreach ($forms as $form) {
                /* @var $form WpProQuiz_Model_Form */
                if ($form->isShowInStatistic()) {
                    $formOverview[] = $formData != null && isset($formData[$form->getFormId()])
                        ? WpProQuiz_Helper_Form::formToString($form, $formData[$form->getFormId()])
                        : '----';
                }
            }

            $model->setFormOverview($formOverview);
        }

        $view = new WpProQuiz_View_StatisticsAjax();
        $view->historyModel = $statisticModel;
        $view->forms = $forms;

        $html = $view->getHistoryTable();
        $navi = null;

        if (isset($data['generateNav']) && $data['generateNav']) {
            $count = $statisticRefMapper->countHistory($quizId, $data['users'], $startTime, $endTime);
            $navi = ceil(($count > 0 ? $count : 1) / $limit);
        }

        return json_encode(array(
            'html' => $html,
            'navi' => $navi
        ));
    }

    public static function ajaxLoadStatisticUser($data)
    {
        if (!current_user_can('wpProQuiz_show_statistics')) {
            return json_encode(array());
        }

        $quizId = $data['quizId'];
        $userId = $data['userId'];
        $refId = $data['refId'];
        $avg = (bool)$data['avg'];
        $refIdUserId = $avg ? $userId : $refId;

        $statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();
        $statisticUserMapper = new WpProQuiz_Model_StatisticUserMapper();
        $formMapper = new WpProQuiz_Model_FormMapper();

        $statisticUsers = $statisticUserMapper->fetchUserStatistic($refIdUserId, $quizId, $avg);

        $output = array();

        foreach ($statisticUsers as $statistic) {
            /* @var $statistic WpProQuiz_Model_StatisticUser */

            if (!isset($output[$statistic->getCategoryId()])) {
                $output[$statistic->getCategoryId()] = array(
                    'questions' => array(),
                    'categoryId' => $statistic->getCategoryId(),
                    'categoryName' => $statistic->getCategoryId() ? $statistic->getCategoryName() : __('No category',
                        'wp-pro-quiz')
                );
            }

            $o = &$output[$statistic->getCategoryId()];

            $o['questions'][] = array(
                'correct' => $statistic->getCorrectCount(),
                'incorrect' => $statistic->getIncorrectCount(),
                'hintCount' => $statistic->getIncorrectCount(),
                'time' => $statistic->getQuestionTime(),
                'points' => $statistic->getPoints(),
                'gPoints' => $statistic->getGPoints(),
                'statistcAnswerData' => $statistic->getStatisticAnswerData(),
                'questionName' => $statistic->getQuestionName(),
                'questionAnswerData' => $statistic->getQuestionAnswerData(),
                'answerType' => $statistic->getAnswerType(),
                'solvedCount' => $statistic->getSolvedCount()
            );
        }

        $view = new WpProQuiz_View_StatisticsAjax();

        $view->avg = $avg;
        $view->statisticModel = $statisticRefMapper->fetchByRefId($refIdUserId, $quizId, $avg);

        $view->userName = __('Anonymous', 'wp-pro-quiz');

        if ($view->statisticModel->getUserId()) {
            $userInfo = get_userdata($view->statisticModel->getUserId());

            if ($userInfo !== false) {
                $view->userName = $userInfo->user_login . ' (' . $userInfo->display_name . ')';
            } else {
                $view->userName = __('Deleted user', 'wp-pro-quiz');
            }
        }

        if (!$avg) {
            $view->forms = $formMapper->fetch($quizId);
        }

        $view->userStatistic = $output;

        $html = $view->getUserTable();

        return json_encode(array(
            'html' => $html
        ));
    }

    public static function ajaxRestStatistic($data)
    {
        if (!current_user_can('wpProQuiz_reset_statistics')) {
            return;
        }

        $statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();

        switch ($data['type']) {
            case 0: //RefId or UserId
                if ($data['refId']) {
                    $statisticRefMapper->deleteByRefId($data['refId']);
                } else {
                    if ($data['userId'] != '') {
                        $statisticRefMapper->deleteByUserIdQuizId($data['userId'], $data['quizId']);
                    }
                }
                break;
            case 1: //alles
                $statisticRefMapper->deleteAll($data['quizId']);
                break;
        }
    }

    public static function ajaxLoadStatsticOverviewNew($data)
    {
        if (!current_user_can('wpProQuiz_show_statistics')) {
            return json_encode(array());
        }

        $statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();

        $quizId = $data['quizId'];

        $page = (isset($data['page']) && $data['page'] > 0) ? $data['page'] : 1;
        $limit = $data['pageLimit'];
        $start = $limit * ($page - 1);

        $statisticModel = $statisticRefMapper->fetchStatisticOverview($quizId, $data['onlyCompleted'], $start, $limit);

        $view = new WpProQuiz_View_StatisticsAjax();
        $view->statisticModel = $statisticModel;

        $navi = null;

        if (isset($data['generateNav']) && $data['generateNav']) {
            $count = $statisticRefMapper->countOverviewNew($quizId, $data['onlyCompleted']);
            $navi = ceil(($count > 0 ? $count : 1) / $limit);
        }

        return json_encode(array(
            'navi' => $navi,
            'html' => $view->getOverviewTable()
        ));
    }
}