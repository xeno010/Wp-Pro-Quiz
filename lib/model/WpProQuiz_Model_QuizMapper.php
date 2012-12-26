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
	
	public function exists($id) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE id = %d", $id));
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
		
		if($results['result_grade_enabled'])
			$results['result_text'] = unserialize($results['result_text']);
		
		return new WpProQuiz_Model_Quiz($results);
	}
	
	public function fetchAll() {
		$r = array();
		
		$results = $this->_wpdb->get_results("SELECT * FROM {$this->_table}", ARRAY_A);

		foreach ($results as $row) {
			
			if($row['result_grade_enabled'])
				$row['result_text'] = unserialize($row['result_text']);
			
			$r[] =  new WpProQuiz_Model_Quiz($row);
		}
		
		return $r;
	}
	
	public function save(WpProQuiz_Model_Quiz $data) {
		
		if($data->isResultGradeEnabled()) {
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
			'back_button' => (int)$data->isBackButton(),
			'check_answer' => (int) $data->isCheckAnswer(),
			'time_limit' => (int)$data->getTimeLimit(),
			'statistics_on' => (int)$data->isStatisticsOn(),
			'statistics_ip_lock' => (int)$data->getStatisticsIpLock(),
			'result_grade_enabled' => (int)$data->isResultGradeEnabled(),
			'show_points' => (int)$data->isShowPoints(),
			'quiz_run_once' => (int)$data->isQuizRunOnce(),
			'quiz_run_once_type' => $data->getQuizRunOnceType(),
			'quiz_run_once_cookie' => (int)$data->isQuizRunOnceCookie(),
			'quiz_run_once_time' => (int)$data->getQuizRunOnceTime(),
			'question_on_single_page' => (int)$data->isQuestionOnSinglePage(),
			'numbered_answer' => (int)$data->isNumberedAnswer()
		);
		
		if($data->getId() != 0) {
			$result = $this->_wpdb->update($this->_table,
					$set,
					array(
							'id' => $data->getId()
					),
					array('%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d'),
					array('%d'));
		} else {
			
			$result = $this->_wpdb->insert($this->_table,
						$set,
						array('%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d'));
			
			$data->setId($this->_wpdb->insert_id);
		}
		
		if($result === false) {
			return null;
		}
		
		return $data;
	}
	
	public function sumQuestionPoints($id) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT SUM(points) FROM {$this->_tableQuestion} WHERE quiz_id = %d", $id));
	}
	
	public function countQuestion($id) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_tableQuestion} WHERE quiz_id = %d", $id));
	}
}