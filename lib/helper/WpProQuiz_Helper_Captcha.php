<?php

class WpProQuiz_Helper_Captcha
{

    private static $INSTANCE = null;

    private $_captcha = null;
    private $_supp = false;
    private $_prefix = '';

    public function __construct()
    {
        if (!class_exists('ReallySimpleCaptcha')) {
            return;
        }

        $this->_captcha = new ReallySimpleCaptcha();

        $this->_captcha->tmp_dir = WPPROQUIZ_CAPTCHA_DIR . '/';
        $this->_captcha->file_mode = 0666;
        $this->_captcha->answer_file_mode = 0666;

        if (!$this->_captcha->make_tmp_dir()) {
            $this->_supp = false;

            return;
        }

        $this->_supp = true;
    }

    public function getPrefix()
    {
        return $this->_prefix;
    }

    public function isSupported()
    {
        return $this->_supp;
    }

    public function createImage()
    {
        if (!$this->isSupported()) {
            return false;
        }

        $w = $this->_captcha->generate_random_word();
        $this->_prefix = mt_rand();

        return $this->_captcha->generate_image($this->_prefix, $w);
    }

    public function remove($prefix)
    {
        if (!$this->isSupported()) {
            return;
        }

        $this->_captcha->remove($prefix);
    }

    public function check($prefix, $answer)
    {
        if (!$this->isSupported()) {
            return;
        }

        return $this->_captcha->check($prefix, $answer);
    }

    public function cleanup()
    {
        if (!$this->isSupported()) {
            return;
        }

        $this->_captcha->cleanup();
    }

    /**
     * @return WpProQuiz_Helper_Captcha
     */
    public static function getInstance()
    {
        if (WpProQuiz_Helper_Captcha::$INSTANCE == null) {
            WpProQuiz_Helper_Captcha::$INSTANCE = new WpProQuiz_Helper_Captcha();
        }

        return WpProQuiz_Helper_Captcha::$INSTANCE;
    }
}