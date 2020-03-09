<?php

class WpProQuiz_Controller_StatisticExport extends WpProQuiz_Controller_Controller
{

    public function route()
    {
        if (!current_user_can('wpProQuiz_show_statistics')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        switch ($_GET['action']) {
            case 'user_export':
                try {
                    $this->exportUser($_GET['quiz_id'], $_GET['ref_id'], $_GET['user_id'], $_GET['avg']);
                } catch (Exception $e) {
                    wp_die(__('An error has occurred.', 'wp-pro-quiz'));
                }
                break;
            case 'history_export':
                try {
                    $this->exportHistory();
                } catch (Exception $e) {
                    wp_die(__('An error has occurred.', 'wp-pro-quiz'));
                }
                break;
            case 'overview_export':
                try {
                    $this->overviewExport();
                } catch (Exception $e) {
                    wp_die(__('An error has occurred.', 'wp-pro-quiz'));
                }
                break;
        }
    }

    protected function exportUser($quizId, $refId, $userId, $avg)
    {
        $refIdUserId = $avg ? $userId : $refId;

        $statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();
        $statisticUserMapper = new WpProQuiz_Model_StatisticUserMapper();
        $formMapper = new WpProQuiz_Model_FormMapper();

        $statisticUsers = $statisticUserMapper->fetchUserStatistic($refIdUserId, $quizId, $avg);
        $statisticModel = $statisticRefMapper->fetchByRefId($refIdUserId, $quizId, $avg);
        $forms = $formMapper->fetch($quizId);

        $expoter = $this->getExpoter($_POST['exportType']);

        if ($expoter === null) {
            wp_die(__('Unsupported exporter'));
        }

        $response = $expoter->exportUser($statisticUsers, $statisticModel, !$avg ? $forms : []);

        $this->handleResponse($response);

        exit;
    }

    protected function exportHistory()
    {
        $page = (int)$_GET['_page'];
        $quizId = (int)$_GET['quiz_id'];
        $users = (int)$_GET['users'];
        $limit = (int)$_GET['page_limit'];
        $startTime = (int)$_GET['data_from'];
        $endTime = (int)$_GET['date_to'];

        $page = $page > 0 ? $page : 1;
        $start = $limit * ($page - 1);
        $endTime = $endTime ? $endTime + 86400 : 0;

        $statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();
        $formMapper = new WpProQuiz_Model_FormMapper();

        $forms = $formMapper->fetch($quizId);
        $statisticModel = $statisticRefMapper->fetchHistory($quizId, $start, $limit, $users, $startTime, $endTime);

        $expoter = $this->getExpoter($_POST['exportType']);

        if ($expoter === null) {
            wp_die(__('Unsupported exporter'));
        }

        $response = $expoter->exportHistory($statisticModel, $forms);

        $this->handleResponse($response);

        exit;
    }

    protected function overviewExport()
    {
        $page = (int)$_GET['_page'];
        $quizId = (int)$_GET['quiz_id'];
        $limit = (int)$_GET['page_limit'];
        $onlyCompleted = !!$_GET['only_completed'];

        $page = $page > 0 ? $page : 1;
        $start = $limit * ($page - 1);

        $statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();

        $statisticModel = $statisticRefMapper->fetchStatisticOverview($quizId, $onlyCompleted, $start, $limit);

        $expoter = $this->getExpoter($_POST['exportType']);

        if ($expoter === null) {
            wp_die(__('Unsupported exporter'));
        }

        $response = $expoter->exportOverview($statisticModel);

        $this->handleResponse($response);

        exit;
    }

    protected function getExpoter($type)
    {
        $helper = new WpProQuiz_Helper_StatisticExport();

        return $helper->factory($type);
    }

    /**
     * @param string|null|WP_Error $response
     */
    protected function handleResponse($response)
    {
        if ($response instanceof WP_Error) {
            wp_die($response);
        } else if ($response !== null) {
            echo $response;
        }
    }
}
