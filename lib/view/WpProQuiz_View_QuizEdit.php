<?php

/**
 * @property WpProQuiz_Model_Form[] forms
 * @property WpProQuiz_Model_Quiz quiz
 * @property array prerequisiteQuizList
 * @property WpProQuiz_Model_Template[] templates
 * @property array quizList
 * @property bool captchaIsInstalled
 * @property WpProQuiz_Model_Category[] categories
 * @property string header
 */
class WpProQuiz_View_QuizEdit extends WpProQuiz_View_View
{
    public function show()
    {
        ?>
        <style>
            .wpProQuiz_quizModus th, .wpProQuiz_quizModus td {
                border-right: 1px solid #A0A0A0;
                padding: 5px;
            }

            .wpProQuiz_demoBox {
                position: relative;
            }
        </style>
        <div class="wrap wpProQuiz_quizEdit">
            <h2 style="margin-bottom: 10px;"><?php echo $this->header; ?></h2>

            <form method="post"
                  action="admin.php?page=wpProQuiz&action=addEdit&quizId=<?php echo $this->quiz->getId(); ?>">

                <input type="hidden" name="ajax_quiz_id" value="<?php echo $this->quiz->getId(); ?>">

                <a style="float: left;" class="button-secondary"
                   href="admin.php?page=wpProQuiz"><?php _e('back to overview', 'wp-pro-quiz'); ?></a>

                <div style="float: right;">
                    <select name="templateLoadId">
                        <?php
                        foreach ($this->templates as $template) {
                            echo '<option value="', $template->getTemplateId(), '">', esc_html($template->getName()), '</option>';
                        }
                        ?>
                    </select>
                    <input type="submit" name="templateLoad" value="<?php _e('load template', 'wp-pro-quiz'); ?>"
                           class="button-primary">
                </div>
                <div style="clear: both;"></div>
                <div id="poststuff">
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Quiz title', 'wp-pro-quiz'); ?><?php _e('(required)',
                                'wp-pro-quiz'); ?></h3>

