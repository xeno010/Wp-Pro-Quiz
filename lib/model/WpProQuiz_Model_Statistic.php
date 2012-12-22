<?php
class WpProQuiz_Model_Statistic extends WpProQuiz_Model_Model {
	protected $_quizId;
	protected $_questionId;
	protected $_userId;
	protected $_correctCount;
	protected $_incorrectCount;
	protected $_hintCout;
	
	public function setQuizId($_quizId) {
		$this->_quizId = $_quizId;
		return $this;
	}
	
	public function getQuizId() {
		return $this->_quizId;
	}
	
	public function setQuestionId($_questionId) {
		$this->_questionId = $_questionId;
		return $this;
	}
	
	public function getQuestionId() {
		return $this->_questionId;
	}
	
	public function setUserId($_userId) {
		$this->_userId = $_userId;
		return $this;
	}
	
	public function getUserId() {
		return $this->_userId;
	}
	
	public function setCorrectCount($_correctCount) {
		$this->_correctCount = $_correctCount;
		return $this;
	}
	
	public function getCorrectCount() {
		return (int)$this->_correctCount;
	}
	
	public function setIncorrectCount($_incorrectCount)	{
		$this->_incorrectCount = $_incorrectCount;
		return $this;
	}
	
	public function getIncorrectCount() {
		return (int)$this->_incorrectCount;
	}
	
	public function setHintCount($_hintCout) {
		$this->_hintCout = $_hintCout;
		return $this;
	}
	
	public function getHintCount() {
		return (int)$this->_hintCout;
	}
}