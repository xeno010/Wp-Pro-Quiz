<?php

class WpProQuiz_Model_GlobalSettings extends WpProQuiz_Model_Model
{

    protected $_addRawShortcode = false;
    protected $_jsLoadInHead = false;
    protected $_touchLibraryDeactivate = false;
    protected $_corsActivated = false;

    public function setAddRawShortcode($_addRawShortcode)
    {
        $this->_addRawShortcode = (bool)$_addRawShortcode;

        return $this;
    }

    public function isAddRawShortcode()
    {
        return $this->_addRawShortcode;
    }

    public function setJsLoadInHead($_jsLoadInHead)
    {
        $this->_jsLoadInHead = (bool)$_jsLoadInHead;

        return $this;
    }

    public function isJsLoadInHead()
    {
        return $this->_jsLoadInHead;
    }

    public function setTouchLibraryDeactivate($_touchLibraryDeactivate)
    {
        $this->_touchLibraryDeactivate = (bool)$_touchLibraryDeactivate;

        return $this;
    }

    public function isTouchLibraryDeactivate()
    {
        return $this->_touchLibraryDeactivate;
    }

    public function setCorsActivated($_corsActivated)
    {
        $this->_corsActivated = (bool)$_corsActivated;

        return $this;
    }

    public function isCorsActivated()
    {
        return $this->_corsActivated;
    }
}