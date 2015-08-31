<?php

class WpProQuiz_Model_Template extends WpProQuiz_Model_Model
{

    const TEMPLATE_TYPE_QUIZ = 0;
    const TEMPLATE_TYPE_QUESTION = 1;

    protected $_templateId = 0;
    protected $_name = '';
    protected $_type = 0;
    protected $_data = null;

    public function setTemplateId($_templateId)
    {
        $this->_templateId = (int)$_templateId;

        return $this;
    }

    public function getTemplateId()
    {
        return $this->_templateId;
    }

    public function setName($_name)
    {
        $this->_name = (string)$_name;

        return $this;
    }

    public function getName()
    {
        return $this->_name;
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

    public function setData($_data)
    {
        $this->_data = $_data;

        return $this;
    }

    public function getData()
    {
        return $this->_data;
    }

}