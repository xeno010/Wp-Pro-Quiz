<?php

class WpProQuiz_View_WpqSupport extends WpProQuiz_View_View
{

    public function show()
    {
        ?>

        <div class="wrap">
            <h2><?php _e('Support WP-Pro-Quiz', 'wp-pro-quiz'); ?></h2>

            <h3><?php _e('Donate', 'wp-pro-quiz'); ?></h3>

            <a class="button" style="background-color: #ffb735;font-weight: bold;" target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KCZPNURT6RYXY"><?php _e('PayPal Donate', 'wp-pro-quiz'); ?></a>

            <p>
                <?php _e('WP-Pro-Quiz is small but nice free quiz plugin for WordPress.', 'wp-pro-quiz'); ?> <br>
                <?php _e('I try to implement all wishes as fast as possible and help with problems.', 'wp-pro-quiz'); ?>
                <br>
                <?php _e('Your donations can help to ensure that the project continues to remain free.',
                    'wp-pro-quiz'); ?>
            </p>

            <h3>Wp-Pro-Quiz on Github</h3>

            <a class="button" target="_blank" href="https://github.com/xeno010/Wp-Pro-Quiz"><?php _e('Wp-Pro-Quiz on Github', 'wp-pro-quiz'); ?></a>


            <h3><?php _e('WP-Pro-Quiz special modification', 'wp-pro-quiz'); ?></h3>
            <strong><?php _e('You need special WP-Pro-Quiz modification for your website?',
                    'wp-pro-quiz'); ?></strong><br>
            <a class="button-primary" href="admin.php?page=wpProQuiz&module=info_adaptation"
               style="margin-top: 5px;"><?php _e('Learn more', 'wp-pro-quiz'); ?></a>

            <h3>Wp-Pro-Quiz Wiki</h3>

            <a class="button-primary" target="_blank" href="https://github.com/xeno010/Wp-Pro-Quiz/wiki">--> Wiki <--</a>

            <h3 style="margin-top: 40px;"><?php _e('Translate WP-Pro-Quiz', 'wp-pro-quiz'); ?></h3>

            <p>
                <?php _e('To translate wp-pro-quiz, please follow these steps:', 'wp-pro-quiz'); ?>
            </p>

            <ul style="list-style: decimal; padding: 0 22px;">
                <li><?php _e('Login to your account on wordpress.org (or create an account if you don’t have one yet).', 'wp-pro-quiz'); ?></li>
                <li><?php _e('Go to https://translate.wordpress.org.', 'wp-pro-quiz'); ?></li>
                <li><?php _e('Select your language and click ‘Contribute Translation’.', 'wp-pro-quiz'); ?></li>
                <li><?php _e('Go to the Plugins tab and search for ‘Wp-Pro-Quiz’.', 'wp-pro-quiz'); ?></li>
                <li><?php _e('Select the plugin and start translating!', 'wp-pro-quiz'); ?></li>
            </ul>

        </div>

        <?php
    }
}
