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
						'answer_type' => $question->getAnswerType(),
						'answer_json' => json_encode($question->getAnswerJson())),
					array('id' => $question->getId()),
					array('%s', '%s', '%s', '%s', '%s', '%s'),
					array('%d'));
		} else {
			$id = $this->_wpdb->insert($this->_table, array(
					'quiz_id' => $question->getQuizId(),
					'sort' => $this->count($question->getQuizId()),
					'title' => $question->getTitle(),
					'question' => $question->getQuestion(),
					'correct_msg' => $question->getCorrectMsg(),
					'incorrect_msg' => $question->getIncorrectMsg(),
					'answer_type' => $question->getAnswerType(),
					'answer_json' => json_encode($question->getAnswerJson())
				),
				array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
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
				$id)
		);
		
		$model = new WpProQuiz_Model_Question();
		$model	->setId($row->id)
				->setQuizId($row->quiz_id)
				->setTitle($row->title)
				->setQuestion($row->question)
				->setCorrectMsg($row->correct_msg)
				->setIncorrectMsg($row->incorrect_msg)
				->setAnswerType($row->answer_type)
				->setAnswerJson(json_decode($row->answer_json, true));
		
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
						ORDER BY sort ASC', $quizId));
		
		foreach($results as $row) {
			$model = new WpProQuiz_Model_Question();
			
			$model	->setId($row->id)
					->setQuizId($row->quiz_id)
					->setTitle($row->title)
					->setQuestion($row->question)
					->setCorrectMsg($row->correct_msg)
					->setIncorrectMsg($row->incorrect_msg)
					->setAnswerType($row->answer_type)
					->setAnswerJson(json_decode($row->answer_json, true));
			
			$a[] = $model;
		}
		
		return $a;
	}
	
	public function count($quizId) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE quiz_id = %d", $quizId));
	}
}