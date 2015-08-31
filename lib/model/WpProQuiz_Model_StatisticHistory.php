<?php

class WpProQuiz_Model_StatisticHistory extends WpProQuiz_Model_Model
{

    protected $_userId = 0;
    protected $_userName = '';
    protected $_statisticRefId = 0;
    protected $_quizId = 0;
    protected $_createTime = 0;
    protected $_correctCount = 0;
    protected $_incorrectCount = 0;
    protected $_points = 0;
    protected $_result = 0;
    protected $_formatTime = '';
    protected $_formatCorrect = '';
    protected $_formatIncorrect = '';
    protected $_gPoints = 0;
    protected $_formData = null;
    protected $_formOverview = array();
    protected $_solvedCount = 0;

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

    public function setResult($_result)
    {
        $this->_result = (float)$_result;

        return $this;
    }

    public function getResult()
    {
        return $this->_result;
    }

    public function setFormatTime($_formatTime)
    {
        $this->_formatTime = (string)$_formatTime;

        return $this;
    }

    public function getFormatTime()
    {
        return $this->_formatTime;
    }

    public function setFormatCorrect($_formatCorrect)
    {
        $this->_formatCorrect = (string)$_formatCorrect;

        return $this;
    }

    public function getFormatCorrect()
    {
        return $this->_formatCorrect;
    }

    public function setFormatIncorrect($_formatIncorrect)
    {
        $this->_formatIncorrect = (string)$_formatIncorrect;

        return $this;
    }

    public function getFormatIncorrect()
    {
        return $this->_formatIncorrect;
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

    public function setFormData($_formData)
    {
        $this->_formData = (array)$_formData;

        return $this;
    }

    public function getFormData()
    {
        return $this->_formData;
    }

    public function setFormOverview($_formOverview)
    {
        $this->_formOverview = (array)$_formOverview;

        return $this;
    }

    public function getFormOverview()
    {
        return $this->_formOverview;
    }

    public function setSolvedCount($_solvedCount)
    {
        $this->_solvedCount = (int)$_solvedCount;

        return $this;
    }

    public function getSolvedCount()
    {
        return $this->_solvedCount;
    }
}
