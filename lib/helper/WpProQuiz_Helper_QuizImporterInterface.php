<?php

interface WpProQuiz_Helper_QuizImporterInterface
{
    /**
     * @return WP_Error|array
     */
    public function getImport();

    /**
     * @param bool $ids
     * @return mixed
     */
    public function import($ids = false);
}
