<?php

class WpProQuiz_Controller_Template
{
    public static function ajaxEditTemplate($data)
    {
        if (!current_user_can('wpProQuiz_edit_quiz')) {
            return json_encode(array());
        }

        $templateMapper = new WpProQuiz_Model_TemplateMapper();

        $template = new WpProQuiz_Model_Template($data);

        $templateMapper->updateName($template->getTemplateId(), $template->getName());

        return json_encode(array());
    }

    public static function ajaxDeleteTemplate($data)
    {
        if (!current_user_can('wpProQuiz_edit_quiz')) {
            return json_encode(array());
        }

        $templateMapper = new WpProQuiz_Model_TemplateMapper();

        $template = new WpProQuiz_Model_Template($data);

        $templateMapper->delete($template->getTemplateId());

        return json_encode(array());
    }
} 