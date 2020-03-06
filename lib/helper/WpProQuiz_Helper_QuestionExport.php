<?php

class WpProQuiz_Helper_QuestionExport
{

    /**
     * @return array
     */
    public function getSupportedExportFormats()
    {
        $formats = [
            'json' => 'JSON'
        ];

        return apply_filters('wpProQuiz_filter_questionExport_supportedExportFormats', $formats);
    }

    public function getExternalFactories()
    {
        $factories = apply_filters('wpProQuiz_filter_questionExport_factory', []);

        return is_array($factories) ? $factories : [];
    }

    /**
     * @param int[] $ids
     * @param string $type
     *
     * @return WpProQuiz_Helper_QuestionExporterInterface|null
     */
    public function factory($ids, $type)
    {
        $exporter = null;

        switch ($type) {
            case 'json':
                return $exporter = $this->createJsonExporter($ids);
                break;
            default:
                $exporter = $this->handleExternalFactories($ids, $type);
                break;
        }

        return $exporter;
    }

    /**
     * @param $ids
     *
     * @return WpProQuiz_Helper_QuestionExporterInterface
     */
    protected function createJsonExporter($ids)
    {
        return new WpProQuiz_Helper_JsonQuestionExporter($ids);
    }

    /**
     * @param $ids
     * @param $type
     * @return WpProQuiz_Helper_QuestionExporterInterface|null
     */
    protected function handleExternalFactories(array $ids, $type)
    {
        $exporter = null;
        $factories = $this->getExternalFactories();

        if (isset($factories[$type])) {
            $factory = $factories[$type];

            if (is_callable($factory)) {
                $exporter = call_user_func($factory, $ids, $type);
            }
        }

        return $exporter instanceof WpProQuiz_Helper_QuestionExporterInterface ? $exporter : null;
    }
}
