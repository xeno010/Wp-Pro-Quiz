<?php
class WpProQuiz_Model_StatisticRefMapper extends WpProQuiz_Model_Mapper {
	
	public function fetchAll($quizId, $userId) {
		$r = array();
		
		$results = $this->_wpdb->get_results(
			$this->_wpdb->prepare(
				"SELECT * FROM {$this->_tableStatisticRef} WHERE quiz_id = %d AND user_id = %d AND is_old = 0 ORDER BY create_time ASC"
			, $quizId, $userId)
		, ARRAY_A);
		
		foreach ($results as $row) {
			$r[] =  new WpProQuiz_Model_StatisticRefModel($row);
		}
		
		return $r;
	}
	
	public function fetchAvg($quizId, $userId) {
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
				'.$this->_tableStatistic.' AS s,
				'.$this->_tableStatisticRef.' AS sf
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
	
	public function fetchOverview($quizId, $onlyCompleded, $start, $limit) {
		$sql = 'SELECT
						u.`user_login`, u.`display_name`, u.ID AS user_id,
						SUM(s.`correct_count`) as correct_count,
						SUM(s.`incorrect_count`) as incorrect_count,
						SUM(s.`hint_count`) as hint_count,
						SUM(s.`points`) as points,
						(SUM(s.question_time)) as question_time
					FROM
						`'.$this->_wpdb->users.'` AS u
						'.($onlyCompleded ? 'INNER' : 'LEFT').' JOIN `'.$this->_tableStatisticRef.'` AS sf ON
								(sf.user_id = u.ID AND sf.quiz_id = %d)
						LEFT JOIN `'.$this->_tableStatistic.'` AS s ON ( s.statistic_ref_id = sf.statistic_ref_id )
					GROUP BY u.ID
					ORDER BY u.`user_login`
					LIMIT %d , %d';
			
		$a = array();
	
		$results = $this->_wpdb->get_results(
				$this->_wpdb->prepare($sql, $quizId, $start, $limit),
				ARRAY_A);
	
		foreach($results as $row) {
				
			$row['user_name'] = $row['user_login'] . ' ('. $row['display_name'] .')';
				
			$a[] = new WpProQuiz_Model_StatisticOverview($row);
		}
	
		return $a;
			
	}
	
	public function countOverview($quizId, $onlyCompleded) {
	
		if($onlyCompleded) {
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
	
	public function fetchByQuiz($quizId) {
		$sql = 'SELECT
					(SUM(`correct_count`) + SUM(`incorrect_count`)) as count,
					SUM(`points`) as points
				FROM
					'.$this->_tableStatisticRef.' AS sf, 
					'.$this->_tableStatistic.' AS s
				WHERE
					sf.quiz_id = %d AND s.statistic_ref_id = sf.statistic_ref_id';
	
		return $this->_wpdb->get_row(
				$this->_wpdb->prepare($sql, $quizId),
				ARRAY_A);
	}
	
	/**
	 * 
	 * @param WpProQuiz_Model_StatisticRefModel $statisticRefModel
	 * @param WpProQuiz_Model_Statistic $statisticModel
	 */
	public function statisticSave($statisticRefModel, $statisticModel) {
		$values = array();
		$refId = null;
		$isOld = false;
		
		if(!$statisticRefModel->getUserId()) {
			$isOld = true;
			
			$refId = $this->_wpdb->get_var(
					$this->_wpdb->prepare('
						SELECT statistic_ref_id
						FROM '.$this->_tableStatisticRef.'
						WHERE quiz_id = %d AND user_id = %d
				', $statisticRefModel->getQuizId(), $statisticRefModel->getUserId())
			);
		}
	
		if($refId === null) {
			$this->_wpdb->insert($this->_tableStatisticRef, array(
					'quiz_id' => $statisticRefModel->getQuizId(),
					'user_id' => $statisticRefModel->getUserId(),
					'create_time' => $statisticRefModel->getCreateTime(),
					'is_old' => (int)$isOld
			), array(
					'%d', '%d', '%d', '%d'
			));
			
			$refId = $this->_wpdb->insert_id;
		}
	
		foreach($statisticModel as $d) {
			$values[] = '( '.implode(', ', array(
					'statistic_ref_id' => $refId,
					'question_id' => $d->getQuestionId(),
					'correct_count' => $d->getCorrectCount(),
					'incorrect_count' => $d->getIncorrectCount(),
					'hint_count' => $d->getHintCount(),
					'points' => $d->getPoints(),
					'question_time' => $d->getQuestionTime()
			)).' )';
		}
	
		$this->_wpdb->query(
				'INSERT INTO
				'.$this->_tableStatistic.' (
					statistic_ref_id, question_id, correct_count, incorrect_count, hint_count, points, question_time
				)
			VALUES
				'.implode(', ', $values).'
  			ON DUPLICATE KEY UPDATE
				correct_count =  correct_count + VALUES(correct_count),
				incorrect_count =  incorrect_count + VALUES(incorrect_count),
				hint_count =  hint_count + VALUES(hint_count),
				points =  points + VALUES(points),
				question_time = ((question_time + VALUES(question_time)) / 2)'
		);
	}
	
	public function deleteUser($quizId, $userId) {
		return $this->_wpdb->query(
			$this->_wpdb->prepare('
				DELETE s, sf
				FROM '.$this->_tableStatistic.' AS s
					INNER JOIN '.$this->_tableStatisticRef.' AS sf
					ON s.statistic_ref_id = sf.statistic_ref_id
				WHERE
					sf.quiz_id = %d AND sf.user_id = %d
			', $quizId, $userId)
		);
	}
	
	public function deleteAll($quizId) {
		return $this->_wpdb->query(
			$this->_wpdb->prepare('
				DELETE s, sf
				FROM '.$this->_tableStatistic.' AS s
					INNER JOIN '.$this->_tableStatisticRef.' AS sf
					ON s.statistic_ref_id = sf.statistic_ref_id
				WHERE
					sf.quiz_id = %d
			', $quizId)
		);
	}
	
	public function deleteUserTest($quizId, $userId, $testId) {
		if(!$testId)
			return $this->deleteUser($quizId, $userId);
		
		return $this->_wpdb->query(
			$this->_wpdb->prepare('
				DELETE s, sf
				FROM '.$this->_tableStatistic.' AS s
					INNER JOIN '.$this->_tableStatisticRef.' AS sf
					ON s.statistic_ref_id = sf.statistic_ref_id
				WHERE
					sf.quiz_id = %d AND sf.user_id = %d AND sf.statistic_ref_id = %d
			', $quizId, $userId, $testId)
		);
	}
	
	public function deleteQuestion($questionId) {
		return $this->_wpdb->delete($this->_tableStatistic, array('question_id' => $questionId), array('%d'));
	}
}