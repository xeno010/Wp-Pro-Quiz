<?php
class WpProQuiz_View_QuizEdit extends WpProQuiz_View_View {
	
	public function show() {
?>
<div class="wrap">
	<h2 style="margin-bottom: 10px;"><?php echo $this->header; ?></h2>
	<a class="button-secondary" href="admin.php?page=wpProQuiz"><?php _e('back to overview', 'wp-pro-quiz'); ?></a>
	<form method="post">
		<div id="poststuff">
			<div class="postbox">
				<h3 class="hndle"><?php _e('Quiz title', 'wp-pro-quiz'); ?> <?php _e('(required)', 'wp-pro-quiz'); ?></h3>
				<div class="inside">
					<input name="name" id="wpProQuiz_title" type="text" class="regular-text" value="<?php echo $this->quiz->getName(); ?>">
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><?php _e('Options', 'wp-pro-quiz'); ?></h3>
				<div class="wrap wpProQuiz_quizEdit">
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
											<input type="checkbox" id="title_hidden" value="1" name="titleHidden" <?php echo $this->quiz->isTitleHidden() ? 'checked="checked"' : '' ?> >
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
											<input type="checkbox" id="btn_restart_quiz_hidden" value="1" name="btnRestartQuizHidden" <?php echo $this->quiz->isBtnRestartQuizHidden() ? 'checked="checked"' : '' ?> >
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('Hide the "Restart quiz" button in the Frontend.', 'wp-pro-quiz'); ?>
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
											<input type="checkbox" id="btn_view_question_hidden" value="1" name="btnViewQuestionHidden" <?php echo $this->quiz->isBtnViewQuestionHidden() ? 'checked="checked"' : '' ?> >
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('Hide the "View question" button in the Frontend.', 'wp-pro-quiz'); ?>
										</p>
									</fieldset>
								</td>
							</tr>
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
											<input type="checkbox" id="show_points" value="1" name="showPoints" <?php echo $this->quiz->isShowPoints() ? 'checked="checked"' : '' ?> >
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('Shows in quiz, how many points are reachable for respective question.', 'wp-pro-quiz'); ?>
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
											<input type="checkbox" id="question_random" value="1" name="questionRandom" <?php echo $this->quiz->isQuestionRandom() ? 'checked="checked"' : '' ?> >
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
											<input type="checkbox" id="answer_random" value="1" name="answerRandom" <?php echo $this->quiz->isAnswerRandom() ? 'checked="checked"' : '' ?> >
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
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
											<input type="number" min="0" class="small-text" id="time_limit" value="<?php echo $this->quiz->getTimeLimit(); ?>" name="timeLimit"> <?php _e('Seconds', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('0 = no limit', 'wp-pro-quiz'); ?>
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
											<input type="checkbox" value="1" name="numberedAnswer" <?php echo $this->quiz->isNumberedAnswer() ? 'checked="checked"' : '' ?>>
											<?php _e('Activate', 'wp-pro-quiz'); ?> 
										</label>
										<p class="description">
											<?php _e('If this option is activated, all answers are numbered (only single and multiple choice)', 'wp-pro-quiz'); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> 
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/numbering.png'; ?> ">
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php _e('Questions below each other', 'wp-pro-quiz'); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php _e('Questions below each other', 'wp-pro-quiz'); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="questionOnSinglePage" <?php echo $this->quiz->isQuestionOnSinglePage() ? 'checked="checked"' : ''; ?>>
											<?php _e('Activate', 'wp-pro-quiz'); ?> 
										</label>
										<p class="description">
											<?php _e('If this option is activated, all answers are displayed below each other, i.e. all questions are on a single page. Otherwise all questions are displayed one after the other.', 'wp-pro-quiz'); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> 
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/singlePage.png'; ?> ">
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php _e('Check -> continue', 'wp-pro-quiz'); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php _e('Check -> continue', 'wp-pro-quiz'); ?></span>
										</legend>
										<label for="check_next">
											<input type="checkbox" id="check_next" value="1" name="checkAnswer" <?php echo $this->quiz->isCheckAnswer() ? 'checked="checked"' : ''; ?>>
											<?php _e('Activate', 'wp-pro-quiz'); ?> 
										</label>
										<p class="description">
											<?php _e('Shows "right or wrong" after each question. Otherwise the solutions will be displayed at the end. (Option will be ignored if "questions below each other" was used)', 'wp-pro-quiz'); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> 
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/checkCcontinue.png'; ?> ">
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php _e('Back button', 'wp-pro-quiz'); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php _e('Back button', 'wp-pro-quiz'); ?></span>
										</legend>
										<label for="back_button">
											<input type="checkbox" id="back_button" value="1" name="backButton" <?php echo $this->quiz->isBackButton() ? 'checked="checked"' : ''; ?>>
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('Allows to use the back button in a question. (Option will be ignored if "Check -> Continue" or "questions below each other" was used)', 'wp-pro-quiz'); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> 
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/backButton.png'; ?> ">
											</div>
										</div>
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
											<input type="checkbox" id="statistics_on" value="1" name="statisticsOn" <?php echo $this->quiz->isStatisticsOn() ? 'checked="checked"' : ''; ?>>
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('Statistics about right or wrong answers. Statistics will be saved by completed quiz, not after every question. The statistics is only visible over administration menu. (internal statistics)', 'wp-pro-quiz'); ?>
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
											<input type="number" min="0" class="small-text" id="statistics_ip_lock" value="<?php echo ($this->quiz->getStatisticsIpLock() === null) ? 1440 : $this->quiz->getStatisticsIpLock(); ?>" name="statisticsIpLock">
											<?php _e('in minutes (recommended 1440 minutes = 1 day)', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('Protect the statistics from spam. Result will only be saved every X minutes from same IP. (0 = deactivated)', 'wp-pro-quiz'); ?>
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
											<input type="checkbox" value="1" name="quizRunOnce" <?php echo $this->quiz->isQuizRunOnce() ? 'checked="checked"' : '' ?>>
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('If you activate this option, the user can complete the quiz only once. Afterwards the quiz is blocked for this user.', 'wp-pro-quiz'); ?>
										</p>
										
										<div id="wpProQuiz_quiz_run_once_type" style="margin-bottom: 5px; display: none;">
											<?php _e('This option applies to:', 'wp-pro-quiz'); 
											
											$quizRunOnceType = $this->quiz->getQuizRunOnceType();
											$quizRunOnceType = ($quizRunOnceType == 0) ? 1: $quizRunOnceType; 
											
											?>		
											<label>
												<input name="quizRunOnceType" type="radio" value="1" <?php echo ($quizRunOnceType == 1) ? 'checked="checked"' : ''; ?>>
												<?php _e('all users', 'wp-pro-quiz'); ?>
											</label>
											<label>
												<input name="quizRunOnceType" type="radio" value="2" <?php echo ($quizRunOnceType == 2) ? 'checked="checked"' : ''; ?>>
												<?php _e('registered useres only', 'wp-pro-quiz'); ?>
											</label>
											<label>
												<input name="quizRunOnceType" type="radio" value="3" <?php echo ($quizRunOnceType == 3) ? 'checked="checked"' : ''; ?>>
												<?php _e('anonymous users only', 'wp-pro-quiz'); ?>
											</label>
											
											<div id="wpProQuiz_quiz_run_once_cookie" style="margin-top: 10px;">
												<label>
													<input type="checkbox" value="1" name="quizRunOnceCookie" <?php echo $this->quiz->isQuizRunOnceCookie() ? 'checked="checked"' : '' ?>>
													<?php _e('user identification by cookie', 'wp-pro-quiz'); ?>
												</label>
												<p class="description">
													<?php _e('If you activate this option, a cookie is set additionally for unregistrated (anonymous) users. This ensures a longer assignment of the user than the simple assignment by the IP address.', 'wp-pro-quiz'); ?>
												</p>
											</div>
											
											<div style="margin-top: 15px;">
												<input class="button-secondary" type="button" name="resetQuizLock" value="<?php _e('Reset the user identification', 'wp-pro-quiz'); ?>">
												<span id="resetLockMsg" style="display:none; background-color: rgb(255, 255, 173); border: 1px solid rgb(143, 143, 143); padding: 4px; margin-left: 5px; "><?php _e('User identification has been reset.'); ?></span>
												<p class="description">
													<?php _e('Resets user identification for all users.', 'wp-pro-quiz'); ?>
												</p>
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
											<input type="checkbox" value="1" name="hideAnswerMessageBox" <?php echo $this->quiz->isHideAnswerMessageBox() ? 'checked="checked"' : '' ?>>
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('If you enable this option, no correct- or incorrect message will be displayed.', 'wp-pro-quiz'); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> 
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/hideAnswerMessageBox.png'; ?> ">
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
											<input type="checkbox" value="1" name="disabledAnswerMark" <?php echo $this->quiz->isDisabledAnswerMark() ? 'checked="checked"' : '' ?>>
											<?php _e('Deactivate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('If you enable this option, answers won\'t be color highlighted as correct or incorrect. ', 'wp-pro-quiz'); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> 
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/mark.png'; ?> ">
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
											<span><?php _e('Show only specific number of questions', 'wp-pro-quiz'); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="showMaxQuestion" <?php echo $this->quiz->isShowMaxQuestion() ? 'checked="checked"' : '' ?>>
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('If you enable this option, maximum number of displayed questions will be X from X questions. (The output of questions is random)', 'wp-pro-quiz'); ?>
										</p>
										<p class="description">
											<?php _e('The statistics function is not available in this option.', 'wp-pro-quiz'); ?>
										</p>
										<p style="font-weight: bold;" class="description">
											<?php _e('This option doesn\'t work reliable in connection with Frontend-Cache-Plugins.', 'wp-pro-quiz'); ?>
										</p>
										<div id="wpProQuiz_showMaxBox" style="display: none;">
											<label>
												<?php _e('How many questions should be displayed simultaneously:', 'wp-pro-quiz'); ?> 
												<input class="small-text" type="text" name="showMaxQuestionValue" value="<?php echo $this->quiz->getShowMaxQuestionValue(); ?>">
											</label>
											<label>
												<input type="checkbox" value="1" name="showMaxQuestionPercent" <?php echo $this->quiz->isShowMaxQuestionPercent() ? 'checked="checked"' : '' ?>>
												<?php _e('in percent', 'wp-pro-quiz'); ?> 
											</label>
										</div>
									</fieldset>
								</td>
							</tr>
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
											<input type="checkbox" value="1" name="showAverageResult" <?php $this->checked($this->quiz->isShowAverageResult()); ?>>
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('Statistics-function must be enabled.', 'wp-pro-quiz'); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> 
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/averagePoints.png'; ?> ">
											</div>
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
											<input type="checkbox" value="1" name="prerequisite" <?php $this->checked($this->quiz->isPrerequisite()); ?>>
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('If you enable this option, you can choose quiz, which user have to finish before he can start this quiz.', 'wp-pro-quiz'); ?>
										</p>
										<p class="description">
											<?php _e('In all selected quizzes statistic function have to be active. If it is not it will be activated automatically.', 'wp-pro-quiz'); ?>
										</p>
										<div id="prerequisiteBox" style="display: none;">
											<table>
												<tr>
													<th style="width: 120px; padding: 0;"><?php _e('Quiz', 'wp-pro-quiz'); ?></th>
													<th style="padding: 0; width: 50px;"></th>
													<th style="padding: 0; width: 400px;"><?php _e('Prerequisites (This quiz have to be finished)', 'wp-pro-quiz'); ?></th>
												</tr>
												<tr>
													<td style="padding: 0;">
														<select multiple="multiple" size="8" style="width: 200px;" name="quizList">
															<?php foreach($this->quizList as $list) {
																if(in_array($list['id'], $this->prerequisiteQuizList))
																	continue;
																
																	echo '<option value="'.$list['id'].'">'.$list['name'].'</option>';
															} ?>
														</select>
													</td>
													<td style="padding: 0; text-align: center;">
														<div>
															<input type="button" id="btnPrerequisiteAdd" value="&gt;&gt;">
														</div>
														<div>
															<input type="button" id="btnPrerequisiteDelete" value="&lt;&lt;">
														</div>
													</td>
													<td style="padding: 0;">
														<select multiple="multiple" size="8" style="width: 200px" name="prerequisiteList[]">
															<?php foreach($this->quizList as $list) {
																if(!in_array($list['id'], $this->prerequisiteQuizList))
																	continue;
																
																	echo '<option value="'.$list['id'].'">'.$list['name'].'</option>';
															} ?>
														</select>
													</td>
												</tr>
											</table>
										</div>
									</fieldset>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><?php _e('Leaderboard', 'wp-pro-quiz'); ?> <?php _e('(optional)', 'wp-pro-quiz'); ?></h3>
				<div class="inside">
					<p>
						<?php _e('The leaderboard allows users to enter results in public list and to share the result this way.', 'wp-pro-quiz'); ?>
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
										<input type="checkbox" name="toplistActivated" value="1" <?php echo $this->quiz->isToplistActivated() ? 'checked="checked"' : ''; ?>> 
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
										<input name="toplistDataAddPermissions" type="radio" value="1" <?php echo $this->quiz->getToplistDataAddPermissions() == 1 ? 'checked="checked"' : ''; ?>>
										<?php _e('all users', 'wp-pro-quiz'); ?>
									</label>
									<label>
										<input name="toplistDataAddPermissions" type="radio" value="2" <?php echo $this->quiz->getToplistDataAddPermissions() == 2 ? 'checked="checked"' : ''; ?>>
										<?php _e('registered useres only', 'wp-pro-quiz'); ?>
									</label>
									<label>
										<input name="toplistDataAddPermissions" type="radio" value="3" <?php echo $this->quiz->getToplistDataAddPermissions() == 3 ? 'checked="checked"' : ''; ?>>
										<?php _e('anonymous users only', 'wp-pro-quiz'); ?>
									</label>
									<p class="description">
										<?php _e('Not registered users have to enter name and e-mail (e-mail won\'t be displayed)', 'wp-pro-quiz'); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php _e('display captcha', 'wp-pro-quiz'); ?>
								</th>
								<td>
									<label>
										<input type="checkbox" name="toplistDataCaptcha" value="1" <?php echo $this->quiz->isToplistDataCaptcha() ? 'checked="checked"' : ''; ?> <?php echo $this->captchaIsInstalled ? '' : 'disabled="disabled"'; ?>> 
										<?php _e('Activate', 'wp-pro-quiz'); ?>
									</label>
									<p class="description">
										<?php _e('If you enable this option, additional captcha will be displayed for users who are not registered.', 'wp-pro-quiz'); ?>
									</p>
									<p class="description" style="color: red;">
										<?php _e('This option requires additional plugin:', 'wp-pro-quiz'); ?>
										 <a href="http://wordpress.org/extend/plugins/really-simple-captcha/" target="_blank">Really Simple CAPTCHA</a>
									</p>
									<?php if($this->captchaIsInstalled) { ?>
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
										<input name="toplistDataSort" type="radio" value="1" <?php echo ($this->quiz->getToplistDataSort() == 1) ? 'checked="checked"' : ''; ?>>
										<?php _e('best user', 'wp-pro-quiz'); ?>
									</label>
									<label>
										<input name="toplistDataSort" type="radio" value="2" <?php echo ($this->quiz->getToplistDataSort() == 2) ? 'checked="checked"' : ''; ?>>
										<?php _e('newest entry', 'wp-pro-quiz'); ?>
									</label>
									<label>
										<input name="toplistDataSort" type="radio" value="3" <?php echo ($this->quiz->getToplistDataSort() == 3) ? 'checked="checked"' : ''; ?>>
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
											<input type="checkbox" name="toplistDataAddMultiple" value="1" <?php echo $this->quiz->isToplistDataAddMultiple() ? 'checked="checked"' : ''; ?>> 
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
									</div>
									<div id="toplistDataAddBlockBox" style="display: none;">
										<label>
											<?php _e('User can apply after:', 'wp-pro-quiz'); ?>
											<input type="number" min="0" class="small-text" name="toplistDataAddBlock" value="<?php echo $this->quiz->getToplistDataAddBlock(); ?>"> 
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
											<input type="number" min="0" class="small-text" name="toplistDataShowLimit" value="<?php echo $this->quiz->getToplistDataShowLimit(); ?>"> 
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
											<input type="radio" name="toplistDataShowIn" value="0" <?php echo ($this->quiz->getToplistDataShowIn() == 0) ? 'checked="checked"' : ''; ?>> 
											<?php _e('don\'t display', 'wp-pro-quiz'); ?>
										</label>
										<label>
											<input type="radio" name="toplistDataShowIn" value="1" <?php echo ($this->quiz->getToplistDataShowIn() == 1) ? 'checked="checked"' : ''; ?>> 
											<?php _e('below the "result text"', 'wp-pro-quiz'); ?>
										</label>
										<span class="wpProQuiz_demoBox" style="margin-right: 5px;">
											<a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> 
											<span style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/leaderboardInResultText.png'; ?> ">
											</span>
										</span>
										<label>
											<input type="radio" name="toplistDataShowIn" value="2" <?php echo ($this->quiz->getToplistDataShowIn() == 2) ? 'checked="checked"' : ''; ?>> 
											<?php _e('in a button', 'wp-pro-quiz'); ?>
										</label>
										<span class="wpProQuiz_demoBox">
											<a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> 
											<span style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/leaderboardInButton.png'; ?> ">
											</span>
										</span>
									</div>
								</td>
							</tr>
						</tbody>
					</table>					
				</div>
			</div>
			<div class="postbox" style="display: none;">
				<h3 class="hndle"><?php //_e('Frage-Pool Modus', 'wp-pro-quiz'); ?> <?php _e('(optional)', 'wp-pro-quiz'); ?></h3>
				<div class="inside">
				
					<p class="description" style="margin-top: 10px;">
						<?php //_e('Der Frage-Pool Modus ermoeglicht es automatisch eine bestimmte Anzahl von Fragen aus anderen Quiz anzeigen zu lassen. Es koennen nach der Aktivierung keine Fragen manuell hinzugefuegt werden. Die Statistik-Funktion ist im diesem Modus nicht verfuegbar.', 'wp-pro-quiz'); ?>
					</p>
					
					<p style="color: red;">
						<?php //_e('Der Frage-Pool Modus nicht zuverlaesslich in Verbindung mit Frontend-Cache-Plugins', 'wp-pro-quiz'); ?>
					</p>
					
					<label style="margin-top: 10px;">
						<input type="checkbox" value="1" name=""> <?php _e('Activate', 'wp-pro-quiz'); ?>
					</label>
									
					<div>
						
						<p>
							<?php //_e('Viele Fragen sollen angezeigt werden?', 'wp-pro-quiz'); ?>
						</p>
						
						<input type="text">
						
					</div>
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><?php _e('Quiz description', 'wp-pro-quiz'); ?> <?php _e('(required)', 'wp-pro-quiz'); ?></h3>
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
				<h3 class="hndle"><?php _e('Results text', 'wp-pro-quiz'); ?> <?php _e('(optional)', 'wp-pro-quiz'); ?></h3>
				<div class="inside">
					<p class="description">
						<?php _e('This text will be displayed at the end of the quiz (in results). (this text is optional)', 'wp-pro-quiz'); ?>
					</p>
					<div style="padding-top: 10px; padding-bottom: 10px;">
						<label for="wpProQuiz_resultGradeEnabled">
							<?php _e('Activate graduation', 'wp-pro-quiz'); ?>  
							<input type="checkbox" name="resultGradeEnabled" id="wpProQuiz_resultGradeEnabled" value="1" <?php echo $this->quiz->isResultGradeEnabled() ? 'checked="checked"' : ''; ?>>
						</label>
					</div>
					<div style="display: none;" id="resultGrade">
						<div>
							<strong><?php _e('Hint:', 'wp-pro-quiz'); ?></strong>
							<ul style="list-style-type: square; padding: 5px; margin-left: 20px; margin-top: 0;">
								<li><?php _e('Maximal 15 levels', 'wp-pro-quiz'); ?></li>
								<li>
									<?php printf(__('Percentages refer to the total score of the quiz. (Current total %d points in %d questions.', 'wp-pro-quiz'),
											$this->quiz->fetchSumQuestionPoints(), $this->quiz->fetchCountQuestions()); ?>
									</li>
								<li><?php _e('Values can also be mixed up', 'wp-pro-quiz'); ?></li>
								<li><?php _e('10,15% or 10.15% allowed (max. two digits after the decimal point)', 'wp-pro-quiz'); ?></li>
							</ul>
								
						</div>
						<div>
							<ul id="resultList">
							<?php
								$resultText = $this->quiz->getResultText();
								
								for($i = 0; $i < 15; $i++) {

									if($this->quiz->isResultGradeEnabled() && isset($resultText['text'][$i])) {
							?>
								<li style="padding: 5px; border: 1; border: 1px dotted;">
									<div style="margin-bottom: 5px;"><?php wp_editor($resultText['text'][$i], 'resultText_'.$i, array('textarea_rows' => 3, 'textarea_name' => 'resultTextGrade[text][]')); ?></div>
									<div style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
										<?php _e('from:', 'wp-pro-quiz'); ?> <input type="text" name="resultTextGrade[prozent][]" class="small-text" value="<?php echo $resultText['prozent'][$i]?>"> <?php _e('percent', 'wp-pro-quiz'); ?> <?php printf(__('(Will be displayed, when result-percent is >= <span class="resultProzent">%s</span>%%)', 'wp-pro-quiz'), $resultText['prozent'][$i]); ?>
										<input type="button" style="float: right;" class="button-primary deleteResult" value="<?php _e('Delete graduation', 'wp-pro-quiz'); ?>">
										<div style="clear: right;"></div>
										<input type="hidden" value="1" name="resultTextGrade[activ][]">
									</div>
								</li>
							
							<?php } else { ?>
								<li style="padding: 5px; border: 1; border: 1px dotted; <?php echo $i ? 'display:none;' : '' ?>">
									<div style="margin-bottom: 5px;"><?php wp_editor('', 'resultText_'.$i, array('textarea_rows' => 3, 'textarea_name' => 'resultTextGrade[text][]')); ?></div>
									<div style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
										<?php _e('from:', 'wp-pro-quiz'); ?> <input type="text" name="resultTextGrade[prozent][]" class="small-text" value="0"> <?php _e('percent', 'wp-pro-quiz'); ?> <?php printf(__('(Will be displayed, when result-percent is >= <span class="resultProzent">%s</span>%%)', 'wp-pro-quiz'), '0'); ?>
										<input type="button" style="float: right;" class="button-primary deleteResult" value="<?php _e('Delete graduation', 'wp-pro-quiz'); ?>">
										<div style="clear: right;"></div>
										<input type="hidden" value="<?php echo $i ? '0' : '1' ?>" name="resultTextGrade[activ][]">
									</div>
								</li>
							<?php } } ?>
							</ul>
							<input type="button" class="button-primary addResult" value="<?php _e('Add graduation', 'wp-pro-quiz'); ?>">
						</div>
					</div>
					<div id="resultNormal">
						<?php
						
							$resultText = is_array($resultText) ? '' : $resultText;
							wp_editor($resultText, 'resultText', array('textarea_rows' => 10));
						?>
					</div>
				</div>
			</div>
		<input type="submit" name="submit" class="button-primary" id="wpProQuiz_save" value="<?php _e('Save', 'wp-pro-quiz'); ?>">
		</div>
	</form>
</div>
<?php
	}
}