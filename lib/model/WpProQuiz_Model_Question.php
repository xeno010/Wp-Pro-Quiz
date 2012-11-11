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
}