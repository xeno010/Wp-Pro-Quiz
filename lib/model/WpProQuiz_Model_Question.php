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
	protected $_answerJson;
	protected $_correctCount;
	protected $_incorrectCount;
	protected $_correctSameText = false;
	protected $_tipEnabled = false;
	protected $_tipMsg;
	protected $_tipCount;
	
	public function setId($_id)
	{
		$this->_id = $_id;
		return $this;
	}
	
	public function getId() {
		return $this->_id;
	}
	
	public function setQuizId($_quizId)
	{
		$this->_quizId = $_quizId;
		return $this;
	}
	
	public function getQuizId() {
		return $this->_quizId;
	}
	
	public function setSort($_sort)
	{
		$this->_sort = $_sort;
		return $this;
	}
	
	public function getSort() {
		return $this->_sort;
	}
	
	public function setTitle($title)
	{
		$this->_title = $title;
		return $this;
	}
	
	public function getTitle() {
		return $this->_title;
	}
	
	public function setQuestion($question)
	{
		$this->_question = $question;
		return $this;
	}
	
	public function getQuestion() {
		return $this->_question;
	}
	
	public function setCorrectMsg($correctMsg)
	{
		$this->_correctMsg = $correctMsg;
		return $this;
	}
	
	public function getCorrectMsg() {
		return $this->_correctMsg;
	}
	
	public function setIncorrectMsg($incorrectMsg)
	{
		$this->_incorrectMsg = $incorrectMsg;
		return $this;
	}
	
	public function getIncorrectMsg() {
		return $this->_incorrectMsg;
	}
	
	public function setAnswerType($_answerType)
	{
		$this->_answerType = $_answerType;
		return $this;
	}
	
	public function getAnswerType() {
		return $this->_answerType;
	}
	
	public function setAnswerJson($answerJson)
	{
		$this->_answerJson = $answerJson;
		
		if(isset($this->_answerJson['answer_type']))
			$this->setAnswerType($this->_answerJson['answer_type']);
		
		return $this;
	}
	
	public function getAnswerJson() {
		return $this->_answerJson;
	}
	
	public function setCorrectCount($_correctCount)
	{
		$this->_correctCount = $_correctCount;
		return $this;
	}
	
	public function getCorrectCount() {
		return (int)$this->_correctCount;
	}
	
	public function setIncorrectCount($_incorrectCount)
	{
		$this->_incorrectCount = $_incorrectCount;
		return $this;
	}
	
	public function getIncorrectCount() {
		return (int)$this->_incorrectCount;
	}
	
	public function setCorrectSameText($_correctSameText)
	{
		$this->_correctSameText = (bool)$_correctSameText;
		return $this;
	}
	
	public function isCorrectSameText() {
		return $this->_correctSameText;
	}
	
	public function setTipEnabled($_tipEnabled)
	{
		$this->_tipEnabled = (bool)$_tipEnabled;
		return $this;
	}
	
	public function isTipEnabled() {
		return $this->_tipEnabled;
	}
	
	public function setTipMsg($_tipMsg)
	{
		$this->_tipMsg = $_tipMsg;
		return $this;
	}
	
	public function getTipMsg() {
		return $this->_tipMsg;
	}
	
	public function setTipCount($_tipCount)
	{
		$this->_tipCount = (int)$_tipCount;
		return $this;
	}
	
	public function getTipCount() {
		return $this->_tipCount;
	}
}