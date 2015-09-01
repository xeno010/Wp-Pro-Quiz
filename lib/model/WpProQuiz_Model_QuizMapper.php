<?php

class WpProQuiz_Model_QuizMapper extends WpProQuiz_Model_Mapper
{
    protected $_table;

    function __construct()
    {
        parent::__construct();

        $this->_table = $this->_prefix . "master";
    }

    public function delete($id)
    {
        $this->_wpdb->delete($this->_table, array(
            'id' => $id
        ),
            array('%d'));
    }

    public function exists($id)
    {
        return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE id = %d", $id));
    }

    /**
     * @param $id
     * @return WpProQuiz_Model_Quiz
     */
    public function fetch($id)
    {
        $results = $this->_wpdb->get_row(
            $this->_wpdb->prepare(
                "SELECT
									m.*,
									c.category_name  
								FROM
									{$this->_table} AS m
									LEFT JOIN {$this->_tableCategory} AS c
										ON c.category_id = m.category_id
								WHERE
									id = %d",
                $id),
            ARRAY_A
        );

        if ($results['result_grade_enabled']) {
            $results['result_text'] = unserialize($results['result_text']);
        }

        return new WpProQuiz_Model_Quiz($results);
    }

    /**
     * @return WpProQuiz_Model_Quiz[]
     */
    public function fetchAll()
    {
        $r = array();

        $results = $this->_wpdb->get_results(
            "
				SELECT 
					m.*,
					c.category_name 
				FROM 
					{$this->_table} AS m
					LEFT JOIN {$this->_tableCategory} AS c
						ON c.category_id = m.category_id
			"
            , ARRAY_A);

        foreach ($results as $row) {

            if ($row['result_grade_enabled']) {
                $row['result_text'] = unserialize($row['result_text']);
            }

            $r[] = new WpProQuiz_Model_Quiz($row);
        }

        return $r;
    }

    /**
     * @param $orderBy
     * @param $order
     * @param $search
     * @param $limit
     * @param $offset
     * @param $filter
     *
     * @return array
     */
    public function fetchTable($orderBy, $order, $search, $limit, $offset, $filter)
    {
        $r = array();

        switch ($orderBy) {
            case 'category';
                $_orderBy = 'c.category_name';
                break;
            default:
                $_orderBy = 'm.name';
                break;
        }

        $whereFilter = '';

        if ($filter) {
            if (isset($filter['cat']) && $filter['cat']) {
                $whereFilter = ' AND m.category_id = ' . ((int)$filter['cat']);
            }
        }

        $results = $this->_wpdb->get_results($this->_wpdb->prepare(
            "
				SELECT
					m.*,
					c.category_name
				FROM
					{$this->_table} AS m
					LEFT JOIN {$this->_tableCategory} AS c
						ON c.category_id = m.category_id
				WHERE
					m.name LIKE %s
					{$whereFilter}
				ORDER BY
					{$_orderBy} " . ($order == 'asc' ? 'asc' : 'desc') . "
				LIMIT %d, %d
			",
            array(
                '%' . $search . '%',
                $offset,
                $limit
            )), ARRAY_A);

        foreach ($results as $row) {

            if ($row['result_grade_enabled']) {
                $row['result_text'] = unserialize($row['result_text']);
            }

            $r[] = new WpProQuiz_Model_Quiz($row);
        }

        $count = $this->_wpdb->get_var($this->_wpdb->prepare(
            "
				SELECT
					COUNT(*) as count_rows
				FROM
					{$this->_table} AS m
				WHERE
					m.name LIKE %s
					{$whereFilter}
			",
            array(
                '%' . $search . '%'
            )));

        return array(
            'quiz' => $r,
            'count' => $count ? $count : 0
        );
    }

