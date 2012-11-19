<?php
class WpProQuiz_Model_Lock extends WpProQuiz_Model_Model {
	protected $_quizId;
	protected $_lockIp;
	protected $_lockDate;
	
	public function setQuizId($_quizId) {
		$this->_quizId = $_quizId;
		return $this;
	}
	
	public function getQuizId() {
		return $this->_quizId;
	}
	
	public function setLockIp($_lockIp) {
		$this->_lockIp = $_lockIp;
		return $this;
	}
	
	public function getLockIp() {
		return $this->_lockIp;
	}
	
	public function setLockDate($_lockDate) {
		$this->_lockDate = $_lockDate;
		return $this;
	}
	
	public function getLockDate() {
		return $this->_lockDate;
	}
}