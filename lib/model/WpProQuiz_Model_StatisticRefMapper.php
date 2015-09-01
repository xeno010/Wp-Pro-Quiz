<?php

class WpProQuiz_Model_StatisticRefMapper extends WpProQuiz_Model_Mapper
{

    public function fetchAll($quizId, $userId, $testId = 0)
    {
        $r = array();

        if (!$testId || $userId > 0) {
            $where = ' AND is_old = 0 ';
        } else {
            $where = ' AND statistic_ref_id = ' . (int)$testId;
        }

        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare(
                "SELECT * FROM {$this->_tableStatisticRef} WHERE quiz_id = %d AND user_id = %d {$where} ORDER BY create_time ASC"
                , $quizId, $userId)
            , ARRAY_A);

        foreach ($results as $row) {
            $row['form_data'] = null;

            $r[] = new WpProQuiz_Model_StatisticRefModel($row);
        }

        return $r;
    }

    public function fetchByRefId($refIdUserId, $quizId, $avg = false)
    {
        $where = $avg ? 'sf.user_id = %d' : 'sf.statistic_ref_id = %d';
        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare(
                "SELECT
					sf.*,
					MIN(sf.create_time) AS min_create_time,
					MAX(sf.create_time) AS max_create_time
				FROM 
					{$this->_tableStatisticRef} AS sf 
				WHERE 
					{$where} AND sf.quiz_id = %d"
                , $refIdUserId, $quizId)
            , ARRAY_A);

        foreach ($results as $row) {
            $row['form_data'] = $row['form_data'] === null ? null : @json_decode($row['form_data'], true);

            return new WpProQuiz_Model_StatisticRefModel($row);
        }

        return null;
    }

    public function fetchAvg($quizId, $userId)
    {
        $r = array();

        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare('
			SELECT
				question_id,
				SUM(correct_count) AS correct_count,
				SUM(incorrect_count) AS incorrect_count,
				SUM(hint_count) AS hint_count,
				SUM(points) AS points,
				(SUM(question_time) / COUNT(DISTINCT sf.statistic_ref_id)) AS question_time
			FROM
				' . $this->_tableStatistic . ' AS s,
				' . $this->_tableStatisticRef . ' AS sf
			WHERE
				s.statistic_ref_id = sf.statistic_ref_id
				AND
				sf.quiz_id = %d AND sf.user_id = %d
			GROUP BY s.question_id
			', $quizId, $userId)
            , ARRAY_A);

        foreach ($results as $row) {
            $r[] = new WpProQuiz_Model_Statistic($row);
        }

        return $r;
    }

    public function fetchOverview($quizId, $onlyCompleded, $start, $limit)
    {
        $sql = 'SELECT
						u.`user_login`, u.`display_name`, u.ID AS user_id,
						SUM(s.`correct_count`) as correct_count,
						SUM(s.`incorrect_count`) as incorrect_count,
						SUM(s.`hint_count`) as hint_count,
						SUM(s.`points`) as points,
						(SUM(s.question_time)) as question_time
					FROM
						`' . $this->_wpdb->users . '` AS u
						' . ($onlyCompleded ? 'INNER' : 'LEFT') . ' JOIN `' . $this->_tableStatisticRef . '` AS sf ON
								(sf.user_id = u.ID AND sf.quiz_id = %d)
						LEFT JOIN `' . $this->_tableStatistic . '` AS s ON ( s.statistic_ref_id = sf.statistic_ref_id )
					GROUP BY u.ID
					ORDER BY u.`user_login`
					LIMIT %d , %d';

        $a = array();

        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare($sql, $quizId, $start, $limit),
            ARRAY_A);

        foreach ($results as $row) {

            $row['user_name'] = $row['user_login'] . ' (' . $row['display_name'] . ')';

            $a[] = new WpProQuiz_Model_StatisticOverview($row);
        }

        return $a;

    }

    public function countOverview($quizId, $onlyCompleded)
    {

        if ($onlyCompleded) {
            return $this->_wpdb->get_var(
                $this->_wpdb->prepare(
                    "SELECT
							COUNT(user_id)
						FROM {$this->_tableStatisticRef}
						WHERE
							quiz_id = %d",
                    $quizId
                )
            );
        } else {
            return $this->_wpdb->get_var(
                "SELECT COUNT(ID) FROM {$this->_wpdb->users}"
            );
        }
    }

    public function fetchByQuiz($quizId)
    {
        $sql = 'SELECT
					(SUM(`correct_count`) + SUM(`incorrect_count`)) as count,
					SUM(`points`) as points
				FROM
					' . $this->_tableStatisticRef . ' AS sf,
					' . $this->_tableStatistic . ' AS s
				WHERE
					sf.quiz_id = %d AND s.statistic_ref_id = sf.statistic_ref_id';

        return $this->_wpdb->get_row(
            $this->_wpdb->prepare($sql, $quizId),
            ARRAY_A);
    }

    /**
     *
     * @param WpProQuiz_Model_StatisticRefModel $statisticRefModel
     * @param WpProQuiz_Model_Statistic[] $statisticModel
     */
    public function statisticSave($statisticRefModel, $statisticModel)
    {
        $values = array();

        $refId = null;
        $isOld = false;

// 		if(!$statisticRefModel->getUserId()) {
// 			$isOld = true;

// 			$refId = $this->_wpdb->get_var(
// 					$this->_wpdb->prepare('
// 						SELECT statistic_ref_id
// 						FROM '.$this->_tableStatisticRef.'
// 						WHERE quiz_id = %d AND user_id = %d
// 				', $statisticRefModel->getQuizId(), $statisticRefModel->getUserId())
// 			);
// 		}

        if ($refId === null) {

            $refData = array(
                'quiz_id' => $statisticRefModel->getQuizId(),
                'user_id' => $statisticRefModel->getUserId(),
                'create_time' => $statisticRefModel->getCreateTime(),
                'is_old' => (int)$isOld
            );

            $refFormat = array('%d', '%d', '%d', '%d');

            if ($statisticRefModel->getFormData() !== null && is_array($statisticRefModel->getFormData())) {
                $refData['form_data'] = @json_encode($statisticRefModel->getFormData());
                $refFormat[] = '%s';
            }

            $this->_wpdb->insert($this->_tableStatisticRef, $refData, $refFormat);

            $refId = $this->_wpdb->insert_id;
        }

        foreach ($statisticModel as $d) {
            $answerData = $d->getAnswerData() === null ? 'NULL' : $this->_wpdb->prepare('%s',
                json_encode($d->getAnswerData()));

            $values[] = '( ' . implode(', ', array(
                    'statistic_ref_id' => $refId,
                    'question_id' => $d->getQuestionId(),
                    'correct_count' => $d->getCorrectCount(),
                    'incorrect_count' => $d->getIncorrectCount(),
                    'hint_count' => $d->getHintCount(),
                    'solved_count' => $d->getSolvedCount(),
                    'points' => $d->getPoints(),
                    'question_time' => $d->getQuestionTime(),
                    'answer_data' => $answerData
                )) . ' )';
        }

        $this->_wpdb->query(
            'INSERT INTO
				' . $this->_tableStatistic . ' (
					statistic_ref_id, question_id, correct_count, incorrect_count, hint_count, solved_count, points, question_time, answer_data
				)
			VALUES
				' . implode(', ', $values)
        );
    }

    public function deleteUser($quizId, $userId)
    {
        return $this->_wpdb->query(
            $this->_wpdb->prepare('
				DELETE s, sf
				FROM ' . $this->_tableStatistic . ' AS s
					INNER JOIN ' . $this->_tableStatisticRef . ' AS sf
					ON s.statistic_ref_id = sf.statistic_ref_id
				WHERE
					sf.quiz_id = %d AND sf.user_id = %d
			', $quizId, $userId)
        );
    }

    public function deleteAll($quizId)
    {
        return $this->_wpdb->query(
            $this->_wpdb->prepare('
				DELETE s, sf
				FROM ' . $this->_tableStatistic . ' AS s
					INNER JOIN ' . $this->_tableStatisticRef . ' AS sf
					ON s.statistic_ref_id = sf.statistic_ref_id
				WHERE
					sf.quiz_id = %d
			', $quizId)
        );
    }

    public function deleteUserTest($quizId, $userId, $testId)
    {
        if (!$testId) {
            return $this->deleteUser($quizId, $userId);
        }

        return $this->_wpdb->query(
            $this->_wpdb->prepare('
				DELETE s, sf
				FROM ' . $this->_tableStatistic . ' AS s
					INNER JOIN ' . $this->_tableStatisticRef . ' AS sf
					ON s.statistic_ref_id = sf.statistic_ref_id
				WHERE
					sf.quiz_id = %d AND sf.user_id = %d AND sf.statistic_ref_id = %d
			', $quizId, $userId, $testId)
        );
    }

    public function deleteQuestion($questionId)
    {
        return $this->_wpdb->delete($this->_tableStatistic, array('question_id' => $questionId), array('%d'));
    }

    public function fetchFormOverview($quizId, $page, $limit, $onlyUser = 0)
    {

        switch ($onlyUser) {
            case 1:
                $where = ' AND sf.user_id > 0 ';
                break;
            case 2:
                $where = ' AND sf.user_id = 0 ';
                break;
            default:
                $where = '';
        }

        $result = $this->_wpdb->get_results(
            $this->_wpdb->prepare('
				SELECT 
					u.`user_login`, u.`display_name`, u.ID AS user_id, 
					sf.*,
					SUM(s.correct_count) AS correct_count,
					SUM(s.incorrect_count) AS incorrect_count,
					SUM(s.points) AS points
				FROM 
					' . $this->_tableStatisticRef . ' AS sf
					INNER JOIN ' . $this->_tableStatistic . ' AS s ON(s.statistic_ref_id = sf.statistic_ref_id)
					LEFT JOIN ' . $this->_wpdb->users . ' AS u ON(u.ID = sf.user_id)
				WHERE 
					quiz_id = %d AND sf.form_data IS NOT NULL ' . $where . '
				GROUP BY 
					sf.statistic_ref_id 
				ORDER BY  
					sf.create_time DESC
				LIMIT 
					%d, %d 
			', $quizId, $page, $limit),
            ARRAY_A
        );

        $r = array();

        foreach ($result as $row) {
            $row['user_name'] = $row['user_login'] . ' (' . $row['display_name'] . ')';

            $r[] = new WpProQuiz_Model_StatisticFormOverview($row);
        }

        return $r;
    }

    /**
     * @param $quizId
     * @param $page
     * @param $limit
     * @param int $users
     * @param int $startTime
     * @param int $endTime
     * @return WpProQuiz_Model_StatisticHistory[]
     */
    public function fetchHistory($quizId, $page, $limit, $users = -1, $startTime = 0, $endTime = 0)
    {
        $timeWhere = '';

        switch ($users) {
            case -3: //only anonym
                $where = 'AND user_id = 0';
                break;
            case -2: //only reg user
                $where = 'AND user_id > 0';
                break;
            case -1: //all
                $where = '';
                break;
            default:
                $where = 'AND user_id = ' . (int)$users;
                break;
        }

        if ($startTime) {
            $timeWhere = 'AND create_time >= ' . (int)$startTime;
        }

        if ($endTime) {
            $timeWhere .= ' AND create_time <= ' . (int)$endTime;
        }

        $result = $this->_wpdb->get_results(
            $this->_wpdb->prepare('
				SELECT
					u.`user_login`, u.`display_name`, u.ID AS user_id,
					sf.*,
					SUM(s.correct_count) AS correct_count,
					SUM(s.incorrect_count) AS incorrect_count,
					SUM(s.solved_count) as solved_count,
					SUM(s.points) AS points, 
					SUM(q.points) AS g_points 
				FROM
					' . $this->_tableStatisticRef . ' AS sf
					INNER JOIN ' . $this->_tableStatistic . ' AS s ON(s.statistic_ref_id = sf.statistic_ref_id)
					LEFT JOIN ' . $this->_wpdb->users . ' AS u ON(u.ID = sf.user_id)
					INNER JOIN ' . $this->_tableQuestion . ' AS q ON(q.id = s.question_id)
				WHERE
					sf.quiz_id = %d AND sf.is_old = 0 ' . $where . ' ' . $timeWhere . '
				GROUP BY
					sf.statistic_ref_id
				ORDER BY
					sf.create_time DESC
				LIMIT
					%d, %d
			', $quizId, $page, $limit),
            ARRAY_A
        );

        $r = array();

        foreach ($result as $row) {
            if (!empty($row['user_login'])) {
                $row['user_name'] = $row['user_login'] . ' (' . $row['display_name'] . ')';
            }

            $row['form_data'] = $row['form_data'] === null ? null : @json_decode($row['form_data'], true);

            $r[] = new WpProQuiz_Model_StatisticHistory($row);
        }

        return $r;
    }

    public function countFormOverview($quizId, $onlyUser)
    {

        switch ($onlyUser) {
            case 1:
                $where = ' AND user_id > 0 ';
                break;
            case 2:
                $where = ' AND user_id = 0 ';
                break;
            default:
                $where = '';
        }

        return $this->_wpdb->get_var(
            $this->_wpdb->prepare(
                "SELECT
					COUNT(user_id)
					FROM {$this->_tableStatisticRef}
					WHERE
					quiz_id = %d AND form_data IS NOT NULL " . $where, $quizId
            )
        );
    }

    public function countHistory($quizId, $users = -1, $startTime = 0, $endTime = 0)
    {
        $timeWhere = '';

        switch ($users) {
            case -3: //only anonym
                $where = 'AND user_id = 0';
                break;
            case -2: //only reg user
                $where = 'AND user_id > 0';
                break;
            case -1: //all
                $where = '';
                break;
            default:
                $where = 'AND user_id = ' . (int)$users;
                break;
        }

        if ($startTime) {
            $timeWhere = 'AND create_time >= ' . (int)$startTime;
        }

        if ($endTime) {
            $timeWhere .= ' AND create_time <= ' . (int)$endTime;
        }

        return $this->_wpdb->get_var(
            $this->_wpdb->prepare(
                "SELECT COUNT(user_id) FROM {$this->_tableStatisticRef} WHERE quiz_id = %d AND is_old = 0 {$where} {$timeWhere}",
                $quizId
            )
        );
    }

    public function deleteByRefId($refId)
    {
        return $this->_wpdb->query(
            $this->_wpdb->prepare('
				DELETE s, sf
				FROM ' . $this->_tableStatistic . ' AS s
					INNER JOIN ' . $this->_tableStatisticRef . ' AS sf
					ON (s.statistic_ref_id = sf.statistic_ref_id)
				WHERE
					sf.statistic_ref_id = %d
			', $refId)
        );
    }

    public function deleteByUserIdQuizId($userId, $quizId)
    {
        return $this->_wpdb->query(
            $this->_wpdb->prepare('
				DELETE s, sf
				FROM ' . $this->_tableStatistic . ' AS s
					INNER JOIN ' . $this->_tableStatisticRef . ' AS sf
					ON (s.statistic_ref_id = sf.statistic_ref_id)
				WHERE
					sf.user_id = %d AND sf.quiz_id = %d
			', $userId, $quizId)
        );
    }

    public function fetchStatisticOverview($quizId, $onlyCompleded, $start, $limit)
    {
        $a = array();

        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare(
                '(
						SELECT
							u.`user_login`, u.`display_name`, u.ID AS user_id,
							SUM(s.`correct_count`) as correct_count,
							SUM(s.`incorrect_count`) as incorrect_count,
							SUM(s.`hint_count`) as hint_count,
							SUM(s.`points`) as points,
							AVG(s.question_time) as question_time,
							SUM(q.points * (s.correct_count + s.incorrect_count)) AS g_points
						FROM
							' . $this->_wpdb->users . ' AS u
							' . ($onlyCompleded ? 'INNER' : 'LEFT') . ' JOIN ' . $this->_tableStatisticRef . ' AS sf ON (sf.user_id = u.ID AND sf.quiz_id = %d)
							LEFT JOIN ' . $this->_tableStatistic . ' AS s ON ( s.statistic_ref_id = sf.statistic_ref_id )
							LEFT JOIN ' . $this->_tableQuestion . ' AS q ON(q.id = s.question_id)
						GROUP BY u.ID
					)
						UNION
					(
						SELECT
							NULL, NULL, 0,
							SUM(s.`correct_count`) as correct_count,
							SUM(s.`incorrect_count`) as incorrect_count,
							SUM(s.`hint_count`) as hint_count,
							SUM(s.`points`) as points,
							AVG(s.question_time) as question_time,
							SUM(q.points * (s.correct_count + s.incorrect_count)) AS g_points
						FROM
							' . $this->_tableMaster . ' AS m
							' . ($onlyCompleded ? 'INNER' : 'LEFT') . ' JOIN ' . $this->_tableStatisticRef . ' AS sf ON(sf.quiz_id = m.id AND sf.user_id = 0)
							LEFT JOIN ' . $this->_tableStatistic . ' AS s ON (s.statistic_ref_id = sf.statistic_ref_id)
							LEFT JOIN ' . $this->_tableQuestion . ' AS q ON (q.id = s.question_id)
						WHERE
							m.id = %d
						GROUP BY sf.user_id
					)
					
					ORDER BY 
						user_login
					LIMIT
						%d, %d',
                $quizId, $quizId, $start, $limit),
            ARRAY_A
        );

        foreach ($results as $row) {
            if (!empty($row['user_login'])) {
                $row['user_name'] = $row['user_login'] . ' (' . $row['display_name'] . ')';
            }

            $a[] = new WpProQuiz_Model_StatisticOverview($row);
        }

        return $a;
    }

    public function countOverviewNew($quizId, $onlyCompleded)
    {
        return $this->_wpdb->get_var(
            $this->_wpdb->prepare(
                'SELECT
					COUNT(*) as g_count
				FROM
					(
						(SELECT
							u.ID
						FROM
							' . $this->_wpdb->users . ' AS u
							' . ($onlyCompleded ? 'INNER' : 'LEFT') . ' JOIN ' . $this->_tableStatisticRef . ' AS sf ON (sf.user_id = u.ID AND sf.quiz_id = %d)
							LEFT JOIN ' . $this->_tableStatistic . ' AS s ON ( s.statistic_ref_id = sf.statistic_ref_id )
							LEFT JOIN ' . $this->_tableQuestion . ' AS q ON(q.id = s.question_id)
						GROUP BY u.ID )
					UNION
						(SELECT
							sf.user_id
						FROM
							' . $this->_tableMaster . ' AS m
							' . ($onlyCompleded ? 'INNER' : 'LEFT') . ' JOIN ' . $this->_tableStatisticRef . ' AS sf ON(sf.quiz_id = m.id AND sf.user_id = 0)
							LEFT JOIN ' . $this->_tableStatistic . ' AS s ON (s.statistic_ref_id = sf.statistic_ref_id)
							LEFT JOIN ' . $this->_tableQuestion . ' AS q ON (q.id = s.question_id)
						WHERE
							m.id = %d
						GROUP BY sf.user_id) 
					) AS c_all',
                $quizId, $quizId));
    }

    public function fetchFrontAvg($quizId)
    {
        return $this->_wpdb->get_row($this->_wpdb->prepare(
            "SELECT
				SUM(s.points) AS points, 
				SUM(q.points * (s.correct_count + s.incorrect_count)) AS g_points 
			FROM 
				{$this->_tableStatisticRef} AS sf 
				INNER JOIN {$this->_tableStatistic} AS s ON ( s.statistic_ref_id = sf.statistic_ref_id ) 
				INNER JOIN {$this->_tableQuestion} AS q ON ( q.id = s.question_id ) 
			WHERE 
				sf.quiz_id = %d",
            $quizId), ARRAY_A);
    }
}