    public function save(WpProQuiz_Model_Quiz $data)
    {

        if ($data->isResultGradeEnabled()) {
            $resultText = serialize($data->getResultText());
        } else {
            $resultText = $data->getResultText();
        }

        $set = array(
            'name' => $data->getName(),
            'text' => $data->getText(),
            'result_text' => $resultText,
            'title_hidden' => (int)$data->isTitleHidden(),
            'btn_restart_quiz_hidden' => (int)$data->isBtnRestartQuizHidden(),
            'btn_view_question_hidden' => (int)$data->isBtnViewQuestionHidden(),
            'question_random' => (int)$data->isQuestionRandom(),
            'answer_random' => (int)$data->isAnswerRandom(),
            'time_limit' => (int)$data->getTimeLimit(),
            'statistics_on' => (int)$data->isStatisticsOn(),
            'statistics_ip_lock' => (int)$data->getStatisticsIpLock(),
            'result_grade_enabled' => (int)$data->isResultGradeEnabled(),
            'show_points' => (int)$data->isShowPoints(),
            'quiz_run_once' => (int)$data->isQuizRunOnce(),
            'quiz_run_once_type' => $data->getQuizRunOnceType(),
            'quiz_run_once_cookie' => (int)$data->isQuizRunOnceCookie(),
            'quiz_run_once_time' => (int)$data->getQuizRunOnceTime(),
            'numbered_answer' => (int)$data->isNumberedAnswer(),
            'hide_answer_message_box' => (int)$data->isHideAnswerMessageBox(),
            'disabled_answer_mark' => (int)$data->isDisabledAnswerMark(),
            'show_max_question' => (int)$data->isShowMaxQuestion(),
            'show_max_question_value' => (int)$data->getShowMaxQuestionValue(),
            'show_max_question_percent' => (int)$data->isShowMaxQuestionPercent(),
            'toplist_activated' => (int)$data->isToplistActivated(),
            'toplist_data' => $data->getToplistData(),
            'show_average_result' => (int)$data->isShowAverageResult(),
            'prerequisite' => (int)$data->isPrerequisite(),
            'quiz_modus' => (int)$data->getQuizModus(),
            'show_review_question' => (int)$data->isShowReviewQuestion(),
            'quiz_summary_hide' => (int)$data->isQuizSummaryHide(),
            'skip_question_disabled' => (int)$data->isSkipQuestionDisabled(),
            'email_notification' => $data->getEmailNotification(),
            'user_email_notification' => (int)$data->isUserEmailNotification(),
            'show_category_score' => (int)$data->isShowCategoryScore(),
            'hide_result_correct_question' => (int)$data->isHideResultCorrectQuestion(),
            'hide_result_quiz_time' => (int)$data->isHideResultQuizTime(),
            'hide_result_points' => (int)$data->isHideResultPoints(),
            'autostart' => (int)$data->isAutostart(),
            'forcing_question_solve' => (int)$data->isForcingQuestionSolve(),
            'hide_question_position_overview' => (int)$data->isHideQuestionPositionOverview(),
            'hide_question_numbering' => (int)$data->isHideQuestionNumbering(),
            'form_activated' => (int)$data->isFormActivated(),
            'form_show_position' => $data->getFormShowPosition(),
            'start_only_registered_user' => (int)$data->isStartOnlyRegisteredUser(),
            'questions_per_page' => $data->getQuestionsPerPage(),
            'sort_categories' => (int)$data->isSortCategories(),
            'show_category' => (int)$data->isShowCategory(),
            'category_id' => (int)$data->getCategoryId(),
            'admin_email' => $data->getAdminEmail(true),
            'user_email' => $data->getUserEmail(true),
            'plugin_container' => $data->getPluginContainer(true)
        );

        if ($data->getId() != 0) {
            $result = $this->_wpdb->update($this->_table,
                $set,
                array(
                    'id' => $data->getId()
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s'
                ),
                array('%d'));
        } else {

            $result = $this->_wpdb->insert($this->_table,
                $set,
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s'
                ));

            $data->setId($this->_wpdb->insert_id);
        }

        if ($result === false) {
            return null;
        }

        return $data;
    }

    /**
     * @param $id
     * @return int
     */
    public function sumQuestionPoints($id)
    {
        return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT SUM(points) FROM {$this->_tableQuestion} WHERE quiz_id = %d AND online = 1",
            $id));
    }

    public function countQuestion($id)
    {
        return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_tableQuestion} WHERE quiz_id = %d AND online = 1",
            $id));
    }

    public function fetchAllAsArray($list, $outIds = array())
    {
        $where = ' 1 ';

        if (!empty($outIds)) {
            $where .= ' AND id NOT IN(' . implode(', ', array_map('intval', (array)$outIds)) . ') ';
        }

        return $this->_wpdb->get_results(
            "SELECT " . implode(', ', (array)$list) . " FROM {$this->_tableMaster} WHERE $where ORDER BY name",
            ARRAY_A
        );
    }

    public function fetchCol($ids, $col)
    {
        $ids = implode(', ', array_map('intval', (array)$ids));

        return $this->_wpdb->get_col("SELECT {$col} FROM {$this->_tableMaster} WHERE id IN({$ids})");
    }

    public function activateStatitic($quizIds, $lockIpTime)
    {
        $quizIds = implode(', ', array_map('intval', (array)$quizIds));

        return $this->_wpdb->query($this->_wpdb->prepare(
            "UPDATE {$this->_tableMaster}
			SET `statistics_on` = 1, `statistics_ip_lock` = %d 
			WHERE `statistics_on` = 0 AND id IN(" . $quizIds . ")"
            , $lockIpTime));
    }

    public function deleteAll($quizId)
    {
        return $this->_wpdb->query(
            $this->_wpdb->prepare(
                "DELETE
					m, q, l, p, t, f, sr, s 
				FROM 
					{$this->_tableMaster} AS m 
					LEFT JOIN {$this->_tableQuestion} AS q ON(q.quiz_id = m.id) 
					LEFT JOIN {$this->_tableLock} AS l ON(l.quiz_id = m.id) 
					LEFT JOIN {$this->_tablePrerequisite} AS p ON(p.prerequisite_quiz_id = m.id) 
					LEFT JOIN {$this->_tableToplist} AS t ON(t.quiz_id = m.id) 
					LEFT JOIN {$this->_tableForm} AS f ON(f.quiz_id = m.id)
					LEFT JOIN {$this->_tableStatisticRef} AS sr ON(sr.quiz_id = m.id) 
						LEFT JOIN {$this->_tableStatistic} AS s ON(s.statistic_ref_id = sr.statistic_ref_id) 
				WHERE 
					m.id = %d"
                , $quizId)
        );
    }

    public function setMultipeCategories($quizIds, $categoryId)
    {
        $quizIds = implode(', ', array_map('intval', (array)$quizIds));

        return $this->_wpdb->query($this->_wpdb->prepare(
            "UPDATE
					{$this->_tableMaster}
				SET 
					`category_id` = %d
				WHERE id IN(" . $quizIds . ")"
            , $categoryId));
    }
}