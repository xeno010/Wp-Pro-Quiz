<?php

class WpProQuiz_Model_StatisticOverview extends WpProQuiz_Model_Model
{

    protected $_correctCount = 0;
    protected $_incorrectCount = 0;
    protected $_hintCount = 0;
    protected $_points = 0;
    protected $_userName = '';
    protected $_quizId = 0;
    protected $_userId = 0;
    protected $_questionTime = 0;
    protected $_gPoints = 0;

    public function setCorrectCount($_correctCount)
    {
        $this->_correctCount = (int)$_correctCount;

        return $this;
    }

    public function getCorrectCount()
    {
        return $this->_correctCount;
    }

    public function setIncorrectCount($_incorrectCount)
    {
        $this->_incorrectCount = (int)$_incorrectCount;

        return $this;
    }

    public function getIncorrectCount()
    {
        return $this->_incorrectCount;
    }

    public function setHintCount($_hintCount)
    {
        $this->_hintCount = (int)$_hintCount;

        return $this;
    }

    public function getHintCount()
    {
        return $this->_hintCount;
    }

    public function setPoints($_points)
    {
        $this->_points = (int)$_points;

        return $this;
    }

    public function getPoints()
    {
        return $this->_points;
    }

    public function setUserName($_userName)
    {
        $this->_userName = (string)$_userName;

        return $this;
    }

    public function getUserName()
    {
        return $this->_userName;
    }

    public function setQuizId($_quizId)
    {
        $this->_quizId = (int)$_quizId;

        return $this;
    }

    public function getQuizId()
    {
        return $this->_quizId;
    }

    public function setUserId($_userId)
    {
        $this->_userId = (int)$_userId;

        return $this;
    }

    public function getUserId()
    {
        return $this->_userId;
    }

    public function setQuestionTime($_questionTime)
    {
        $this->_questionTime = (int)$_questionTime;

        return $this;
    }

    public function getQuestionTime()
    {
        return $this->_questionTime;
    }

    public function setGPoints($_gPoints)
    {
        $this->_gPoints = (int)$_gPoints;

        return $this;
    }

    public function getGPoints()
    {
        return $this->_gPoints;
    }

}