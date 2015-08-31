<?php

class WpProQuiz_Model_Form extends WpProQuiz_Model_Model
{
    const FORM_TYPE_TEXT = 0;
    const FORM_TYPE_TEXTAREA = 1;
    const FORM_TYPE_NUMBER = 2;
    const FORM_TYPE_CHECKBOX = 3;
    const FORM_TYPE_EMAIL = 4;
    const FORM_TYPE_YES_NO = 5;
    const FORM_TYPE_DATE = 6;
    const FORM_TYPE_SELECT = 7;
    const FORM_TYPE_RADIO = 8;

    protected $_formId = 0;
    protected $_quizId = 0;
    protected $_fieldname = '';
    protected $_type = 0;
    protected $_required = false;
    protected $_sort = 0;
    protected $_data = null;
    protected $_showInStatistic = false;

    public function setFormId($_formId)
    {
        $this->_formId = (int)$_formId;

        return $this;
    }

    public function getFormId()
    {
        return $this->_formId;
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

    public function setFieldname($_fieldname)
    {
        $this->_fieldname = (string)$_fieldname;

        return $this;
    }

    public function getFieldname()
    {
        return $this->_fieldname;
    }

    public function setType($_type)
    {
        $this->_type = (int)$_type;

        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setRequired($_required)
    {
        $this->_required = (bool)$_required;

        return $this;
    }

    public function isRequired()
    {
        return $this->_required;
    }

    public function setSort($_sort)
    {
        $this->_sort = (int)$_sort;

        return $this;
    }

    public function getSort()
    {
        return $this->_sort;
    }

    public function setData($_data)
    {
        $this->_data = $_data === null ? null : (array)$_data;

        return $this;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setShowInStatistic($_showInStatistic)
    {
        $this->_showInStatistic = (bool)$_showInStatistic;

        return $this;
    }

    public function isShowInStatistic()
    {
        return $this->_showInStatistic;
    }
}