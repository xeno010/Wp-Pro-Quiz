<?php
class WpProQuiz_Model_StatisticRefModel extends WpProQuiz_Model_Model {

	protected $_statisticRefId = 0;
	protected $_quizId = 0;
	protected $_userId = 0;
	protected $_createTime = 0;
	protected $_isOld = false;

	public function setStatisticRefId($_statisticRefId) {
		$this->_statisticRefId = (int)$_statisticRefId;
		return $this;
	}

	public function getStatisticRefId() {
		return $this->_statisticRefId;
	}

	public function setQuizId($_quizId) {
		$this->_quizId = (int)$_quizId;
		return $this;
	}

	public function getQuizId() {
		return $this->_quizId;
	}

	public function setUserId($_userId) {
		$this->_userId = (int)$_userId;
		return $this;
	}

	public function getUserId() {
		return $this->_userId;
	}

	public function setCreateTime($_createTime) {
		$this->_createTime = (int)$_createTime;
		return $this;
	}

	public function getCreateTime() {
		return $this->_createTime;
	}

	public function setIsOld($_isOld) {
		$this->_isOld = (bool)$_isOld;
		return $this;
	}

	public function isIsOld() {
		return $this->_isOld;
	}
}