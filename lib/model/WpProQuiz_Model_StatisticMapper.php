<?php
class WpProQuiz_Model_StatisticMapper extends WpProQuiz_Model_Mapper {
	
	private $_table;

	public function __construct() {
		parent::__construct();
		
		$this->_table = $this->_prefix.'statistic';
	}
	
	public function fetch($quizId, $questionId, $userId) {
		
	}
	
	public function fetchAll($quizId, $userId) {
		$a = array();
		
		$results = $this->_wpdb->get_results(
				$this->_wpdb->prepare(
						'SELECT
							*
						FROM
							'. $this->_table.'
						WHERE
							quiz_id = %d AND
							user_id = %d', $quizId, $userId),
				ARRAY_A);
		
		foreach($results as $row) {
			$a[] = new WpProQuiz_Model_Statistic($row);
		}
		
		return $a;
	}
	
	public function fetchOverview($quizId, $onlyCompleded, $start, $limit) {
			$sql = 'SELECT 
						u.`user_login`, u.`display_name`, u.ID AS user_id,
						SUM(s.`correct_count`) as correct_count,
						SUM(s.`incorrect_count`) as incorrect_count,
						SUM(s.`hint_count`) as hint_count,
						SUM(q.`points_answer` * s.`correct_answer_count`) as points,
						SUM(q.`points` * (s.`correct_count` + s.`incorrect_count`)) as total_points
					FROM 
						`'.$this->_wpdb->users.'` AS u
						'.($onlyCompleded ? 'INNER' : 'LEFT').' JOIN `'.$this->_tableStatistic.'` AS s ON ( s.user_id = u.ID AND s.`quiz_id` = %d )
						LEFT JOIN `'.$this->_tableQuestion.'` AS q ON ( q.id = s.`question_id` )
					GROUP BY u.ID 
					ORDER BY u.`user_login`  
					LIMIT %d , %d';
			
		$a = array();
		
		$results = $this->_wpdb->get_results(
				$this->_wpdb->prepare($sql, $quizId, $start, $limit), 
				ARRAY_A);
		
		foreach($results as $row) {
			
			$row['user_name'] = $row['user_login'] . ' ('. $row['display_name'] .')';
			
			$a[] = new WpProQuiz_Model_Statistic($row);
		}
		
		return $a;	
			
	}
	
	public function countOverview($quizId, $onlyCompleded) {
		
		if($onlyCompleded) {
			return $this->_wpdb->get_var(
					$this->_wpdb->prepare(
							"SELECT 
								COUNT(ID)
							FROM {$this->_wpdb->users} 
							WHERE
								ID IN(SELECT user_id FROM wp_wp_pro_quiz_statistic WHERE quiz_id = %d GROUP BY user_id)",
							$quizId
				)
			);
		} else {
			return $this->_wpdb->get_var(
				"SELECT COUNT(ID) FROM {$this->_wpdb->users}"
			);
		}
	}
	
	public function delete($quizId, $userId) {
		$this->_wpdb->delete($this->_table, array(
			'quiz_id' => $quizId,
			'user_id' => $userId
		), array('%d', '%d'));
	}
	
	/**
	 * @deprecated
	 */
	public function deleteByQuiz($quizId) {
		$this->deleteByQuizId($quizId);
	}
	
	public function deleteByQuizId($quizId) {
		return $this->_wpdb->delete($this->_tableStatistic, array('quiz_id' => $quizId), array('%d'));
	}
	
	public function deleteByQuestionId($questionId) {
		return $this->_wpdb->delete($this->_tableStatistic, array('question_id' => $questionId), array('%d'));
	}
	
	public function save($quizId, $userId, $array) {
		$ids = $this->_wpdb->get_col($this->_wpdb->prepare("SELECT id FROM {$this->_tableQuestion} WHERE quiz_id = %d", $quizId));
	
		$ak = array_keys($array);
	
		if(array_diff($ids, $ak) !== array_diff($ak, $ids))
			return false;
		
		$values = array();
		$globalValue = array(
			'quiz_id' => $quizId,
			'question_id' => 0,
			'user_id' => $userId,
			'correct_count' => 0,
			'incorrect_count' => 0,
			'hint_count' => 0,
			'correct_answer_count' => 0
		);
		
		foreach($array as $k => $v) {
			
			$value = $globalValue;
			
			$value['question_id'] = $k;
			
			if(isset($v['tip'])) {
				$value['hint_count'] = 1;
			}
				
			if($v['correct']) {
				$value['correct_count'] = 1;
			} else {
				$value['incorrect_count'] = 1;
			}
			
			$value['correct_answer_count'] = isset($v['correct_answer_count']) ? (int)$v['correct_answer_count'] : 0;
			
			$values[] = '('.implode(', ', $value).')';
		}
		
		$this->_wpdb->query(
			'INSERT INTO 
				'.$this->_tableStatistic.' ('.implode(', ', array_keys($globalValue)).') 
			VALUES 
				'.implode(', ', $values).'
  			ON DUPLICATE KEY UPDATE 
				correct_count =  correct_count + VALUES(correct_count), 
				incorrect_count =  incorrect_count + VALUES(incorrect_count), 
				hint_count =  hint_count + VALUES(hint_count), 
				correct_answer_count = correct_answer_count + VALUES(correct_answer_count)'
		);
	}
}