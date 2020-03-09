<?php

class WpProQuiz_Controller_ImportExport extends WpProQuiz_Controller_Controller
{

    public function route()
    {

        @set_time_limit(0);
        @ini_set('memory_limit', '128M');

        if (!isset($_GET['action']) || $_GET['action'] != 'import' && $_GET['action'] != 'export') {
            wp_die("Error");
        }

        if ($_GET['action'] == 'export') {
            $this->handleExport();
        } else {
            $this->handleImport();
        }
    }

    private function handleExport()
    {

        if (!current_user_can('wpProQuiz_export')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $helper = new WpProQuiz_Helper_QuizExport();
        $exporter = $helper->factory($this->_post['exportIds'], $_POST['exportType']);

        if ($exporter === null) {
            wp_die(__('Unsupported expoter', 'wp-pro-quiz'));
        }

        $response = $exporter->response();

        if ($response instanceof WP_Error) {
            wp_die($response);
        } else if ($response !== null) {
            echo $response;
        }

        exit;
    }

    private function handleImport()
    {
        if (!current_user_can('wpProQuiz_import')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (isset($_FILES) && isset($_FILES['import']) && $_FILES['import']['size'] > 0 && $_FILES['import']['error'] == 0) {
            $this->previewImport();
        } else {
            if(isset($this->_post, $this->_post['importSave'])) {
                $this->saveImport();
            } else {
                $view = new WpProQuiz_View_Import();
                $view->error = __('File cannot be processed', 'wp-pro-quiz');
                $view->show();
            }
        }

        return;
    }

    protected function previewImport()
    {
        $name = $_FILES['import']['name'];
        $type = $_FILES['import']['type'];
        $data = file_get_contents($_FILES['import']['tmp_name']);

        $helper = new WpProQuiz_Helper_QuizImport();

        if (!$helper->canHandle($name, $type)) {
            wp_die(__('Unsupport import'));
        }

        $importer = $helper->factory($name, $type, $data);

        if ($importer === null) {
            wp_die(__('Unsupport import'));
        }

        $import = $importer->getImport();

        $view = new WpProQuiz_View_Import();
        $view->error = false;
        $view->importType = $type;
        $view->name = $name;
        $view->importData = base64_encode($data);

        if (is_wp_error($import)) {
            $view->error = $import->get_error_message();
        } else {
            $view->import = $import;
        }

        $view->show();
    }

    protected function saveImport()
    {
        if(empty($_POST['name']) || empty($_POST['importType']) || empty($_POST['importData'])) {
            WpProQuiz_View_View::admin_notices(__('Import failed', 'wp-pro-quiz'), 'error');

            return;
        }

        $name = $_POST['name'];
        $type = $_POST['importType'];
        $data = base64_decode($_POST['importData']);
        $ids = isset($this->_post['importItems']) ? $this->_post['importItems'] : false;

        $helper = new WpProQuiz_Helper_QuizImport();

        if (!$helper->canHandle($name, $type)) {
            wp_die(__('Unsupport import'));
        }

        $importer = $helper->factory($name, $type, $data);

        if ($importer === null) {
            wp_die(__('Unsupport import'));
        }

        $result = $importer->import($ids);

        $view = new WpProQuiz_View_Import();

        if (is_wp_error($result)) {
            $view->error = false;
        } else {
            $view->finish = true;
        }

        $view->show();
    }
}
