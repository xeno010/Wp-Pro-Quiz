<?php
class WpProQuiz_Model_QuizMapper extends WpProQuiz_Model_Mapper
{
	protected $_table; 
	
	function __construct() {
		parent::__construct();
		
		$this->_table = $this->_prefix."master";
	}
	
	public function delete($id) {
		$this->_wpdb->delete($this->_table, array(
				'id' => $id),
				array('%d'));
	}
	
	public function fetch($id) {
		$results = $this->_wpdb->get_row(
					$this->_wpdb->prepare(
								"SELECT 
									* 
								FROM
									{$this->_table}
								WHERE
									id = %d",
								$id),
								ARRAY_A
					);
		
		return new WpProQuiz_Model_Quiz($results);
	}
	
	public function fetchAll() {
		$r = array();
		
		$results = $this->_wpdb->get_results("SELECT * FROM {$this->_table}", ARRAY_A);

		foreach ($results as $row) {
			$r[] =  new WpProQuiz_Model_Quiz($row);
		}
		
		return $r;
	}
	
	public function save(WpProQuiz_Model_Quiz $data) {
		
		$set = array(
			'name' => $data->getName(),
			'text' => $data->getText(),
			'result_text' => $data->getResultText(),
			'title_hidden' => (int)$data->isTitleHidden(),
			'question_random' => (int)$data->isQuestionRandom(),
			'answer_random' => (int)$data->isAnswerRandom(),
			'back_button' => (int)$data->isBackButton(),
			'check_answer' => (int) $data->isCheckAnswer(),
			'time_limit' => (int)$data->getTimeLimit()
		);
		
		if($data->getId() != 0) {
			return $this->_wpdb->update($this->_table,
					$set,
					array(
							'id' => $data->getId()
					),
					array('%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d'),
					array('%d'));
		} else {
			return $this->_wpdb->insert($this->_table,
					$set,
					array('%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d'));
		}
	}
}