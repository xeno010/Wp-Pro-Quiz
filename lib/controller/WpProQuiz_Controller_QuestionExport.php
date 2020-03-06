<?php

class WpProQuiz_Controller_QuestionExport extends WpProQuiz_Controller_Controller
{
    public function route()
    {
        $this->handleExport();
    }

    protected function handleExport()
    {
        if (!current_user_can('wpProQuiz_export')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $exportIds = $this->prepareExportIds($_POST['exportIds']);

        if (empty($exportIds) || empty($_POST['exportType'])) {
            wp_die(__('Invalid arguments'));
        }

        $questionExport = new WpProQuiz_Helper_QuestionExport();
        $exporter = $questionExport->factory($exportIds, $_POST['exportType']);

        if ($exporter === null) {
            wp_die(__('Unsupported exporter'));
        }

        $response = $exporter->response();

        if($response instanceof WP_Error) {
            wp_die($response);
        } else if ($response !== null) {
            echo $response;
        }

        exit;
    }

    /**
     * @param array $ids
     * @return array
     */
    protected function prepareExportIds($ids)
    {
        return array_map('intval', array_filter($ids, 'is_numeric'));
    }
}
