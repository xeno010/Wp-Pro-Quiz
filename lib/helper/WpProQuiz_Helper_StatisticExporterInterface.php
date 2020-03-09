<?php

interface WpProQuiz_Helper_StatisticExporterInterface
{

    /**
     * @param WpProQuiz_Model_StatisticUser[] $users
     * @param WpProQuiz_Model_StatisticRefModel $ref
     * @param WpProQuiz_Model_Form[] $forms
     *
     * @return string|WP_Error
     */
    public function exportUser($users, $ref, $forms);

    /**
     * @param WpProQuiz_Model_StatisticOverview[] $overviews
     *
     * @return string|WP_Error
     */
    public function exportOverview($overviews);

    /**
     * @param WpProQuiz_Model_StatisticHistory[] $histories
     * @param WpProQuiz_Model_Form[] $forms
     * @return string|WP_Error
     */
    public function exportHistory($histories, $forms);
}
