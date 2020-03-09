<?php

class WpProQuiz_Helper_QuizExport
{
    /**
     * @return array
     */
    public function getSupportedExportFormats()
    {
        $formats = [
            'wpq' => 'WPQ',
            'xml' => 'XML'
        ];

        return apply_filters('wpProQuiz_filter_quizExport_supportedExportFormats', $formats);
    }

    public function getExternalFactories()
    {
        $factories = apply_filters('wpProQuiz_filter_quizExport_factory', []);

        return is_array($factories) ? $factories : [];
    }

    /**
     * @param int[] $ids
     * @param string $type
     *
     * @return WpProQuiz_Helper_QuizExporterInterface|null
     */
    public function factory($ids, $type)
    {
        $exporter = null;

        switch ($type) {
            case 'wpq':
                $exporter = $this->createWqpExporter($ids);
                break;
            case 'xml':
                $exporter = $this->createXmlExporter($ids);
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
     * @return WpProQuiz_Helper_QuizExporterInterface
     */
    protected function createWqpExporter($ids)
    {
        return new WpProQuiz_Helper_WpqQuizExporter($ids);
    }

    /**
     * @param $ids
     *
     * @return WpProQuiz_Helper_QuizExporterInterface
     */
    protected function createXmlExporter($ids)
    {
        return new WpProQuiz_Helper_XmlQuizExporter($ids);
    }

    /**
     * @param $ids
     * @param $type
     * @return WpProQuiz_Helper_QuizExporterInterface|null
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

        return $exporter instanceof WpProQuiz_Helper_QuizExporterInterface ? $exporter : null;
    }
}
