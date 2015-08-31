<?php

class WpProQuiz_Model_StatisticUser extends WpProQuiz_Model_Model
{

    protected $_correctCount = 0;
    protected $_incorrectCount = 0;
    protected $_hintCount = 0;
    protected $_points = 0;
    protected $_questionTime = 0;
    protected $_questionId = 0;
    protected $_questionName = '';
    protected $_gPoints = 0;
    protected $_categoryId = 0;
    protected $_categoryName = '';
    protected $_statisticAnswerData = null;
    protected $_questionAnswerData = null;
    protected $_answerType = '';
    protected $_solvedCount = 0;

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

    public function setQuestionTime($_questionTime)
    {
        $this->_questionTime = (int)$_questionTime;

        return $this;
    }

    public function getQuestionTime()
    {
        return $this->_questionTime;
    }

    public function setQuestionId($_questionId)
    {
        $this->_questionId = (int)$_questionId;

        return $this;
    }

    public function getQuestionId()
    {
        return $this->_questionId;
    }

    public function setQuestionName($_questionName)
    {
        $this->_questionName = (string)$_questionName;

        return $this;
    }

    public function getQuestionName()
    {
        return $this->_questionName;
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

    public function setCategoryId($_categoryId)
    {
        $this->_categoryId = (int)$_categoryId;

        return $this;
    }

    public function getCategoryId()
    {
        return $this->_categoryId;
    }

    public function setCategoryName($_categoryName)
    {
        $this->_categoryName = (string)$_categoryName;

        return $this;
    }

    public function getCategoryName()
    {
        return $this->_categoryName;
    }

    public function setStatisticAnswerData($_statisticAnswerData)
    {
        $this->_statisticAnswerData = $_statisticAnswerData;

        return $this;
    }

    public function getStatisticAnswerData()
    {
        return $this->_statisticAnswerData;
    }

    public function setQuestionAnswerData($_questionAnswerData)
    {
        $this->_questionAnswerData = null;

        if (WpProQuiz_Helper_Until::saveUnserialize($_questionAnswerData, $into) !== false) {
            $this->_questionAnswerData = $into;
        }

        return $this;
    }

    public function getQuestionAnswerData()
    {
        return $this->_questionAnswerData;
    }

    public function setAnswerType($_answerType)
    {
        $this->_answerType = (string)$_answerType;

        return $this;
    }

    public function getAnswerType()
    {
        return $this->_answerType;
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