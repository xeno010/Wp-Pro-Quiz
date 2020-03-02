<?php

class WpProQuiz_View_GlobalHelperTabs
{


    public function getHelperSidebar()
    {
        ob_start();

        $this->showHelperSidebar();

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    public function getHelperTab()
    {
        ob_start();

        $this->showHelperTabContent();

        $content = ob_get_contents();

        ob_end_clean();

        return array(
            'id' => 'wp_pro_quiz_help_tab_1',
            'title' => __('Wp-Pro-Quiz', 'wp-pro-quiz'),
            'content' => $content,
        );
    }

    private function showHelperTabContent()
    {
        ?>

        <h2>Wp-Pro-Quiz</h2>

        <h4>Wp-Pro-Quiz on Github</h4>

        <a class="button" target="_blank" href="https://github.com/xeno010/Wp-Pro-Quiz"><?php _e('Wp-Pro-Quiz on Github', 'wp-pro-quiz'); ?></a>

        <h4><?php _e('Donate', 'wp-pro-quiz'); ?></h4>

        <a class="button" style="background-color: #ffb735;font-weight: bold;" target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KCZPNURT6RYXY"><?php _e('PayPal Donate', 'wp-pro-quiz'); ?></a>

        <?php
    }

    private function showHelperSidebar()
    {
        ?>

        <p>
            <strong><?php _e('For more information:'); ?></strong>
        </p>
        <p>
            <a href="admin.php?page=wpProQuiz_wpq_support"><?php _e('Support', 'wp-pro-quiz'); ?></a>
        </p>
        <p>
            <a href="https://github.com/xeno010/Wp-Pro-Quiz" target="_blank">Github</a>
        </p>
        <p>
            <a href="https://github.com/xeno010/Wp-Pro-Quiz/wiki" target="_blank"><?php _e('Wiki',
                    'wp-pro-quiz'); ?></a>
        </p>
        <p>
            <a target="_blank"
               href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KCZPNURT6RYXY"><?php _e('Donate',
                    'wp-pro-quiz'); ?></a>
        </p>


        <?php
    }
}
