<?php

class WpProQuiz_Model_StatisticFormOverview extends WpProQuiz_Model_Model
{

    protected $_userId = 0;
    protected $_userName = '';
    protected $_statisticRefId = 0;
    protected $_quizId = 0;
    protected $_createTime = 0;
    protected $_correctCount = 0;
    protected $_incorrectCount = 0;
    protected $_points = 0;

    public function setUserId($_userId)
    {
        $this->_userId = (int)$_userId;

        return $this;
    }

    public function getUserId()
    {
        return $this->_userId;
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

    public function setStatisticRefId($_statisticRefId)
    {
        $this->_statisticRefId = (int)$_statisticRefId;

        return $this;
    }

    public function getStatisticRefId()
    {
        return $this->_statisticRefId;
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

    public function setCreateTime($_createTime)
    {
        $this->_createTime = (int)$_createTime;

        return $this;
    }

    public function getCreateTime()
    {
        return $this->_createTime;
    }

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

    public function setPoints($_points)
    {
        $this->_points = (int)$_points;

        return $this;
    }

    public function getPoints()
    {
        return $this->_points;
    }

}