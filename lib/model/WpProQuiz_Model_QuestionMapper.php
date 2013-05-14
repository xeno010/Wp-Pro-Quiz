<?php
class WpProQuiz_Model_QuestionMapper extends WpProQuiz_Model_Mapper {
	private $_table;

	public function __construct() {
		parent::__construct();
		
		$this->_table = $this->_prefix."question";
	}
	
	public function delete($id) {
		$this->_wpdb->delete($this->_table, array('id' => $id), '%d');
	}
	
	public function deleteByQuizId($id) {
		$this->_wpdb->delete($this->_table, array('quiz_id' => $id), '%d');
	}
	
	public function updateSort($id, $sort) {
		$this->_wpdb->update(
				$this->_table,
				array(
						'sort' => $sort),
				array('id' => $id),
				array('%d'),
				array('%d'));
	}
	
	public function save(WpProQuiz_Model_Question $question) {
		if($question->getId() != 0) {
			$this->_wpdb->update(
					$this->_table, 
					array(
						'title' => $question->getTitle(),
						'points' => $question->getPoints(),
						'question' => $question->getQuestion(),
						'correct_msg' => $question->getCorrectMsg(),
						'incorrect_msg' => $question->getIncorrectMsg(),
						'correct_same_text' => (int)$question->isCorrectSameText(),
						'tip_enabled' => (int)$question->isTipEnabled(),
						'tip_msg' => $question->getTipMsg(),
						'answer_type' => $question->getAnswerType(),
						'show_points_in_box' => (int)$question->isShowPointsInBox(),
						'answer_points_activated' => (int)$question->isAnswerPointsActivated(),
						'answer_data' => $question->getAnswerData(true),
						'category_id' => $question->getCategoryId()
					),
					array('id' => $question->getId()),
					array('%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%s', '%d'),
					array('%d'));
		} else {
			$this->_wpdb->insert($this->_table, array(
					'quiz_id' => $question->getQuizId(),
					'sort' => $this->count($question->getQuizId()),
					'title' => $question->getTitle(),
					'points' => $question->getPoints(),
					'question' => $question->getQuestion(),
					'correct_msg' => $question->getCorrectMsg(),
					'incorrect_msg' => $question->getIncorrectMsg(),
					'correct_same_text' => (int)$question->isCorrectSameText(),
					'tip_enabled' => (int)$question->isTipEnabled(),
					'tip_msg' => $question->getTipMsg(),
					'answer_type' => $question->getAnswerType(),
					'show_points_in_box' => (int)$question->isShowPointsInBox(),
					'answer_points_activated' => (int)$question->isAnswerPointsActivated(),
					'answer_data' => $question->getAnswerData(true),
					'category_id' => $question->getCategoryId()
				),
				array('%d', '%d', '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%s', '%d')
			);
			
			$question->setId($this->_wpdb->insert_id);
		}
		
		return $question;
	}
	
	public function fetch($id) {
		
		$row = $this->_wpdb->get_row(
			$this->_wpdb->prepare(
				"SELECT
					*
				FROM
					". $this->_table. "
				WHERE
					id = %d",
				$id),
			ARRAY_A
		);
		
		$model = new WpProQuiz_Model_Question($row);
	
		return $model;
	}
	
	public function fetchById($id) {
		
		$ids = array_map('intval', (array)$id);
		$a = array();
		
		if(empty($ids))
			return null;
		
		$results = $this->_wpdb->get_results(
				"SELECT
					*
				FROM
					". $this->_table. "
				WHERE
					id IN(".implode(', ', $ids).")",
				ARRAY_A
		);
		
		foreach ($results as $row) {
			$a[] = new WpProQuiz_Model_Question($row);
			
		}
		
		return is_array($id) ? $a : (isset($a[0]) ? $a[0] : null);
	}
	
	public function fetchAll($quizId, $rand = false, $max = 0) {
		
		if($rand) {
			$orderBy = 'ORDER BY RAND()';
		} else {
			$orderBy = 'ORDER BY sort ASC';
		}
		
		$limit = '';
		
		if($max > 0) {
			$limit = 'LIMIT 0, '.((int)$max);
		}
		
		$a = array();
		$results = $this->_wpdb->get_results(
				$this->_wpdb->prepare(
							'SELECT 
								q.*,
								c.category_name 
							FROM 
								'. $this->_table.' AS q
								LEFT JOIN '.$this->_tableCategory.' AS c
									ON c.category_id = q.category_id
							WHERE
								quiz_id = %d 
							'.$orderBy.' 
							'.$limit
						, $quizId),
				ARRAY_A);
		
		foreach($results as $row) {
			$model = new WpProQuiz_Model_Question($row);
			
			$a[] = $model;
		}
		
		return $a;
	}
	
	public function fetchAllList($quizId, $list) {
		$results = $this->_wpdb->get_results(
				$this->_wpdb->prepare(
						'SELECT
								'.implode(', ', (array)$list).'
							FROM
								'. $this->_tableQuestion.'
							WHERE
								quiz_id = %d'
						, $quizId),
				ARRAY_A);
		
		return $results;
	}
	
	public function count($quizId) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE quiz_id = %d", $quizId));
	}
	
	public function exists($id) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE id = %d", $id));
	}	
}