                        <div class="inside">
                            <input name="name" id="wpProQuiz_title" type="text" class="regular-text"
                                   value="<?php echo htmlspecialchars($this->quiz->getName(), ENT_QUOTES); ?>">
                        </div>
                    </div>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Category', 'wp-pro-quiz'); ?><?php _e('(optional)',
                                'wp-pro-quiz'); ?></h3>

                        <div class="inside">
                            <p class="description">
                                <?php _e('You can assign classify category for a quiz.', 'wp-pro-quiz'); ?>
                            </p>

                            <p class="description">
                                <?php _e('You can manage categories in global settings.', 'wp-pro-quiz'); ?>
                            </p>

                            <div>
                                <select name="category">
                                    <option value="-1">--- <?php _e('Create new category', 'wp-pro-quiz'); ?>----
                                    </option>
                                    <option
                                        value="0" <?php echo $this->quiz->getCategoryId() == 0 ? 'selected="selected"' : ''; ?>>
                                        --- <?php _e('No category', 'wp-pro-quiz'); ?> ---
                                    </option>
                                    <?php
                                    foreach ($this->categories as $cat) {
                                        echo '<option ' . ($this->quiz->getCategoryId() == $cat->getCategoryId() ? 'selected="selected"' : '') . ' value="' . $cat->getCategoryId() . '">' . $cat->getCategoryName() . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div style="display: none;" id="categoryAddBox">
                                <h4><?php _e('Create new category', 'wp-pro-quiz'); ?></h4>
                                <input type="text" name="categoryAdd" value="">
                                <input type="button" class="button-secondary" name="" id="categoryAddBtn"
                                       value="<?php _e('Create', 'wp-pro-quiz'); ?>">
                            </div>
                            <div id="categoryMsgBox"
                                 style="display:none; padding: 5px; border: 1px solid rgb(160, 160, 160); background-color: rgb(255, 255, 168); font-weight: bold; margin: 5px; ">
                                Kategorie gespeichert
                            </div>
                        </div>
                    </div>

                    <?php do_action('wpProQuiz_action_plugin_quizEdit', $this); ?>

                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Options', 'wp-pro-quiz'); ?></h3>

                        <div class="inside">
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Hide quiz title', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Hide title', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label for="title_hidden">
                                                <input type="checkbox" id="title_hidden" value="1"
                                                       name="titleHidden" <?php echo $this->quiz->isTitleHidden() ? 'checked="checked"' : '' ?> >
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('The title serves as quiz heading.', 'wp-pro-quiz'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Hide "Restart quiz" button', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Hide "Restart quiz" button', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label for="btn_restart_quiz_hidden">
                                                <input type="checkbox" id="btn_restart_quiz_hidden" value="1"
                                                       name="btnRestartQuizHidden" <?php echo $this->quiz->isBtnRestartQuizHidden() ? 'checked="checked"' : '' ?> >
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('Hide the "Restart quiz" button in the Frontend.',
                                                    'wp-pro-quiz'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Hide "View question" button', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Hide "View question" button', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label for="btn_view_question_hidden">
                                                <input type="checkbox" id="btn_view_question_hidden" value="1"
                                                       name="btnViewQuestionHidden" <?php echo $this->quiz->isBtnViewQuestionHidden() ? 'checked="checked"' : '' ?> >
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('Hide the "View question" button in the Frontend.',
                                                    'wp-pro-quiz'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Display question randomly', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Display question randomly', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label for="question_random">
                                                <input type="checkbox" id="question_random" value="1"
                                                       name="questionRandom" <?php echo $this->quiz->isQuestionRandom() ? 'checked="checked"' : '' ?> >
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Display answers randomly', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Display answers randomly', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label for="answer_random">
                                                <input type="checkbox" id="answer_random" value="1"
                                                       name="answerRandom" <?php echo $this->quiz->isAnswerRandom() ? 'checked="checked"' : '' ?> >
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Sort questions by category', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Sort questions by category', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="sortCategories" <?php $this->checked($this->quiz->isSortCategories()); ?> >
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('Also works in conjunction with the "display randomly question" option.',
                                                    'wp-pro-quiz'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Time limit', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Time limit', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label for="time_limit">
                                                <input type="number" min="0" class="small-text" id="time_limit"
                                                       value="<?php echo $this->quiz->getTimeLimit(); ?>"
                                                       name="timeLimit"> <?php _e('Seconds', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('0 = no limit', 'wp-pro-quiz'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Statistics', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Statistics', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label for="statistics_on">
                                                <input type="checkbox" id="statistics_on" value="1"
                                                       name="statisticsOn" <?php echo $this->quiz->isStatisticsOn() ? 'checked="checked"' : ''; ?>>
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('Statistics about right or wrong answers. Statistics will be saved by completed quiz, not after every question. The statistics is only visible over administration menu. (internal statistics)',
                                                    'wp-pro-quiz'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr id="statistics_ip_lock_tr" style="display: none;">
                                    <th scope="row">
                                        <?php _e('Statistics IP-lock', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Statistics IP-lock', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label for="statistics_ip_lock">
                                                <input type="number" min="0" class="small-text" id="statistics_ip_lock"
                                                       value="<?php echo ($this->quiz->getStatisticsIpLock() === null) ? 1440 : $this->quiz->getStatisticsIpLock(); ?>"
                                                       name="statisticsIpLock">
                                                <?php _e('in minutes (recommended 1440 minutes = 1 day)',
                                                    'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('Protect the statistics from spam. Result will only be saved every X minutes from same IP. (0 = deactivated)',
                                                    'wp-pro-quiz'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Execute quiz only once', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>

                                            <legend class="screen-reader-text">
                                                <span><?php _e('Execute quiz only once', 'wp-pro-quiz'); ?></span>
                                            </legend>

                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="quizRunOnce" <?php echo $this->quiz->isQuizRunOnce() ? 'checked="checked"' : '' ?>>
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you activate this option, the user can complete the quiz only once. Afterwards the quiz is blocked for this user.',
                                                    'wp-pro-quiz'); ?>
                                            </p>

                                            <div id="wpProQuiz_quiz_run_once_type"
                                                 style="margin-bottom: 5px; display: none;">
                                                <?php _e('This option applies to:', 'wp-pro-quiz');

                                                $quizRunOnceType = $this->quiz->getQuizRunOnceType();
                                                $quizRunOnceType = ($quizRunOnceType == 0) ? 1 : $quizRunOnceType;

                                                ?>
                                                <label>
                                                    <input name="quizRunOnceType" type="radio"
                                                           value="1" <?php echo ($quizRunOnceType == 1) ? 'checked="checked"' : ''; ?>>
                                                    <?php _e('all users', 'wp-pro-quiz'); ?>
                                                </label>
                                                <label>
                                                    <input name="quizRunOnceType" type="radio"
                                                           value="2" <?php echo ($quizRunOnceType == 2) ? 'checked="checked"' : ''; ?>>
                                                    <?php _e('registered useres only', 'wp-pro-quiz'); ?>
                                                </label>
                                                <label>
                                                    <input name="quizRunOnceType" type="radio"
                                                           value="3" <?php echo ($quizRunOnceType == 3) ? 'checked="checked"' : ''; ?>>
                                                    <?php _e('anonymous users only', 'wp-pro-quiz'); ?>
                                                </label>

                                                <div id="wpProQuiz_quiz_run_once_cookie" style="margin-top: 10px;">
                                                    <label>
                                                        <input type="checkbox" value="1"
                                                               name="quizRunOnceCookie" <?php echo $this->quiz->isQuizRunOnceCookie() ? 'checked="checked"' : '' ?>>
                                                        <?php _e('user identification by cookie', 'wp-pro-quiz'); ?>
                                                    </label>

                                                    <p class="description">
                                                        <?php _e('If you activate this option, a cookie is set additionally for unregistrated (anonymous) users. This ensures a longer assignment of the user than the simple assignment by the IP address.',
                                                            'wp-pro-quiz'); ?>
                                                    </p>
                                                </div>

                                                <div style="margin-top: 15px;">
                                                    <input class="button-secondary" type="button" name="resetQuizLock"
                                                           value="<?php _e('Reset the user identification',
                                                               'wp-pro-quiz'); ?>">
                                                    <span id="resetLockMsg"
                                                          style="display:none; background-color: rgb(255, 255, 173); border: 1px solid rgb(143, 143, 143); padding: 4px; margin-left: 5px; "><?php _e('User identification has been reset.'); ?></span>

                                                    <p class="description">
                                                        <?php _e('Resets user identification for all users.',
                                                            'wp-pro-quiz'); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Show only specific number of questions', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Show only specific number of questions',
                                                        'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="showMaxQuestion" <?php echo $this->quiz->isShowMaxQuestion() ? 'checked="checked"' : '' ?>>
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you enable this option, maximum number of displayed questions will be X from X questions. (The output of questions is random)',
                                                    'wp-pro-quiz'); ?>
                                            </p>

                                            <div id="wpProQuiz_showMaxBox" style="display: none;">
                                                <label>
                                                    <?php _e('How many questions should be displayed simultaneously:',
                                                        'wp-pro-quiz'); ?>
                                                    <input class="small-text" type="text" name="showMaxQuestionValue"
                                                           value="<?php echo $this->quiz->getShowMaxQuestionValue(); ?>">
                                                </label>
                                                <label>
                                                    <input type="checkbox" value="1"
                                                           name="showMaxQuestionPercent" <?php echo $this->quiz->isShowMaxQuestionPercent() ? 'checked="checked"' : '' ?>>
                                                    <?php _e('in percent', 'wp-pro-quiz'); ?>
                                                </label>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Prerequisites', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Prerequisites', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="prerequisite" <?php $this->checked($this->quiz->isPrerequisite()); ?>>
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you enable this option, you can choose quiz, which user have to finish before he can start this quiz.',
                                                    'wp-pro-quiz'); ?>
                                            </p>

                                            <p class="description">
                                                <?php _e('In all selected quizzes statistic function have to be active. If it is not it will be activated automatically.',
                                                    'wp-pro-quiz'); ?>
                                            </p>

                                            <div id="prerequisiteBox" style="display: none;">
                                                <table>
                                                    <tr>
                                                        <th style="width: 120px; padding: 0;"><?php _e('Quiz',
                                                                'wp-pro-quiz'); ?></th>
                                                        <th style="padding: 0; width: 50px;"></th>
                                                        <th style="padding: 0; width: 400px;"><?php _e('Prerequisites (This quiz have to be finished)',
                                                                'wp-pro-quiz'); ?></th>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 0;">
                                                            <select multiple="multiple" size="8" style="width: 200px;"
                                                                    name="quizList">
                                                                <?php foreach ($this->quizList as $list) {
                                                                    if (in_array($list['id'],
                                                                        $this->prerequisiteQuizList)) {
                                                                        continue;
                                                                    }

                                                                    echo '<option value="' . $list['id'] . '">' . $list['name'] . '</option>';
                                                                } ?>
                                                            </select>
                                                        </td>
                                                        <td style="padding: 0; text-align: center;">
                                                            <div>
                                                                <input type="button" id="btnPrerequisiteAdd"
                                                                       value="&gt;&gt;">
                                                            </div>
                                                            <div>
                                                                <input type="button" id="btnPrerequisiteDelete"
                                                                       value="&lt;&lt;">
                                                            </div>
                                                        </td>
                                                        <td style="padding: 0;">
                                                            <select multiple="multiple" size="8" style="width: 200px"
                                                                    name="prerequisiteList[]">
                                                                <?php foreach ($this->quizList as $list) {
                                                                    if (!in_array($list['id'],
                                                                        $this->prerequisiteQuizList)
                                                                    ) {
                                                                        continue;
                                                                    }

                                                                    echo '<option value="' . $list['id'] . '">' . $list['name'] . '</option>';
                                                                } ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Question overview', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Question overview', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="showReviewQuestion" <?php $this->checked($this->quiz->isShowReviewQuestion()); ?>>
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('Add at the top of the quiz a question overview, which allows easy navigation. Additional questions can be marked "to review".',
                                                    'wp-pro-quiz'); ?>
                                            </p>

                                            <p class="description">
                                                <?php _e('Additional quiz overview will be displayed, before quiz is finished.',
                                                    'wp-pro-quiz'); ?>
                                            </p>

                                            <div class="wpProQuiz_demoBox">
                                                <?php _e('Question overview', 'wp-pro-quiz'); ?>: <a
                                                    href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                                <div
                                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                    <img alt=""
                                                         src="<?php echo WPPROQUIZ_URL . '/img/questionOverview.png'; ?> ">
                                                </div>
                                            </div>
                                            <div class="wpProQuiz_demoBox">
                                                <?php _e('Quiz-summary', 'wp-pro-quiz'); ?>: <a
                                                    href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                                <div
                                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                    <img alt=""
                                                         src="<?php echo WPPROQUIZ_URL . '/img/quizSummary.png'; ?> ">
                                                </div>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr class="wpProQuiz_reviewQuestionOptions" style="display: none;">
                                    <th scope="row">
                                        <?php _e('Quiz-summary', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Quiz-summary', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="quizSummaryHide" <?php $this->checked($this->quiz->isQuizSummaryHide()); ?>>
                                                <?php _e('Deactivate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you enalbe this option, no quiz overview will be displayed, before finishing quiz.',
                                                    'wp-pro-quiz'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr class="wpProQuiz_reviewQuestionOptions" style="display: none;">
                                    <th scope="row">
                                        <?php _e('Skip question', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Skip question', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="skipQuestionDisabled" <?php $this->checked($this->quiz->isSkipQuestionDisabled()); ?>>
                                                <?php _e('Deactivate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you enable this option, user won\'t be able to skip question. (only in "Overview -> next" mode). User still will be able to navigate over "Question-Overview"',
                                                    'wp-pro-quiz'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <!--
							<tr>
								<th scope="row">
									<?php _e('Admin e-mail notification', 'wp-pro-quiz'); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php _e('Admin e-mail notification', 'wp-pro-quiz'); ?></span>
										</legend>
										<label>
											<input type="radio" name="emailNotification" value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_NONE; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
                                    WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_NONE); ?>>
											<?php _e('Deactivate', 'wp-pro-quiz'); ?>
										</label>
										<label>
											<input type="radio" name="emailNotification" value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
                                    WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER); ?>>
											<?php _e('for registered users only', 'wp-pro-quiz'); ?>
										</label>
										<label>
											<input type="radio" name="emailNotification" value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
                                    WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL); ?>>
											<?php _e('for all users', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('If you enable this option, you will be informed if a user completes this quiz.',
                                    'wp-pro-quiz'); ?>
										</p>
										<p class="description">
											<?php _e('E-Mail settings can be edited in global settings.',
                                    'wp-pro-quiz'); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							
							<tr>
								<th scope="row">
									<?php _e('User e-mail notification', 'wp-pro-quiz'); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php _e('User e-mail notification', 'wp-pro-quiz'); ?></span>
										</legend>
										<label>
											<input type="checkbox" name="userEmailNotification" value="1" <?php $this->checked($this->quiz->isUserEmailNotification()); ?>>
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('If you enable this option, an email is sent with his quiz result to the user. (only registered users)',
                                    'wp-pro-quiz'); ?>
										</p>
										<p class="description">
											<?php _e('E-Mail settings can be edited in global settings.',
                                    'wp-pro-quiz'); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							 -->
                                <tr>
                                    <th scope="row">
                                        <?php _e('Autostart', 'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Autostart', 'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" name="autostart"
                                                       value="1" <?php $this->checked($this->quiz->isAutostart()); ?>>
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you enable this option, the quiz will start automatically after the page is loaded.',
                                                    'wp-pro-quiz'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Only registered users are allowed to start the quiz',
                                            'wp-pro-quiz'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Only registered users are allowed to start the quiz',
                                                        'wp-pro-quiz'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" name="startOnlyRegisteredUser"
                                                       value="1" <?php $this->checked($this->quiz->isStartOnlyRegisteredUser()); ?>>
                                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you enable this option, only registered users allowed start the quiz.',
                                                    'wp-pro-quiz'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->questionOptions(); ?>
                    <?php $this->resultOptions(); ?>
                    <?php $this->quizMode(); ?>
                    <?php $this->leaderboardOptions(); ?>
                    <?php $this->form(); ?>
                    <?php $this->adminEmailOption(); ?>
                    <?php $this->userEmailOption(); ?>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Quiz description', 'wp-pro-quiz'); ?><?php _e('(required)',
                                'wp-pro-quiz'); ?></h3>

                        <div class="inside">
                            <p class="description">
                                <?php _e('This text will be displayed before start of the quiz.', 'wp-pro-quiz'); ?>
                            </p>
                            <?php
                            wp_editor($this->quiz->getText(), "text");
                            ?>
                        </div>
                    </div>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Results text', 'wp-pro-quiz'); ?><?php _e('(optional)',
                                'wp-pro-quiz'); ?></h3>

                        <div class="inside">
                            <p class="description">
                                <?php _e('This text will be displayed at the end of the quiz (in results). (this text is optional)',
                                    'wp-pro-quiz'); ?>
                            </p>

                            <div style="padding-top: 10px; padding-bottom: 10px;">
                                <label for="wpProQuiz_resultGradeEnabled">
                                    <?php _e('Activate graduation', 'wp-pro-quiz'); ?>
                                    <input type="checkbox" name="resultGradeEnabled" id="wpProQuiz_resultGradeEnabled"
                                           value="1" <?php echo $this->quiz->isResultGradeEnabled() ? 'checked="checked"' : ''; ?>>
                                </label>
                            </div>
                            <div style="display: none;" id="resultGrade">
                                <div>
                                    <strong><?php _e('Hint:', 'wp-pro-quiz'); ?></strong>
                                    <ul style="list-style-type: square; padding: 5px; margin-left: 20px; margin-top: 0;">
                                        <li><?php _e('Maximal 15 levels', 'wp-pro-quiz'); ?></li>
                                        <li>
                                            <?php printf(__('Percentages refer to the total score of the quiz. (Current total %d points in %d questions.',
                                                'wp-pro-quiz'),
                                                $this->quiz->fetchSumQuestionPoints(),
                                                $this->quiz->fetchCountQuestions()); ?>
                                        </li>
                                        <li><?php _e('Values can also be mixed up', 'wp-pro-quiz'); ?></li>
                                        <li><?php _e('10,15% or 10.15% allowed (max. two digits after the decimal point)',
                                                'wp-pro-quiz'); ?></li>
                                    </ul>

                                </div>
                                <div>
                                    <ul id="resultList">
                                        <?php
                                        $resultText = $this->quiz->getResultText();

                                        for ($i = 0; $i < 15; $i++) {

                                            if ($this->quiz->isResultGradeEnabled() && isset($resultText['text'][$i])) {
                                                ?>
                                                <li style="padding: 5px; border: 1px dotted;">
                                                    <div
                                                        style="margin-bottom: 5px;"><?php wp_editor($resultText['text'][$i],
                                                            'resultText_' . $i, array(
                                                                'textarea_rows' => 3,
                                                                'textarea_name' => 'resultTextGrade[text][]'
                                                            )); ?></div>
                                                    <div
                                                        style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
                                                        <?php _e('from:', 'wp-pro-quiz'); ?> <input type="text"
                                                                                                    name="resultTextGrade[prozent][]"
                                                                                                    class="small-text"
                                                                                                    value="<?php echo $resultText['prozent'][$i] ?>"> <?php _e('percent',
                                                            'wp-pro-quiz'); ?> <?php printf(__('(Will be displayed, when result-percent is >= <span class="resultProzent">%s</span>%%)',
                                                            'wp-pro-quiz'), $resultText['prozent'][$i]); ?>
                                                        <input type="button" style="float: right;"
                                                               class="button-primary deleteResult"
                                                               value="<?php _e('Delete graduation', 'wp-pro-quiz'); ?>">

                                                        <div style="clear: right;"></div>
                                                        <input type="hidden" value="1" name="resultTextGrade[activ][]">
                                                    </div>
                                                </li>

                                            <?php } else { ?>
                                                <li style="padding: 5px; border: 1px dotted; <?php echo $i ? 'display:none;' : '' ?>">
                                                    <div style="margin-bottom: 5px;"><?php wp_editor('',
                                                            'resultText_' . $i, array(
                                                                'textarea_rows' => 3,
                                                                'textarea_name' => 'resultTextGrade[text][]'
                                                            )); ?></div>
                                                    <div
                                                        style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
                                                        <?php _e('from:', 'wp-pro-quiz'); ?> <input type="text"
                                                                                                    name="resultTextGrade[prozent][]"
                                                                                                    class="small-text"
                                                                                                    value="0"> <?php _e('percent',
                                                            'wp-pro-quiz'); ?> <?php printf(__('(Will be displayed, when result-percent is >= <span class="resultProzent">%s</span>%%)',
                                                            'wp-pro-quiz'), '0'); ?>
                                                        <input type="button" style="float: right;"
                                                               class="button-primary deleteResult"
                                                               value="<?php _e('Delete graduation', 'wp-pro-quiz'); ?>">

                                                        <div style="clear: right;"></div>
                                                        <input type="hidden" value="<?php echo $i ? '0' : '1' ?>"
                                                               name="resultTextGrade[activ][]">
                                                    </div>
                                                </li>
                                            <?php }
                                        } ?>
                                    </ul>
                                    <input type="button" class="button-primary addResult"
                                           value="<?php _e('Add graduation', 'wp-pro-quiz'); ?>">
                                </div>
                            </div>
                            <div id="resultNormal">
                                <?php

                                $resultText = is_array($resultText) ? '' : $resultText;
                                wp_editor($resultText, 'resultText', array('textarea_rows' => 10));
                                ?>
                            </div>

                            <h4><?php _e('Custom fields - Variables', 'wp-pro-quiz'); ?></h4>
                            <ul class="formVariables"></ul>

                        </div>
                    </div>
                    <div style="float: left;">
                        <input type="submit" name="submit" class="button-primary" id="wpProQuiz_save"
                               value="<?php _e('Save', 'wp-pro-quiz'); ?>">
                    </div>
                    <div style="float: right;">
                        <input type="text" placeholder="<?php _e('template name', 'wp-pro-quiz'); ?>"
                               class="regular-text" name="templateName" style="border: 1px solid rgb(255, 134, 134);">
                        <select name="templateSaveList">
                            <option value="0">=== <?php _e('Create new template', 'wp-pro-quiz'); ?> ===</option>
                            <?php
                            foreach ($this->templates as $template) {
                                echo '<option value="', $template->getTemplateId(), '">', esc_html($template->getName()), '</option>';
                            }
                            ?>
                        </select>

                        <input type="submit" name="template" class="button-primary" id="wpProQuiz_saveTemplate"
                               value="<?php _e('Save as template', 'wp-pro-quiz'); ?>">
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </form>
        </div>
        <?php
    }

    private function resultOptions()
    {
        ?>
        <div class="postbox">
            <h3 class="hndle"><?php _e('Result-Options', 'wp-pro-quiz'); ?></h3>

            <div class="inside">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e('Show average points', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Show average points', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="showAverageResult" <?php $this->checked($this->quiz->isShowAverageResult()); ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('Statistics-function must be enabled.', 'wp-pro-quiz'); ?>
                                </p>

                                <div class="wpProQuiz_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/averagePoints.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Show category score', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Show category score', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" name="showCategoryScore"
                                           value="1" <?php $this->checked($this->quiz->isShowCategoryScore()); ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, the results of each category is displayed on the results page.',
                                        'wp-pro-quiz'); ?>
                                </p>
                            </fieldset>

                            <div class="wpProQuiz_demoBox">
                                <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                <div
                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                    <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/catOverview.png'; ?> ">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide correct questions - display', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide correct questions - display', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" name="hideResultCorrectQuestion"
                                           value="1" <?php $this->checked($this->quiz->isHideResultCorrectQuestion()); ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you select this option, no longer the number of correctly answered questions are displayed on the results page.',
                                        'wp-pro-quiz'); ?>
                                </p>
                            </fieldset>

                            <div class="wpProQuiz_demoBox">
                                <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                <div
                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                    <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/hideCorrectQuestion.png'; ?> ">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide quiz time - display', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide quiz time - display', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" name="hideResultQuizTime"
                                           value="1" <?php $this->checked($this->quiz->isHideResultQuizTime()); ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, the time for finishing the quiz won\'t be displayed on the results page anymore.',
                                        'wp-pro-quiz'); ?>
                                </p>
                            </fieldset>

                            <div class="wpProQuiz_demoBox">
                                <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                <div
                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                    <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/hideQuizTime.png'; ?> ">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide score - display', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide score - display', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" name="hideResultPoints"
                                           value="1" <?php $this->checked($this->quiz->isHideResultPoints()); ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, final score won\'t be displayed on the results page anymore.',
                                        'wp-pro-quiz'); ?>
                                </p>
                            </fieldset>

                            <div class="wpProQuiz_demoBox">
                                <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                <div
                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                    <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/hideQuizPoints.png'; ?> ">
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
    }

    private function questionOptions()
    {
        ?>

        <div class="postbox">
            <h3 class="hndle"><?php _e('Question-Options', 'wp-pro-quiz'); ?></h3>

            <div class="inside">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e('Show points', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Show points', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label for="show_points">
                                    <input type="checkbox" id="show_points" value="1"
                                           name="showPoints" <?php echo $this->quiz->isShowPoints() ? 'checked="checked"' : '' ?> >
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('Shows in quiz, how many points are reachable for respective question.',
                                        'wp-pro-quiz'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Number answers', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Number answers', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="numberedAnswer" <?php echo $this->quiz->isNumberedAnswer() ? 'checked="checked"' : '' ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If this option is activated, all answers are numbered (only single and multiple choice)',
                                        'wp-pro-quiz'); ?>
                                </p>

                                <div class="wpProQuiz_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/numbering.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide correct- and incorrect message', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide correct- and incorrect message', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="hideAnswerMessageBox" <?php echo $this->quiz->isHideAnswerMessageBox() ? 'checked="checked"' : '' ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, no correct- or incorrect message will be displayed.',
                                        'wp-pro-quiz'); ?>
                                </p>

                                <div class="wpProQuiz_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt=""
                                             src="<?php echo WPPROQUIZ_URL . '/img/hideAnswerMessageBox.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Correct and incorrect answer mark', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Correct and incorrect answer mark', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="disabledAnswerMark" <?php echo $this->quiz->isDisabledAnswerMark() ? 'checked="checked"' : '' ?>>
                                    <?php _e('Deactivate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, answers won\'t be color highlighted as correct or incorrect. ',
                                        'wp-pro-quiz'); ?>
                                </p>

                                <div class="wpProQuiz_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/mark.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Force user to answer each question', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Force user to answer each question', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="forcingQuestionSolve" <?php $this->checked($this->quiz->isForcingQuestionSolve()); ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, the user is forced to answer each question.',
                                        'wp-pro-quiz'); ?> <br>
                                    <?php _e('If the option "Question overview" is activated, this notification will appear after end of the quiz, otherwise after each question.',
                                        'wp-pro-quiz'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide question position overview', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide question position overview', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="hideQuestionPositionOverview" <?php $this->checked($this->quiz->isHideQuestionPositionOverview()); ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, the question position overview is hidden.',
                                        'wp-pro-quiz'); ?>
                                </p>

                                <div class="wpProQuiz_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt=""
                                             src="<?php echo WPPROQUIZ_URL . '/img/hideQuestionPositionOverview.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide question numbering', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide question numbering', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="hideQuestionNumbering" <?php $this->checked($this->quiz->isHideQuestionNumbering()); ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, the question numbering is hidden.',
                                        'wp-pro-quiz'); ?>
                                </p>

                                <div class="wpProQuiz_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt=""
                                             src="<?php echo WPPROQUIZ_URL . '/img/hideQuestionNumbering.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Display category', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Display category', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="showCategory" <?php $this->checked($this->quiz->isShowCategory()); ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, category will be displayed in the question.',
                                        'wp-pro-quiz'); ?>
                                </p>

                                <div class="wpProQuiz_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/showCategory.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
    }

    private function leaderboardOptions()
    {
        ?>
        <div class="postbox">
            <h3 class="hndle"><?php _e('Leaderboard', 'wp-pro-quiz'); ?><?php _e('(optional)', 'wp-pro-quiz'); ?></h3>

            <div class="inside">
                <p>
                    <?php _e('The leaderboard allows users to enter results in public list and to share the result this way.',
                        'wp-pro-quiz'); ?>
                </p>

                <p>
                    <?php _e('The leaderboard works independent from internal statistics function.', 'wp-pro-quiz'); ?>
                </p>
                <table class="form-table">
                    <tbody id="toplistBox">
                    <tr>
                        <th scope="row">
                            <?php _e('Leaderboard', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="toplistActivated"
                                       value="1" <?php echo $this->quiz->isToplistActivated() ? 'checked="checked"' : ''; ?>>
                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Who can sign up to the list', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input name="toplistDataAddPermissions" type="radio"
                                       value="1" <?php echo $this->quiz->getToplistDataAddPermissions() == 1 ? 'checked="checked"' : ''; ?>>
                                <?php _e('all users', 'wp-pro-quiz'); ?>
                            </label>
                            <label>
                                <input name="toplistDataAddPermissions" type="radio"
                                       value="2" <?php echo $this->quiz->getToplistDataAddPermissions() == 2 ? 'checked="checked"' : ''; ?>>
                                <?php _e('registered useres only', 'wp-pro-quiz'); ?>
                            </label>
                            <label>
                                <input name="toplistDataAddPermissions" type="radio"
                                       value="3" <?php echo $this->quiz->getToplistDataAddPermissions() == 3 ? 'checked="checked"' : ''; ?>>
                                <?php _e('anonymous users only', 'wp-pro-quiz'); ?>
                            </label>

                            <p class="description">
                                <?php _e('Not registered users have to enter name and e-mail (e-mail won\'t be displayed)',
                                    'wp-pro-quiz'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('insert automatically', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input name="toplistDataAddAutomatic" type="checkbox"
                                       value="1" <?php $this->checked($this->quiz->isToplistDataAddAutomatic()); ?>>
                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                            </label>

                            <p class="description">
                                <?php _e('If you enable this option, logged in users will be automatically entered into leaderboard',
                                    'wp-pro-quiz'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('display captcha', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="toplistDataCaptcha"
                                       value="1" <?php echo $this->quiz->isToplistDataCaptcha() ? 'checked="checked"' : ''; ?> <?php echo $this->captchaIsInstalled ? '' : 'disabled="disabled"'; ?>>
                                <?php _e('Activate', 'wp-pro-quiz'); ?>
                            </label>

                            <p class="description">
                                <?php _e('If you enable this option, additional captcha will be displayed for users who are not registered.',
                                    'wp-pro-quiz'); ?>
                            </p>

                            <p class="description" style="color: red;">
                                <?php _e('This option requires additional plugin:', 'wp-pro-quiz'); ?>
                                <a href="http://wordpress.org/extend/plugins/really-simple-captcha/" target="_blank">Really
                                    Simple CAPTCHA</a>
                            </p>
                            <?php if ($this->captchaIsInstalled) { ?>
                                <p class="description" style="color: green;">
                                    <?php _e('Plugin has been detected.', 'wp-pro-quiz'); ?>
                                </p>
                            <?php } else { ?>
                                <p class="description" style="color: red;">
                                    <?php _e('Plugin is not installed.', 'wp-pro-quiz'); ?>
                                </p>
                            <?php } ?>

                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Sort list by', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input name="toplistDataSort" type="radio"
                                       value="1" <?php echo ($this->quiz->getToplistDataSort() == 1) ? 'checked="checked"' : ''; ?>>
                                <?php _e('best user', 'wp-pro-quiz'); ?>
                            </label>
                            <label>
                                <input name="toplistDataSort" type="radio"
                                       value="2" <?php echo ($this->quiz->getToplistDataSort() == 2) ? 'checked="checked"' : ''; ?>>
                                <?php _e('newest entry', 'wp-pro-quiz'); ?>
                            </label>
                            <label>
                                <input name="toplistDataSort" type="radio"
                                       value="3" <?php echo ($this->quiz->getToplistDataSort() == 3) ? 'checked="checked"' : ''; ?>>
                                <?php _e('oldest entry', 'wp-pro-quiz'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Users can apply multiple times', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <div>
                                <label>
                                    <input type="checkbox" name="toplistDataAddMultiple"
                                           value="1" <?php echo $this->quiz->isToplistDataAddMultiple() ? 'checked="checked"' : ''; ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>
                            </div>
                            <div id="toplistDataAddBlockBox" style="display: none;">
                                <label>
                                    <?php _e('User can apply after:', 'wp-pro-quiz'); ?>
                                    <input type="number" min="0" class="small-text" name="toplistDataAddBlock"
                                           value="<?php echo $this->quiz->getToplistDataAddBlock(); ?>">
                                    <?php _e('minute', 'wp-pro-quiz'); ?>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('How many entries should be displayed', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <div>
                                <label>
                                    <input type="number" min="0" class="small-text" name="toplistDataShowLimit"
                                           value="<?php echo $this->quiz->getToplistDataShowLimit(); ?>">
                                    <?php _e('Entries', 'wp-pro-quiz'); ?>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Automatically display leaderboard in quiz result', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <div style="margin-top: 6px;">
                                <?php _e('Where should leaderboard be displayed:', 'wp-pro-quiz'); ?>
                                <label style="margin-right: 5px; margin-left: 5px;">
                                    <input type="radio" name="toplistDataShowIn"
                                           value="0" <?php echo ($this->quiz->getToplistDataShowIn() == 0) ? 'checked="checked"' : ''; ?>>
                                    <?php _e('don\'t display', 'wp-pro-quiz'); ?>
                                </label>
                                <label>
                                    <input type="radio" name="toplistDataShowIn"
                                           value="1" <?php echo ($this->quiz->getToplistDataShowIn() == 1) ? 'checked="checked"' : ''; ?>>
                                    <?php _e('below the "result text"', 'wp-pro-quiz'); ?>
                                </label>
									<span class="wpProQuiz_demoBox" style="margin-right: 5px;">
										<a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> 
										<span
                                            style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
											<img alt=""
                                                 src="<?php echo WPPROQUIZ_URL . '/img/leaderboardInResultText.png'; ?> ">
										</span>
									</span>
                                <label>
                                    <input type="radio" name="toplistDataShowIn"
                                           value="2" <?php echo ($this->quiz->getToplistDataShowIn() == 2) ? 'checked="checked"' : ''; ?>>
                                    <?php _e('in a button', 'wp-pro-quiz'); ?>
                                </label>
									<span class="wpProQuiz_demoBox">
										<a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> 
										<span
                                            style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
											<img alt=""
                                                 src="<?php echo WPPROQUIZ_URL . '/img/leaderboardInButton.png'; ?> ">
										</span>
									</span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    private function quizMode()
    {
        ?>
        <div class="postbox">
            <h3 class="hndle"><?php _e('Quiz-Mode', 'wp-pro-quiz'); ?><?php _e('(required)', 'wp-pro-quiz'); ?></h3>

            <div class="inside">
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #A0A0A0;"
                       class="wpProQuiz_quizModus">
                    <thead>
                    <tr>
                        <th style="width: 25%;"><?php _e('Normal', 'wp-pro-quiz'); ?></th>
                        <th style="width: 25%;"><?php _e('Normal + Back-Button', 'wp-pro-quiz'); ?></th>
                        <th style="width: 25%;"><?php _e('Check -> continue', 'wp-pro-quiz'); ?></th>
                        <th style="width: 25%;"><?php _e('Questions below each other', 'wp-pro-quiz'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><label><input type="radio" name="quizModus"
                                          value="0" <?php $this->checked($this->quiz->getQuizModus(),
                                    WpProQuiz_Model_Quiz::QUIZ_MODUS_NORMAL); ?>> <?php _e('Activate',
                                    'wp-pro-quiz'); ?></label></td>
                        <td><label><input type="radio" name="quizModus"
                                          value="1" <?php $this->checked($this->quiz->getQuizModus(),
                                    WpProQuiz_Model_Quiz::QUIZ_MODUS_BACK_BUTTON); ?>> <?php _e('Activate',
                                    'wp-pro-quiz'); ?></label></td>
                        <td><label><input type="radio" name="quizModus"
                                          value="2" <?php $this->checked($this->quiz->getQuizModus(),
                                    WpProQuiz_Model_Quiz::QUIZ_MODUS_CHECK); ?>> <?php _e('Activate', 'wp-pro-quiz'); ?>
                            </label></td>
                        <td><label><input type="radio" name="quizModus"
                                          value="3" <?php $this->checked($this->quiz->getQuizModus(),
                                    WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE); ?>> <?php _e('Activate',
                                    'wp-pro-quiz'); ?></label></td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('Displays all questions sequentially, "right" or "false" will be displayed at the end of the quiz.',
                                'wp-pro-quiz'); ?>
                        </td>
                        <td>
                            <?php _e('Allows to use the back button in a question.', 'wp-pro-quiz'); ?>
                        </td>
                        <td>
                            <?php _e('Shows "right or wrong" after each question.', 'wp-pro-quiz'); ?>
                        </td>
                        <td>
                            <?php _e('If this option is activated, all answers are displayed below each other, i.e. all questions are on a single page.',
                                'wp-pro-quiz'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="wpProQuiz_demoBox">
                                <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                <div
                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                    <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/normal.png'; ?> ">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="wpProQuiz_demoBox">
                                <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                <div
                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                    <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/backButton.png'; ?> ">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="wpProQuiz_demoBox" style="position: relative;">
                                <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                <div
                                    style="z-index: 9999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                    <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/checkCcontinue.png'; ?> ">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="wpProQuiz_demoBox" style="position: relative;">
                                <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                <div
                                    style="z-index: 9999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                    <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/singlePage.png'; ?> ">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <?php _e('How many questions to be displayed on a page:', 'wp-pro-quiz'); ?><br>
                            <input type="number" name="questionsPerPage"
                                   value="<?php echo $this->quiz->getQuestionsPerPage(); ?>" min="0">
									<span class="description">
										<?php _e('(0 = All on one page)', 'wp-pro-quiz'); ?>
									</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    private function form()
    {
        $forms = $this->forms;
        $index = 0;

        if (!count($forms)) {
            $forms = array(new WpProQuiz_Model_Form(), new WpProQuiz_Model_Form());
        } else {
            array_unshift($forms, new WpProQuiz_Model_Form());
        }

        ?>
        <div class="postbox">
            <h3 class="hndle"><?php _e('Custom fields', 'wp-pro-quiz'); ?></h3>

            <div class="inside">

                <p class="description">
                    <?php _e('You can create custom fields, e.g. to request the name or the e-mail address of the users.',
                        'wp-pro-quiz'); ?>
                </p>

                <p class="description">
                    <?php _e('The statistic function have to be enabled.', 'wp-pro-quiz'); ?>
                </p>

                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e('Custom fields enable', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Custom fields enable', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" id="formActivated" value="1"
                                           name="formActivated" <?php $this->checked($this->quiz->isFormActivated()); ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, custom fields are enabled.', 'wp-pro-quiz'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Display position', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Display position', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <?php _e('Where should the fileds be displayed:', 'wp-pro-quiz'); ?>
                                <label>
                                    <input type="radio"
                                           value="<?php echo WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_START; ?>"
                                           name="formShowPosition" <?php $this->checked($this->quiz->getFormShowPosition(),
                                        WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_START); ?>>
                                    <?php _e('On the quiz startpage', 'wp-pro-quiz'); ?>

                                    <div style="display: inline-block;" class="wpProQuiz_demoBox">
                                        <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                        <div
                                            style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                            <img alt=""
                                                 src="<?php echo WPPROQUIZ_URL . '/img/customFieldsFront.png'; ?> ">
                                        </div>
                                    </div>

                                </label>
                                <label>
                                    <input type="radio"
                                           value="<?php echo WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_END; ?>"
                                           name="formShowPosition" <?php $this->checked($this->quiz->getFormShowPosition(),
                                        WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_END); ?> >
                                    <?php _e('At the end of the quiz (before the quiz result)', 'wp-pro-quiz'); ?>

                                    <div style="display: inline-block;" class="wpProQuiz_demoBox">
                                        <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                        <div
                                            style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                            <img alt=""
                                                 src="<?php echo WPPROQUIZ_URL . '/img/customFieldsEnd1.png'; ?> ">
                                        </div>
                                    </div>

                                    <div style="display: inline-block;" class="wpProQuiz_demoBox">
                                        <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                        <div
                                            style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                            <img alt=""
                                                 src="<?php echo WPPROQUIZ_URL . '/img/customFieldsEnd2.png'; ?> ">
                                        </div>
                                    </div>

                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div style="margin-top: 10px; padding: 10px; border: 1px solid #C2C2C2;">
                    <table style=" width: 100%; text-align: left; " id="form_table">
                        <thead>
                        <tr>
                            <th>#ID</th>
                            <th><?php _e('Field name', 'wp-pro-quiz'); ?></th>
                            <th><?php _e('Type', 'wp-pro-quiz'); ?></th>
                            <th><?php _e('Required?', 'wp-pro-quiz'); ?></th>
                            <th>
                                <?php _e('Show in statistic table?', 'wp-pro-quiz'); ?>
                                <div style="display: inline-block;" class="wpProQuiz_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt=""
                                             src="<?php echo WPPROQUIZ_URL . '/img/formStatisticOverview.png'; ?> ">
                                    </div>
                                </div>
                            </th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($forms as $form) {
                            $checkType = $this->selectedArray($form->getType(), array(
                                WpProQuiz_Model_Form::FORM_TYPE_TEXT,
                                WpProQuiz_Model_Form::FORM_TYPE_TEXTAREA,
                                WpProQuiz_Model_Form::FORM_TYPE_CHECKBOX,
                                WpProQuiz_Model_Form::FORM_TYPE_SELECT,
                                WpProQuiz_Model_Form::FORM_TYPE_RADIO,
                                WpProQuiz_Model_Form::FORM_TYPE_NUMBER,
                                WpProQuiz_Model_Form::FORM_TYPE_EMAIL,
                                WpProQuiz_Model_Form::FORM_TYPE_YES_NO,
                                WpProQuiz_Model_Form::FORM_TYPE_DATE
                            ));
                            ?>
                            <tr <?php echo $index++ == 0 ? 'style="display: none;"' : '' ?>>
                                <td>
                                    <?php echo $index - 2; ?>
                                </td>
                                <td>
                                    <input type="text" name="form[][fieldname]"
                                           value="<?php echo esc_attr($form->getFieldname()); ?>"
                                           class="regular-text formFieldName"/>
                                </td>
                                <td style="position: relative;">
                                    <select name="form[][type]">
                                        <option
                                            value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_TEXT; ?>" <?php echo $checkType[0]; ?>><?php _e('Text',
                                                'wp-pro-quiz'); ?></option>
                                        <option
                                            value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_TEXTAREA; ?>" <?php echo $checkType[1]; ?>><?php _e('Textarea',
                                                'wp-pro-quiz'); ?></option>
                                        <option
                                            value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_CHECKBOX; ?>" <?php echo $checkType[2]; ?>><?php _e('Checkbox',
                                                'wp-pro-quiz'); ?></option>
                                        <option
                                            value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_SELECT; ?>" <?php echo $checkType[3]; ?>><?php _e('Drop-Down menu',
                                                'wp-pro-quiz'); ?></option>
                                        <option
                                            value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_RADIO; ?>" <?php echo $checkType[4]; ?>><?php _e('Radio',
                                                'wp-pro-quiz'); ?></option>
                                        <option
                                            value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_NUMBER; ?>" <?php echo $checkType[5]; ?>><?php _e('Number',
                                                'wp-pro-quiz'); ?></option>
                                        <option
                                            value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_EMAIL; ?>" <?php echo $checkType[6]; ?>><?php _e('Email',
                                                'wp-pro-quiz'); ?></option>
                                        <option
                                            value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_YES_NO; ?>" <?php echo $checkType[7]; ?>><?php _e('Yes/No',
                                                'wp-pro-quiz'); ?></option>
                                        <option
                                            value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_DATE; ?>" <?php echo $checkType[8]; ?>><?php _e('Date',
                                                'wp-pro-quiz'); ?></option>
                                    </select>

                                    <a href="#" class="editDropDown"><?php _e('Edit list', 'wp-pro-quiz'); ?></a>

                                    <div class="dropDownEditBox"
                                         style="position: absolute; border: 1px solid #AFAFAF; background: #EBEBEB; padding: 5px; bottom: 0;right: 0;box-shadow: 1px 1px 1px 1px #AFAFAF; display: none;">
                                        <h4><?php _e('One entry per line', 'wp-pro-quiz'); ?></h4>

                                        <div>
                                            <textarea rows="5" cols="50"
                                                      name="form[][data]"><?php echo $form->getData() === null ? '' : esc_textarea(implode("\n",
                                                    $form->getData())); ?></textarea>
                                        </div>

                                        <input type="button" value="<?php _e('OK', 'wp-pro-quiz'); ?>"
                                               class="button-primary">
                                    </div>
                                </td>
                                <td>
                                    <input type="checkbox" name="form[][required]"
                                           value="1" <?php $this->checked($form->isRequired()); ?>>
                                </td>
                                <td>
                                    <input type="checkbox" name="form[][show_in_statistic]"
                                           value="1" <?php $this->checked($form->isShowInStatistic()); ?>>
                                </td>
                                <td>
                                    <input type="button" name="form_delete"
                                           value="<?php _e('Delete', 'wp-pro-quiz'); ?>" class="button-secondary">
                                    <a class="form_move button-secondary" href="#" style="cursor:move;"><?php _e('Move',
                                            'wp-pro-quiz'); ?></a>

                                    <input type="hidden" name="form[][form_id]"
                                           value="<?php echo $form->getFormId(); ?>">
                                    <input type="hidden" name="form[][form_delete]" value="0">
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 10px;">
                        <input type="button" name="form_add" id="form_add"
                               value="<?php _e('Add field', 'wp-pro-quiz'); ?>" class="button-secondary">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function adminEmailOption()
    {
        /** @var WpProQuiz_Model_Email * */
        $email = $this->quiz->getAdminEmail();
        $email = $email === null ? WpProQuiz_Model_Email::getDefault(true) : $email;
        ?>
        <div class="postbox" id="adminEmailSettings">
            <h3 class="hndle"><?php _e('Admin e-mail settings', 'wp-pro-quiz'); ?></h3>

            <div class="inside">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e('Admin e-mail notification', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Admin e-mail notification', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="radio" name="emailNotification"
                                           value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_NONE; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
                                        WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_NONE); ?>>
                                    <?php _e('Deactivate', 'wp-pro-quiz'); ?>
                                </label>
                                <label>
                                    <input type="radio" name="emailNotification"
                                           value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
                                        WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER); ?>>
                                    <?php _e('for registered users only', 'wp-pro-quiz'); ?>
                                </label>
                                <label>
                                    <input type="radio" name="emailNotification"
                                           value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
                                        WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL); ?>>
                                    <?php _e('for all users', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, you will be informed if a user completes this quiz.',
                                        'wp-pro-quiz'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('To:', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="text" name="adminEmail[to]" value="<?php echo $email->getTo(); ?>"
                                       class="regular-text">
                            </label>

                            <p class="description">
                                <?php _e('Separate multiple email addresses with a comma, e.g. wp@test.com, test@test.com',
                                    'wp-pro-quiz'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('From:', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="text" name="adminEmail[from]" value="<?php echo $email->getFrom(); ?>"
                                       class="regular-text">
                            </label>
                            <!-- 								<p class="description"> -->
                            <?php //_e('Server-Adresse empfohlen, z.B. info@YOUR-PAGE.com', 'wp-pro-quiz');
                            ?>
                            <!-- 								</p> -->
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Subject:', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="text" name="adminEmail[subject]"
                                       value="<?php echo $email->getSubject(); ?>" class="regular-text">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('HTML', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="adminEmail[html]"
                                       value="1" <?php $this->checked($email->isHtml()); ?>> <?php _e('Activate',
                                    'wp-pro-quiz'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Message body:', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <?php
                            wp_editor($email->getMessage(), 'adminEmailEditor',
                                array('textarea_rows' => 20, 'textarea_name' => 'adminEmail[message]'));
                            ?>

                            <div style="padding-top: 10px;">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="padding: 0;">
                                            <?php _e('Allowed variables', 'wp-pro-quiz'); ?>
                                        </th>
                                        <th style="padding: 0;">
                                            <?php _e('Custom fields - Variables', 'wp-pro-quiz'); ?>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="vertical-align: top;">
                                            <ul>
                                                <li><span>$userId</span> - <?php _e('User-ID', 'wp-pro-quiz'); ?></li>
                                                <li><span>$username</span> - <?php _e('Username', 'wp-pro-quiz'); ?>
                                                </li>
                                                <li><span>$quizname</span> - <?php _e('Quiz-Name', 'wp-pro-quiz'); ?>
                                                </li>
                                                <li><span>$result</span> - <?php _e('Result in precent',
                                                        'wp-pro-quiz'); ?></li>
                                                <li><span>$points</span> - <?php _e('Reached points', 'wp-pro-quiz'); ?>
                                                </li>
                                                <li><span>$ip</span> - <?php _e('IP-address of the user',
                                                        'wp-pro-quiz'); ?></li>
                                                <li><span>$categories</span> - <?php _e('Category-Overview',
                                                        'wp-pro-quiz'); ?></li>
                                            </ul>
                                        </td>
                                        <td style="vertical-align: top;">
                                            <ul class="formVariables"></ul>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <?php

    }

    private function userEmailOption()
    {
        /** @var WpProQuiz_Model_Email * */
        $email = $this->quiz->getUserEmail();
        $email = $email === null ? WpProQuiz_Model_Email::getDefault(false) : $email;
        $to = $email->getTo();
        ?>
        <div class="postbox" id="userEmailSettings">
            <h3 class="hndle"><?php _e('User e-mail settings', 'wp-pro-quiz'); ?></h3>

            <div class="inside">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e('User e-mail notification', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('User e-mail notification', 'wp-pro-quiz'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" name="userEmailNotification"
                                           value="1" <?php $this->checked($this->quiz->isUserEmailNotification()); ?>>
                                    <?php _e('Activate', 'wp-pro-quiz'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, an email is sent with his quiz result to the user.',
                                        'wp-pro-quiz'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('To:', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="userEmail[toUser]"
                                       value="1" <?php $this->checked($email->isToUser()); ?>>
                                <?php _e('User Email-Address (only registered users)', 'wp-pro-quiz'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="userEmail[toForm]"
                                       value="1" <?php $this->checked($email->isToForm()); ?>>
                                <?php _e('Custom fields', 'wp-pro-quiz'); ?> :
                                <select name="userEmail[to]" class="emailFormVariables"
                                        data-default="<?php echo empty($to) && $to != 0 ? -1 : $email->getTo(); ?>"></select>
                                <?php _e('(Type Email)', 'wp-pro-quiz'); ?>
                            </label>

                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('From:', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="text" name="userEmail[from]" value="<?php echo $email->getFrom(); ?>"
                                       class="regular-text">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Subject:', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="text" name="userEmail[subject]" value="<?php echo $email->getSubject(); ?>"
                                       class="regular-text">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('HTML', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="userEmail[html]"
                                       value="1" <?php $this->checked($email->isHtml()); ?>> <?php _e('Activate',
                                    'wp-pro-quiz'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Message body:', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                            <?php
                            wp_editor($email->getMessage(), 'userEmailEditor',
                                array('textarea_rows' => 20, 'textarea_name' => 'userEmail[message]'));
                            ?>

                            <div style="padding-top: 10px;">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="padding: 0;">
                                            <?php _e('Allowed variables', 'wp-pro-quiz'); ?>
                                        </th>
                                        <th style="padding: 0;">
                                            <?php _e('Custom fields - Variables', 'wp-pro-quiz'); ?>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="vertical-align: top;">
                                            <ul>
                                                <li><span>$userId</span> - <?php _e('User-ID', 'wp-pro-quiz'); ?></li>
                                                <li><span>$username</span> - <?php _e('Username', 'wp-pro-quiz'); ?>
                                                </li>
                                                <li><span>$quizname</span> - <?php _e('Quiz-Name', 'wp-pro-quiz'); ?>
                                                </li>
                                                <li><span>$result</span> - <?php _e('Result in precent',
                                                        'wp-pro-quiz'); ?></li>
                                                <li><span>$points</span> - <?php _e('Reached points', 'wp-pro-quiz'); ?>
                                                </li>
                                                <li><span>$ip</span> - <?php _e('IP-address of the user',
                                                        'wp-pro-quiz'); ?></li>
                                                <li><span>$categories</span> - <?php _e('Category-Overview',
                                                        'wp-pro-quiz'); ?></li>
                                            </ul>
                                        </td>
                                        <td style="vertical-align: top;">
                                            <ul class="formVariables"></ul>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}
