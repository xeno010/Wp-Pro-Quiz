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
						'question' => $question->getQuestion(),
						'correct_msg' => $question->getCorrectMsg(),
						'incorrect_msg' => $question->getIncorrectMsg(),
						'correct_same_text' => (int)$question->isCorrectSameText(),
						'answer_type' => $question->getAnswerType(),
						'answer_json' => json_encode($question->getAnswerJson())),
					array('id' => $question->getId()),
					array('%s', '%s', '%s', '%s', '%d', '%s', '%s'),
					array('%d'));
		} else {
			$id = $this->_wpdb->insert($this->_table, array(
					'quiz_id' => $question->getQuizId(),
					'sort' => $this->count($question->getQuizId()),
					'title' => $question->getTitle(),
					'question' => $question->getQuestion(),
					'correct_msg' => $question->getCorrectMsg(),
					'incorrect_msg' => $question->getIncorrectMsg(),
					'correct_same_text' => (int)$question->isCorrectSameText(),
					'answer_type' => $question->getAnswerType(),
					'answer_json' => json_encode($question->getAnswerJson())
				),
				array('%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
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
		
// 		$model = new WpProQuiz_Model_Question();
// 		$model	->setId($row->id)
// 				->setQuizId($row->quiz_id)
// 				->setTitle($row->title)
// 				->setQuestion($row->question)
// 				->setCorrectMsg($row->correct_msg)
// 				->setIncorrectMsg($row->incorrect_msg)
// 				->setAnswerType($row->answer_type)
// 				->setAnswerJson(json_decode($row->answer_json, true))
// 				->setCorrectCount($row->correct_count)
// 				->setIncorrectCount($row->incorrect_count)
// 				->setSort($row->sort);
		
		return $model;
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
		
		$v = array_keys($array);
		
		if(array_diff($ids, $v) !== array_diff($v, $ids))
			return false;
		
		$correctIds = implode(', ', array_keys($array, 1));			
		$incorrectIds = implode(', ', array_keys($array, 0));
		
		if(!empty($correctIds)) {
			$this->_wpdb->query("UPDATE {$this->_table}	SET	correct_count = correct_count + 1 WHERE	id IN({$correctIds})");
		}
		
		if(!empty($incorrectIds)) {
			$this->_wpdb->query("UPDATE	{$this->_table} SET	incorrect_count = incorrect_count + 1 WHERE	id IN({$incorrectIds})");
		}
		
		return true;
	}
	
	public function resetStatistics($quizId) {
		return $this->_wpdb->update($this->_table, 
					array(	'incorrect_count' => 0,
							'correct_count' => 0
							), 
					array(	'quiz_id' => $quizId),
					array(	'%d', '%d'),
					array(	'%d'));
	}
}