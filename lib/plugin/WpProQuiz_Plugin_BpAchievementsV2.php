<?php

class WpProQuiz_Plugin_BpAchievementsV2
{

    public function __construct()
    {
        add_filter('dpa_get_addedit_action_descriptions_category_name', array($this, 'setCategoryName'), 10, 2);

        add_action('wp_pro_quiz_completed_quiz', array($this, 'quizFinished'));
    }

    public function setCategoryName($category_name, $category)
    {
        $other = 'Other';

        if (__($other, 'dpa') == $category_name && 'Wp-Pro-Quiz' == $category) {
            return 'Wp-Pro-Quiz';
        } else {
            return $category_name;
        }
    }

    public function quizFinished()
    {
        do_action('wp_pro_quiz_quiz_finished');
    }

    public static function install()
    {
        global $wpdb;

        if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}achievements_actions'") === null) {
            return false;
        }

        $actions = array(
            array(
                'category' => 'Wp-Pro-Quiz',
                'name' => 'wp_pro_quiz_quiz_finished',
                'description' => __('The user completed a quiz.', 'wp-pro-quiz')
            )
        );

        foreach ($actions as $action) {
            if ($wpdb->get_var("SELECT id FROM {$wpdb->prefix}achievements_actions WHERE name = 'wp_pro_quiz_quiz_finished'") === null) {
                $wpdb->insert($wpdb->prefix . 'achievements_actions', $action);
            }
        }

        return true;
    }

    public static function deinstall()
    {
        global $wpdb;

        if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}achievements_actions'") === null) {
            return false;
        }

        return $wpdb->delete($wpdb->prefix . 'achievements_actions', array('name' => 'wp_pro_quiz_quiz_finished'));
    }
}

function dpa_handle_action_wp_pro_quiz_quiz_finished()
{
    $func_get_args = func_get_args();

    if (function_exists('dpa_handle_action')) {
        dpa_handle_action('wp_pro_quiz_quiz_finished', $func_get_args);
    }
}