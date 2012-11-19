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
					<input name="name" type="text" class="regular-text" value="<?php echo $this->quiz->getName(); ?>">
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
										</label>
										<p class="description">
											<?php _e('The title serves as quiz heading.', 'wp-pro-quiz'); ?>
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
											<input type="text" id="time_limit" value="<?php echo $this->quiz->getTimeLimit(); ?>" name="timeLimit"> <?php _e('Seconds', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('0 = no limit', 'wp-pro-quiz'); ?>
										</p>
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
											<input type="checkbox" id="check_next" value="1" name="checkAnswer" <?php echo $this->quiz->isCheckAnswer() ? 'checked="checked"' : '' ?>> 
										</label>
										<p class="description">
											<?php _e('Show "right or wrong" after the question. Otherwise the solutions will be displayed at the end.', 'wp-pro-quiz'); ?>
										</p>
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
											<input type="checkbox" id="back_button" value="1" name="backButton" <?php echo $this->quiz->isBackButton() ? 'checked="checked"' : '' ?>>
										</label>
										<p class="description">
											<?php _e('Allow to use back button in a question. (Option will be ignored if "Check -> Continue" was used)', 'wp-pro-quiz'); ?>
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
											<input type="checkbox" id="statistics_on" value="1" name="statisticsOn" <?php echo $this->quiz->isStatisticsOn() ? 'checked="checked"' : '' ?>>
											<?php _e('activate', 'wp-pro-quiz'); ?>
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
											<input type="text" id="statistics_ip_lock" value="<?php echo ($this->quiz->getStatisticsIpLock() === null) ? 1440 : $this->quiz->getStatisticsIpLock(); ?>" name="statisticsIpLock">
											<?php _e('in minutes (recommended 1440 minutes = 1 day)', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('Protect the statistics from spam. Result will only be saved every X minutes from same IP. (0 = deactivated)', 'wp-pro-quiz'); ?>
										</p>
									</fieldset>
								</td>
							</tr>
						</tbody>
					</table>
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
					<?php
						wp_editor($this->quiz->getResultText(), "resultText"); 
					?>
				</div>
			</div>
			<input type="submit" name="submit" class="button-primary" value="<?php _e('Save', 'wp-pro-quiz'); ?>">
		</div>
	</form>
</div>
<?php
	}
}