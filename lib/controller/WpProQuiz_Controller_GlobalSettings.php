<?php

class WpProQuiz_Controller_GlobalSettings extends WpProQuiz_Controller_Controller
{

    public function route()
    {
        $this->edit();
    }

    private function edit()
    {

        if (!current_user_can('wpProQuiz_change_settings')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $mapper = new WpProQuiz_Model_GlobalSettingsMapper();
        $categoryMapper = new WpProQuiz_Model_CategoryMapper();
        $templateMapper = new WpProQuiz_Model_TemplateMapper();

        $view = new WpProQuiz_View_GobalSettings();

        if (isset($this->_post['submit'])) {
            $mapper->save(new WpProQuiz_Model_GlobalSettings($this->_post));
            WpProQuiz_View_View::admin_notices(__('Settings saved', 'wp-pro-quiz'), 'info');

            $toplistDateFormat = $this->_post['toplist_date_format'];

            if ($toplistDateFormat == 'custom') {
                $toplistDateFormat = trim($this->_post['toplist_date_format_custom']);
            }

            $statisticTimeFormat = $this->_post['statisticTimeFormat'];

            if (add_option('wpProQuiz_toplistDataFormat', $toplistDateFormat) === false) {
                update_option('wpProQuiz_toplistDataFormat', $toplistDateFormat);
            }

            if (add_option('wpProQuiz_statisticTimeFormat', $statisticTimeFormat, '', 'no') === false) {
                update_option('wpProQuiz_statisticTimeFormat', $statisticTimeFormat);
            }
        } else {
            if (isset($this->_post['databaseFix'])) {
                WpProQuiz_View_View::admin_notices(__('Database repaired', 'wp-pro-quiz'), 'info');

                $DbUpgradeHelper = new WpProQuiz_Helper_DbUpgrade();
                $DbUpgradeHelper->databaseDelta();
            }
        }

        $view->settings = $mapper->fetchAll();
        $view->isRaw = !preg_match('[raw]', apply_filters('the_content', '[raw]a[/raw]'));
        $view->category = $categoryMapper->fetchAll();
        $view->categoryQuiz = $categoryMapper->fetchAll(WpProQuiz_Model_Category::CATEGORY_TYPE_QUIZ);
        $view->email = $mapper->getEmailSettings();
        $view->userEmail = $mapper->getUserEmailSettings();
        $view->templateQuiz = $templateMapper->fetchAll(WpProQuiz_Model_Template::TEMPLATE_TYPE_QUIZ, false);
        $view->templateQuestion = $templateMapper->fetchAll(WpProQuiz_Model_Template::TEMPLATE_TYPE_QUESTION, false);

        $view->toplistDataFormat = get_option('wpProQuiz_toplistDataFormat', 'Y/m/d g:i A');
        $view->statisticTimeFormat = get_option('wpProQuiz_statisticTimeFormat', 'Y/m/d g:i A');

        $view->show();
    }
}