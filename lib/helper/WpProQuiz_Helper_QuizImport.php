<?php

class WpProQuiz_Helper_QuizImport
{

    protected static $booted = false;
    protected static $handlers = [];

    public function __construct()
    {
        $this->runHooks();
    }

    public function canHandle($name, $type)
    {
        return $this->getSupportedHandler($name, $type) !== null;
    }

    protected function getSupportedHandler($name, $type)
    {
        $ext = $this->getExtensionFromName($name);

        foreach (self::$handlers as $handler) {
            if (in_array($ext, $handler['ext']) || in_array($type, $handler['types'])) {
                return $handler;
            }
        }

        return null;
    }

    /**
     * @param string|string[] $exterions
     * @param string|string[] $types
     * @param callable $callback
     *
     * @return bool
     */
    public function registerHandler($exterions, $types, $callback)
    {
        if (!is_callable($callback)) {
            return false;
        }

        self::$handlers[] = [
            'ext' => (array)$exterions,
            'types' => (array)$types,
            'callback' => is_callable($callback) ? $callback : null,
        ];

        return true;
    }

    public function getSupportedFileExtensions()
    {
        return $this->mergeHandlersToUniqueArray('ext');
    }

    public function getSupportedTypes()
    {
        return $this->mergeHandlersToUniqueArray('types');
    }

    /**
     * @param string $name
     * @param string $type
     * @param string|resource $resource
     * @return WpProQuiz_Helper_QuizImporterInterface|null
     */
    public function factory($name, $type, $resource)
    {
        $impoter = null;

        if ($handler = $this->getSupportedHandler($name, $type)) {
            $impoter = call_user_func($handler['callback'], $resource, $name, $type);
        }

        return $impoter instanceof WpProQuiz_Helper_QuizImporterInterface ? $impoter : null;
    }

    protected function mergeHandlersToUniqueArray($key)
    {
        $ext = [];

        foreach (self::$handlers as $handler) {
            $ext = array_merge($ext, $handler[$key]);
        }

        return array_unique($ext);
    }

    protected function getExtensionFromName($filename)
    {
        $pos = strrpos($filename, '.');

        if ($pos !== false) {
            return strtolower(substr($filename, $pos+1));
        }

        return $filename;
    }

    protected function runHooks()
    {
        if (!self::$booted) {
            self::$booted = true;

            $this->registerBuildInHandlers();

            do_action('wpProQuiz_quizImport_boot', $this);
        }
    }

    public function createWpqImporter($resource)
    {
        return WpProQuiz_Helper_WpqQuizImporter::factory($resource);
    }

    public function createXmlImporter($resource)
    {
        return WpProQuiz_Helper_XmlQuizImporter::factory($resource);
    }

    protected function registerBuildInHandlers()
    {
        $this->registerHandler('wpq', [], [$this, 'createWpqImporter']);
        $this->registerHandler('xml', 'application/xml', [$this, 'createXmlImporter']);
    }
}
