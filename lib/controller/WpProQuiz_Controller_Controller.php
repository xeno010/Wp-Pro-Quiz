<?php

class WpProQuiz_Controller_Controller
{
    protected $_post = null;
    protected $_cookie = null;

    /**
     * @deprecated
     */
    public function __construct()
    {
        if ($this->_post === null) {
            $this->_post = stripslashes_deep($_POST);
        }

        if ($this->_cookie === null && $_COOKIE !== null) {
            $this->_cookie = stripslashes_deep($_COOKIE);
        }
    }
}