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

        <iframe src="https://ghbtns.com/github-btn.html?user=xeno010&repo=Wp-Pro-Quiz&type=star&count=true"
                frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
        <iframe src="https://ghbtns.com/github-btn.html?user=xeno010&repo=Wp-Pro-Quiz&type=watch&count=true&v=2"
                frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
        <iframe src="https://ghbtns.com/github-btn.html?user=xeno010&repo=Wp-Pro-Quiz&type=fork&count=true"
                frameborder="0" scrolling="0" width="100px" height="20px"></iframe>

        <h4><?php _e('Donate', 'wp-pro-quiz'); ?></h4>

        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="KCZPNURT6RYXY">
            <input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0"
                   name="submit" alt="PayPal – The safer, easier way to pay online.">
            <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
        </form>

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