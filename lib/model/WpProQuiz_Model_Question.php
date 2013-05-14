<?php
class WpProQuiz_Model_Question extends WpProQuiz_Model_Model {
	protected $_id;
	protected $_quizId;
	protected $_sort;
	protected $_title;
	protected $_question;
	protected $_correctMsg;
	protected $_incorrectMsg;
	protected $_answerType;
	protected $_correctSameText = false;
	protected $_tipEnabled = false;
	protected $_tipMsg;
	protected $_points = 1;
	protected $_showPointsInBox = false;
	
	//0.19
	protected $_answerPointsActivated = false;
	protected $_answerData = null;
	
	//0.23
	protected $_categoryId = 0;
	
	//0.24
	protected $_categoryName = '';

	public function setId($_id) {
		$this->_id = $_id;
		return $this;
	}
	
	public function getId() {
		return $this->_id;
	}
	
	public function setQuizId($_quizId) {
		$this->_quizId = $_quizId;
		return $this;
	}
	
	public function getQuizId() {
		return $this->_quizId;
	}
	
	public function setSort($_sort) {
		$this->_sort = $_sort;
		return $this;
	}
	
	public function getSort() {
		return $this->_sort;
	}
	
	public function setTitle($title) {
		$this->_title = $title;
		return $this;
	}
	
	public function getTitle() {
		return $this->_title;
	}
	
	public function setQuestion($question) {
		$this->_question = $question;
		return $this;
	}
	
	public function getQuestion() {
		return $this->_question;
	}
	
	public function setCorrectMsg($correctMsg) {
		$this->_correctMsg = $correctMsg;
		return $this;
	}
	
	public function getCorrectMsg() {
		return $this->_correctMsg;
	}
	
	public function setIncorrectMsg($incorrectMsg) {
		$this->_incorrectMsg = $incorrectMsg;
		return $this;
	}
	
	public function getIncorrectMsg() {
		return $this->_incorrectMsg;
	}
	
	public function setAnswerType($_answerType) {
		$this->_answerType = $_answerType;
		return $this;
	}
	
	public function getAnswerType() {
		return $this->_answerType;
	}
	
	public function setCorrectSameText($_correctSameText) {
		$this->_correctSameText = (bool)$_correctSameText;
		return $this;
	}
	
	public function isCorrectSameText() {
		return $this->_correctSameText;
	}
	
	public function setTipEnabled($_tipEnabled) {
		$this->_tipEnabled = (bool)$_tipEnabled;
		return $this;
	}
	
	public function isTipEnabled() {
		return $this->_tipEnabled;
	}
	
	public function setTipMsg($_tipMsg) {
		$this->_tipMsg = $_tipMsg;
		return $this;
	}
	
	public function getTipMsg() {
		return $this->_tipMsg;
	}
	
	public function setPoints($_points) {
		$this->_points = (int)$_points;
		return $this;
	}
	
	public function getPoints() {
		return $this->_points;
	}
	
	public function setShowPointsInBox($_showPointsInBox) {
		$this->_showPointsInBox = (bool)$_showPointsInBox;
		return $this;
	}
	
	public function isShowPointsInBox() {
		return $this->_showPointsInBox;
	}
	
	public function setAnswerPointsActivated($_answerPointsActivated) {
		$this->_answerPointsActivated = (bool)$_answerPointsActivated;
		return $this;
	}
	
	public function isAnswerPointsActivated() {
		return $this->_answerPointsActivated;
	}
	
	public function setAnswerData($_answerData) {
		$this->_answerData = $_answerData;
		
		return $this;
	}
	
	public function getAnswerData($serialize = false) {
		if($this->_answerData === null)
			return null;
		
		if(is_array($this->_answerData) || $this->_answerData instanceof WpProQuiz_Model_AnswerTypes) {
			if($serialize) {
				return @serialize($this->_answerData);
			}
		} else {
			if(!$serialize) {
				if(WpProQuiz_Helper_Until::saveUnserialize($this->_answerData, $into) === false) {
					return null;
				}
				
				$this->_answerData = $into;
			}
		}
		
		return $this->_answerData;
	}
	
	public function setCategoryId($_categoryId) {
		$this->_categoryId = (int)$_categoryId;
		return $this;
	}
	
	public function getCategoryId() {
		return $this->_categoryId;
	}
	
	public function setCategoryName($_categoryName) {
		$this->_categoryName = (string)$_categoryName;
		return $this;
	}
	
	public function getCategoryName() {
		return $this->_categoryName;
	}
}