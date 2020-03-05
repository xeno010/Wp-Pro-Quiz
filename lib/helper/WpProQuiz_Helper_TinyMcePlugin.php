<?php

class WpProQuiz_Helper_TinyMcePlugin
{

    public function __construct()
    {
        $this->addHooks();
    }

    protected function addHooks()
    {
        add_filter('mce_external_plugins', [$this, 'registerTinyMcePlugin']);
        add_filter('mce_buttons', [$this, 'addTinymceButton']);
        add_action('wp_ajax_wpProQuiz_generate_mce_shortcode', [$this, 'generateTinyMceShortcodeView']);
    }

    public function addTinymceButton($buttons)
    {
        $buttons[] = 'wp_pro_quiz_button_mce';

        return $buttons;
    }

    public function registerTinyMcePlugin($plugin_array)
    {
        $plugin_array['wp_pro_quiz_button_mce'] = plugins_url('js/wpProQuiz_mce_shortcode.js', WPPROQUIZ_FILE);

        return $plugin_array;
    }

    public function generateTinyMceShortcodeView()
    {
        $mapper = new WpProQuiz_Model_QuizMapper();

        $view = new WpProQuiz_View_TinyMceShortcodeWindow();
        $view->quizzes = $mapper->fetchAll();
        $view->show();

        die();
    }

    public static function init()
    {
        return new self();
    }
}
