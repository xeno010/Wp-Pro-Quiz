<?php
class WpProQuiz_Model_Quiz extends WpProQuiz_Model_Model {
	
	protected $_id;
	protected $_name;
	protected $_text;
	protected $_resultText;
	protected $_titleHidden;
	protected $_questionRandom;
	protected $_answerRandom;
	protected $_timeLimit = 0;
	protected $_backButton;
	protected $_checkAnswer;
	
	public function getId() {
		return $this->_id;
	}
	
	public function setId($id) {
		$this->_id = $id;
		return $this;
	}
	
	public function getName() {
		return $this->_name;
	}
	
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}
	
	/**
	 * Test
	 * @param string $text
	 * @return WpProQuiz_Model_Quiz
	 */
	public function setText($text) {
		$this->_text = $text;
		return $this;
	}
	
	public function getText() {
		return $this->_text;
	}
	
	public function setResultText($_resultText) {
		$this->_resultText = $_resultText;
		return $this;
	}
	
	public function getResultText() {
		return $this->_resultText;
	}
	
	public function setTitleHidden($_titleHidden) {
		$this->_titleHidden = (bool)$_titleHidden;
		return $this;
	}
	
	public function isTitleHidden() {
		return $this->_titleHidden;
	}
	
	public function setQuestionRandom($_questionRandom) {
		$this->_questionRandom = (bool)$_questionRandom;
		return $this;
	}
	
	public function isQuestionRandom() {
		return $this->_questionRandom;
	}

	public function setAnswerRandom($_answerRandom) {
		$this->_answerRandom = (bool)$_answerRandom;
		return $this;
	}
	
	public function isAnswerRandom() {
		return $this->_answerRandom;
	}
	
	public function setTimeLimit($_timeLimit) {
		$this->_timeLimit = $_timeLimit;
		return $this;
	}
	
	public function getTimeLimit() {
		return $this->_timeLimit;
	}
	
	public function setBackButton($_backButton) {
		$this->_backButton = (bool)$_backButton;
		return $this;
	}
	
	public function isBackButton() {
		return $this->_backButton;
	}
	
	public function setCheckAnswer($_checkAnswer) {
		$this->_checkAnswer = (bool)$_checkAnswer;
		return $this;
	}
	
	public function isCheckAnswer() {
		return $this->_checkAnswer;
	}
}