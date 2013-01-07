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
						'answer_json' => json_encode($question->getAnswerJson()),
						'points_per_answer' => (int)$question->isPointsPerAnswer(),
						'points_answer' => (int)$question->getPointsAnswer(),
						'show_points_in_box' => (int)$question->isShowPointsInBox()
					),
					array('id' => $question->getId()),
					array('%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%d'),
					array('%d'));
		} else {
			$id = $this->_wpdb->insert($this->_table, array(
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
					'answer_json' => json_encode($question->getAnswerJson()),
					'points_per_answer' => (int)$question->isPointsPerAnswer(),
					'points_answer' => (int)$question->getPointsAnswer(),
					'show_points_in_box' => (int)$question->isShowPointsInBox()
				),
				array('%d', '%d', '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%d')
			);
			
			$question->setId($id);
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
		
		$row['answer_json'] = json_decode($row['answer_json'], true);
		
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
			$row['answer_json'] = json_decode($row['answer_json'], true);
				
			$a[] = new WpProQuiz_Model_Question($row);
			
		}
		
		return is_array($id) ? $a : (isset($a[0]) ? $a[0] : null);
	}
	
	public function fetchAll($quizId) {
		$a = array();
		$results = $this->_wpdb->get_results(
				$this->_wpdb->prepare(
						'SELECT 
							* 
						FROM 
							'. $this->_table.'
						WHERE
							quiz_id = %d 
						ORDER BY sort ASC', $quizId),
				ARRAY_A);
		
		foreach($results as $row) {
			
			$row['answer_json'] = json_decode($row['answer_json'], true);
			
			$model = new WpProQuiz_Model_Question($row);
			
// 			$model = new WpProQuiz_Model_Question();
			
// 			$model	->setId($row->id)
// 					->setQuizId($row->quiz_id)
// 					->setTitle($row->title)
// 					->setQuestion($row->question)
// 					->setCorrectMsg($row->correct_msg)
// 					->setIncorrectMsg($row->incorrect_msg)
// 					->setAnswerType($row->answer_type)
// 					->setAnswerJson(json_decode($row->answer_json, true))
// 					->setCorrectCount($row->correct_count)
// 					->setIncorrectCount($row->incorrect_count)
// 					->setSort($row->sort);
			
			$a[] = $model;
		}
		
		return $a;
	}
	
	public function count($quizId) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE quiz_id = %d", $quizId));
	}
	
	public function exists($id) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE id = %d", $id));
	}	

	public function updateStatistics($quizId, $array) {
		$ids = $this->_wpdb->get_col($this->_wpdb->prepare("SELECT id FROM {$this->_table} WHERE quiz_id = %d", $quizId));
		
		$ak = array_keys($array);
		
		if(array_diff($ids, $ak) !== array_diff($ak, $ids))
			return false;
		
		$correctIds = $incorrectIds = $tipIds = array();
		
		foreach($array as $k => $v) {
			if(isset($v['tip'])) {
				$tipIds[] = $k;
			}
			
			if($v['correct']) {
				$correctIds[] = $k;
			} else {
				$incorrectIds[] = $k;
			}
		}
		
		$correctIds = implode(', ', $correctIds);			
		$incorrectIds = implode(', ', $incorrectIds);
		$tipIds = implode(', ', $tipIds);
		
		if(!empty($correctIds)) {
			$this->_wpdb->query("UPDATE {$this->_table}	SET	correct_count = correct_count + 1 WHERE	id IN({$correctIds})");
		}
		
		if(!empty($incorrectIds)) {
			$this->_wpdb->query("UPDATE	{$this->_table} SET	incorrect_count = incorrect_count + 1 WHERE	id IN({$incorrectIds})");
		}
		
		if(!empty($tipIds)) {
			$this->_wpdb->query("UPDATE	{$this->_table} SET	tip_count = tip_count + 1 WHERE	id IN({$tipIds})");
		}
		
		return true;
	}
	
	public function resetStatistics($quizId) {
		return $this->_wpdb->update($this->_table, 
					array(	'incorrect_count' => 0,
							'correct_count' => 0,
							'tip_count' => 0
							), 
					array(	'quiz_id' => $quizId),
					array(	'%d', '%d', '%d'),
					array(	'%d'));
	}
}