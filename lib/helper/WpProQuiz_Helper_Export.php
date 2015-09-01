<?php

class WpProQuiz_Helper_Export
{

    const WPPROQUIZ_EXPORT_VERSION = 4;

    public function export($ids)
    {
        $export = array();

        $export['version'] = WPPROQUIZ_VERSION;
        $export['exportVersion'] = WpProQuiz_Helper_Export::WPPROQUIZ_EXPORT_VERSION;
        $export['date'] = time();

        $v = str_pad(WPPROQUIZ_VERSION, 5, '0', STR_PAD_LEFT);
        $v .= str_pad(WpProQuiz_Helper_Export::WPPROQUIZ_EXPORT_VERSION, 5, '0', STR_PAD_LEFT);
        $code = 'WPQ' . $v;

        $export['master'] = $this->getQuizMaster($ids);

        foreach ($export['master'] as $master) {
            $export['question'][$master->getId()] = $this->getQuestion($master->getId());
            $export['forms'][$master->getId()] = $this->getForms($master->getId());
        }

        return $code . base64_encode(serialize($export));
    }

    /**
     * @param $ids
     * @return WpProQuiz_Model_Quiz[]
     */
    private function getQuizMaster($ids)
    {
        $m = new WpProQuiz_Model_QuizMapper();

        $r = array();

        foreach ($ids as $id) {
            $master = $m->fetch($id);

            if ($master->getId() > 0) {
                $r[] = $master;
            }
        }

        return $r;
    }

    public function getQuestion($quizId)
    {
        $m = new WpProQuiz_Model_QuestionMapper();

        return $m->fetchAll($quizId);
    }

    private function getForms($quizId)
    {
        $formMapper = new WpProQuiz_Model_FormMapper();

        return $formMapper->fetch($quizId);
    }
}