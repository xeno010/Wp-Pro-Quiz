<?php

class WpProQuiz_Helper_WpqQuizExporter implements WpProQuiz_Helper_QuizExporterInterface
{
    const WPPROQUIZ_EXPORT_VERSION = 4;

    /**
     * @var int[]
     */
    private $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function response()
    {
        $export = $this->getArrayHeader();
        $export['master'] = $this->getQuizMaster();

        foreach ($export['master'] as $master) {
            $export['question'][$master->getId()] = $this->getQuestion($master->getId());
            $export['forms'][$master->getId()] = $this->getForms($master->getId());
        }

        $this->printHeader($this->getFilename());

        return $this->buildReturnValue($export);
    }

    protected function buildReturnValue($export)
    {
        $code = $this->getFileSuffix();

        return $code . base64_encode(serialize($export));
    }

    protected function getFilename()
    {
        return 'WpProQuiz_export_' . time() . '.wpq';
    }

    protected function printHeader($filename)
    {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
    }

    protected function getArrayHeader()
    {
        $export = [];

        $export['version'] = WPPROQUIZ_VERSION;
        $export['exportVersion'] = static::WPPROQUIZ_EXPORT_VERSION;
        $export['date'] = time();

        return $export;
    }

    protected function getFileSuffix()
    {
        $v = str_pad(WPPROQUIZ_VERSION, 5, '0', STR_PAD_LEFT);
        $v .= str_pad(static::WPPROQUIZ_EXPORT_VERSION, 5, '0', STR_PAD_LEFT);

        return 'WPQ' . $v;
    }

    /**
     * @return WpProQuiz_Model_Quiz[]
     */
    protected function getQuizMaster()
    {
        $m = new WpProQuiz_Model_QuizMapper();

        $r = [];

        foreach ($this->ids as $id) {
            $master = $m->fetch($id);

            if ($master->getId() > 0) {
                $r[] = $master;
            }
        }

        return $r;
    }

    /**
     * @param $quizId
     * @return WpProQuiz_Model_Question[]
     */
    protected function getQuestion($quizId)
    {
        $m = new WpProQuiz_Model_QuestionMapper();

        return $m->fetchAll($quizId);
    }

    /**
     * @param $quizId
     * @return WpProQuiz_Model_Form[]
     */
    protected function getForms($quizId)
    {
        $formMapper = new WpProQuiz_Model_FormMapper();

        return $formMapper->fetch($quizId);
    }
}
