<?php

class WpProQuiz_Helper_GutenbergBlock
{
    public function __construct()
    {
        $this->addHooks();
    }

    protected function addHooks()
    {
        if (function_exists('register_block_type')) {
            add_action('init', [$this, 'initHook']);
        }
    }

    public function initHook() {
        $this->registerScripts();
        $this->registerBlock();
    }

    protected function registerScripts()
    {
        $data = array(
            'src' => plugins_url('css/wpProQuiz_front' . (WPPROQUIZ_DEV ? '' : '.min') . '.css', WPPROQUIZ_FILE),
            'deps' => array(),
            'ver' => WPPROQUIZ_VERSION,
        );

        wp_register_style('wpProQuiz_block-style', $data['src'], $data['deps'], $data['ver']);

        wp_register_script(
            'wpProQuiz-block-js',
            plugins_url('js/wpProQuiz_block.js', WPPROQUIZ_FILE),
            ['jquery', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor'],
            WPPROQUIZ_VERSION, true
        );
    }

    protected function registerBlock()
    {
        $mapper = new WpProQuiz_Model_QuizMapper();
        $results = [];

        foreach ($mapper->fetchAll() as $quiz) {
            $results[] = [
                'id' => $quiz->getId(),
                'title' => $quiz->getName(),
            ];
        }

        register_block_type('wp-pro-quiz/quiz', [
            'editor_script' => 'wpProQuiz-block-js',
            'editor_style' => 'wpProQuiz_block-style',
            'render_callback'   => [$this, 'renderRequest'],
            'attributes'	    => array(
                'idner' => $results,
                'metaFieldValue' => array(
                    'type'  => 'integer',
                ),
                'shortcode' => array(
                    'type'  => 'string',
                ),
                'className' => array(
                    'type'  => 'string',
                ),
            ),
        ]);
    }

    public function renderRequest($attributes)
    {
        $html = '<p style="text-align:center;">' . __('Please select quiz') . '</p>';

        if(isset($attributes['shortcode']) && $attributes['shortcode'] != '') {
            $html = do_shortcode( $attributes['shortcode'] );
        }

        return $html;
    }

    public static function init()
    {
        return new self();
    }
}
