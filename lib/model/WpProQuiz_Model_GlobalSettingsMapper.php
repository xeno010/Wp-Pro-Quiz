<?php

class WpProQuiz_Model_GlobalSettingsMapper extends WpProQuiz_Model_Mapper
{

    public function fetchAll()
    {
        $s = new WpProQuiz_Model_GlobalSettings();

        $s->setAddRawShortcode(get_option('wpProQuiz_addRawShortcode'))
            ->setJsLoadInHead(get_option('wpProQuiz_jsLoadInHead'))
            ->setTouchLibraryDeactivate(get_option('wpProQuiz_touchLibraryDeactivate'))
            ->setCorsActivated(get_option('wpProQuiz_corsActivated'));

        return $s;
    }

    public function save(WpProQuiz_Model_GlobalSettings $settings)
    {

        if (add_option('wpProQuiz_addRawShortcode', $settings->isAddRawShortcode()) === false) {
            update_option('wpProQuiz_addRawShortcode', $settings->isAddRawShortcode());
        }

        if (add_option('wpProQuiz_jsLoadInHead', $settings->isJsLoadInHead()) === false) {
            update_option('wpProQuiz_jsLoadInHead', $settings->isJsLoadInHead());
        }

        if (add_option('wpProQuiz_touchLibraryDeactivate', $settings->isTouchLibraryDeactivate()) === false) {
            update_option('wpProQuiz_touchLibraryDeactivate', $settings->isTouchLibraryDeactivate());
        }

        if (add_option('wpProQuiz_corsActivated', $settings->isCorsActivated()) === false) {
            update_option('wpProQuiz_corsActivated', $settings->isCorsActivated());
        }
    }

    public function delete()
    {
        delete_option('wpProQuiz_addRawShortcode');
        delete_option('wpProQuiz_jsLoadInHead');
        delete_option('wpProQuiz_touchLibraryDeactivate');
        delete_option('wpProQuiz_corsActivated');
    }

    /**
     * @return array
     */
    public function getEmailSettings()
    {
        $e = get_option('wpProQuiz_emailSettings', null);

        if ($e === null) {
            $e['to'] = '';
            $e['from'] = '';
            $e['subject'] = __('Wp-Pro-Quiz: One user completed a quiz', 'wp-pro-quiz');#
            $e['html'] = false;
            $e['message'] = __('Wp-Pro-Quiz

The user "$username" has completed "$quizname" the quiz.

Points: $points
Result: $result

', 'wp-pro-quiz');

        }

        return $e;
    }

    public function saveEmailSettiongs($data)
    {
        if (isset($data['html']) && $data['html']) {
            $data['html'] = true;
        } else {
            $data['html'] = false;
        }

        if (add_option('wpProQuiz_emailSettings', $data, '', 'no') === false) {
            update_option('wpProQuiz_emailSettings', $data);
        }
    }

    /**
     * @return array
     */
    public function getUserEmailSettings()
    {
        $e = get_option('wpProQuiz_userEmailSettings', null);

        if ($e === null) {
            $e['from'] = '';
            $e['subject'] = __('Wp-Pro-Quiz: One user completed a quiz', 'wp-pro-quiz');
            $e['html'] = false;
            $e['message'] = __('Wp-Pro-Quiz

You have completed the quiz "$quizname".

Points: $points
Result: $result

', 'wp-pro-quiz');

        }

        return $e;

    }

    public function saveUserEmailSettiongs($data)
    {
        if (isset($data['html']) && $data['html']) {
            $data['html'] = true;
        } else {
            $data['html'] = false;
        }

        if (add_option('wpProQuiz_userEmailSettings', $data, '', 'no') === false) {
            update_option('wpProQuiz_userEmailSettings', $data);
        }
    }
}