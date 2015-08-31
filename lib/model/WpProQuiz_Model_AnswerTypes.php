<?php

class WpProQuiz_Model_AnswerTypes extends WpProQuiz_Model_Model
{
    protected $_answer = '';
    protected $_html = false;
    protected $_points = 1;

    protected $_correct = false;

    protected $_sortString = '';
    protected $_sortStringHtml = false;

    public function setAnswer($_answer)
    {
        $this->_answer = (string)$_answer;

        return $this;
    }

    public function getAnswer()
    {
        return $this->_answer;
    }

    public function setHtml($_html)
    {
        $this->_html = (bool)$_html;

        return $this;
    }

    public function isHtml()
    {
        return $this->_html;
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

    public function setCorrect($_correct)
    {
        $this->_correct = (bool)$_correct;

        return $this;
    }

    public function isCorrect()
    {
        return $this->_correct;
    }

    public function setSortString($_sortString)
    {
        $this->_sortString = (string)$_sortString;

        return $this;
    }

    public function getSortString()
    {
        return $this->_sortString;
    }

    public function setSortStringHtml($_sortStringHtml)
    {
        $this->_sortStringHtml = (bool)$_sortStringHtml;

        return $this;
    }

    public function isSortStringHtml()
    {
        return $this->_sortStringHtml;
    }
}