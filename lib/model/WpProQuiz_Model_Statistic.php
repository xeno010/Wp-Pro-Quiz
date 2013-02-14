<?php
class WpProQuiz_Model_Statistic extends WpProQuiz_Model_Model {
	protected $_quizId;
	protected $_questionId = 0;
	protected $_userId = 0;
	protected $_correctCount = 0;
	protected $_incorrectCount = 0;
	protected $_hintCout = 0;
	protected $_userName = '';
	protected $_points = 0;
	protected $_totalPoints = 0;
	
	public function setQuizId($_quizId) {
		$this->_quizId = (int)$_quizId;
		return $this;
	}
	
	public function getQuizId() {
		return $this->_quizId;
	}
	
	public function setQuestionId($_questionId) {
		$this->_questionId = (int)$_questionId;
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
		$this->_correctCount = (int)$_correctCount;
		return $this;
	}
	
	public function getCorrectCount() {
		return (int)$this->_correctCount;
	}
	
	public function setIncorrectCount($_incorrectCount)	{
		$this->_incorrectCount = (int)$_incorrectCount;
		return $this;
	}
	
	public function getIncorrectCount() {
		return (int)$this->_incorrectCount;
	}
	
	public function setHintCount($_hintCout) {
		$this->_hintCout = (int)$_hintCout;
		return $this;
	}
	
	public function getHintCount() {
		return (int)$this->_hintCout;
	}
	
	public function setUserName($_userName) {
		$this->_userName = $_userName;
		return $this;
	}
	
	public function getUserName() {
		return $this->_userName;
	}
	
	public function setPoints($_points) {
		$this->_points = (int)$_points;
		return $this;
	}
	
	public function getPoints() {
		return $this->_points;
	}
	
	public function setTotalPoints($_totalPoints) {
		$this->_totalPoints = (int)$_totalPoints;
		return $this;
	}
	
	public function getTotalPoints() {
		return $this->_totalPoints;
	}
}