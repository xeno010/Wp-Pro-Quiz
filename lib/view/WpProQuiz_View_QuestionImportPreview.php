<?php

/**
 * @property array $questionNames
 * @property int $quizId
 * @property string $name
 * @property string $type
 * @property string $data
 */
class WpProQuiz_View_QuestionImportPreview extends WpProQuiz_View_View
{

    public function show()
    {
        ?>
        <style>
            .wpProQuiz_importList {
                list-style: none;
                margin: 0;
                padding: 0;
            }

            .wpProQuiz_importList li {
                float: left;
                padding: 5px;
                border: 1px solid #B3B3B3;
                margin-right: 5px;
                background-color: #DAECFF;
            }
        </style>
        <div class="wrap wpProQuiz_importOverall">
            <h2><?php _e('Import', 'wp-pro-quiz'); ?></h2>

            <p>
                <a class="button-secondary" href="<?php admin_url('admin.php?page=wpProQuiz&module=question&quiz_id='.$this->quizId); ?>"><?php _e('back to overview', 'wp-pro-quiz'); ?></a>
            </p>

            <form method="post" action="<?php echo admin_url('admin.php?page=wpProQuiz&module=questionImport&action=import&quizId='.$this->quizId); ?>">
                <table class="wp-list-table widefat">
                    <thead>
                    <tr>
                        <th scope="col"><?php _e('Questions', 'wp-pro-quiz'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>
                                <ul class="wpProQuiz_importList">
                                    <?php foreach ($this->questionNames as $name) { ?>
                                        <li><?php echo esc_html($name); ?></li>
                                    <?php } ?>
                                </ul>
                                <div style="clear: both;"></div>
                            </th>
                        </tr>
                    </tbody>
                </table>

                <input name="name" value="<?php echo $this->name; ?>" type="hidden">
                <input name="type" value="<?php echo $this->type; ?>" type="hidden">
                <input name="data" value="<?php echo $this->data; ?>" type="hidden">
                <input style="margin-top: 20px;" class="button-primary" name="importSave" value="<?php echo __('Start import', 'wp-pro-quiz'); ?>" type="submit">
            </form>
        </div>

        <?php
    }
}
