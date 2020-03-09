<?php

class WpProQuiz_Helper_StatisticExport
{
    /**
     * @return array
     */
    public function getSupportedExportFormats()
    {
        $formats = [
            'json' => 'JSON'
        ];

        return apply_filters('wpProQuiz_filter_statisticExport_supportedExportFormats', $formats);
    }

    public function getExternalFactories()
    {
        $factories = apply_filters('wpProQuiz_filter_statisticExport_factory', []);

        return is_array($factories) ? $factories : [];
    }

    /**
     * @param string $type
     *
     * @return WpProQuiz_Helper_StatisticExporterInterface|null
     */
    public function factory($type)
    {
        $exporter = null;

        switch ($type) {
            case 'json':
                $exporter = $this->createJsonExporter();
                break;
            default:
                $exporter = $this->handleExternalFactories($type);
                break;
        }

        return $exporter;
    }

    /**
     *
     * @return WpProQuiz_Helper_StatisticExporterInterface
     */
    protected function createJsonExporter()
    {
        return new WpProQuiz_Helper_JsonStatisticExporter();
    }

    /**
     * @param $type
     * @return WpProQuiz_Helper_StatisticExporterInterface|null
     */
    protected function handleExternalFactories($type)
    {
        $exporter = null;
        $factories = $this->getExternalFactories();

        if (isset($factories[$type])) {
            $factory = $factories[$type];

            if (is_callable($factory)) {
                $exporter = call_user_func($factory, $type);
            }
        }

        return $exporter instanceof WpProQuiz_Helper_StatisticExporterInterface ? $exporter : null;
    }
}
