<?php
class WpProQuiz_View_QuestionEdit extends WpProQuiz_View_View {
	
	public function show() {
?>
<script>
jQuery(document).ready(function($) {

});
</script>
<div class="wrap wpProQuiz_questionEdit">
	<h2 style="margin-bottom: 10px;"><?php echo $this->header; ?></h2>
	<a class="button-secondary" href="admin.php?page=wpProQuiz&module=question&action=show&quiz_id=<?php echo $this->question->getQuizId(); ?>"><?php _e('back to overview', 'wp-pro-quiz'); ?></a>
	<form action="" method="POST">
		<div id="poststuff">
			<div class="postbox">
				<h3 class="hndle"><?php _e('Title', 'wp-pro-quiz'); ?> <?php _e('(optional)', 'wp-pro-quiz'); ?></h3>
				<div class="inside">
					<p class="description">
						<?php _e('The title is used for overview, it is not visible in quiz. If you leave the title field empty, a title will be generated.', 'wp-pro-quiz'); ?>
					</p>
					<input name="title" class="regular-text" value="<?php echo $this->question->getTitle(); ?>">
				</div>
			</div>			
			<div class="postbox">
				<h3 class="hndle"><?php _e('Question', 'wp-pro-quiz'); ?> <?php _e('(required)', 'wp-pro-quiz'); ?></h3>
				<div class="inside">
					<?php 
						wp_editor($this->question->getQuestion(), "question", array('textarea_rows' => 5));
					?>
				</div>
			</div>	
			<div class="postbox">
				<h3 class="hndle"><?php _e('Message with the correct answer', 'wp-pro-quiz'); ?> <?php _e('(optional)', 'wp-pro-quiz'); ?></h3>
				<div class="inside">
					<p class="description">
						<?php _e('This text will be visible if answered correctly. It can be used as explanation for complex questions. The message "Right" or "Wrong" is always displayed automatically.', 'wp-pro-quiz'); ?>
					</p>
					<div style="padding-top: 10px; padding-bottom: 10px;">
						<label for="wpProQuiz_correctSameText">
							<?php _e('Same text for correct- and incorrect-message?', 'wp-pro-quiz'); ?>  
							<input type="checkbox" name="correctSameText" id="wpProQuiz_correctSameText" value="1" <?php echo $this->question->isCorrectSameText() ? 'checked="checked"' : '' ?>>
						</label>
					</div>
					<?php 
						wp_editor($this->question->getCorrectMsg(), "correctMsg", array('textarea_rows' => 3));
					?>
				</div>
			</div>	
			<div class="postbox" id="wpProQuiz_incorrectMassageBox">
				<h3 class="hndle"><?php _e('Message with the incorrect answer', 'wp-pro-quiz'); ?> <?php _e('(optional)', 'wp-pro-quiz'); ?></h3>
				<div class="inside">
					<p class="description">
						<?php _e('This text will be visible if answered incorrectly. It can be used as explanation for complex questions. The message "Right" or "Wrong" is always displayed automatically.', 'wp-pro-quiz'); ?>
					</p>
					<?php 
						wp_editor($this->question->getIncorrectMsg(), "incorrectMsg", array('textarea_rows' => 3));
					?>
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><?php _e('Hint', 'wp-pro-quiz'); ?> <?php _e('(optional)', 'wp-pro-quiz'); ?></h3>
				<div class="inside">
					<p class="description">
						<?php _e('Here you can enter solution hint.', 'wp-pro-quiz'); ?>
					</p>
					<div style="padding-top: 10px; padding-bottom: 10px;">
						<label for="wpProQuiz_tip">
							<?php _e('Activate hint for this question?', 'wp-pro-quiz'); ?>  
							<input type="checkbox" name="tipEnabled" id="wpProQuiz_tip" value="1" <?php echo $this->question->isTipEnabled() ? 'checked="checked"' : '' ?>>
						</label>
					</div>
					<div id="wpProQuiz_tipBox">
						<?php 
							wp_editor($this->question->getTipMsg(), 'tipMsg', array('textarea_rows' => 3));
						?>
					</div>
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><?php _e('Answer type', 'wp-pro-quiz'); ?></h3>
				<div class="inside">
				<?php
					$qa = (array) $this->question->getAnswerJson();
					$type = $this->question->getAnswerType();
				?>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="single" <?php echo ($type === 'single') ? 'checked="checked"' : ''; ?>>
						<?php _e('Single choice', 'wp-pro-quiz'); ?>
					</label>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="multiple" <?php echo ($type === 'multiple') ? 'checked="checked"' : ''; ?>>
						<?php _e('Multiple choice', 'wp-pro-quiz'); ?>
					</label>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="free_answer" <?php echo ($type === 'free_answer') ? 'checked="checked"' : ''; ?>>
						<?php _e('"Free" choice', 'wp-pro-quiz'); ?>
					</label>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="sort_answer" <?php echo ($type === 'sort_answer') ? 'checked="checked"' : ''; ?>>
						<?php _e('"Sorting" choice', 'wp-pro-quiz'); ?>
					</label>
					<label style="padding-right: 10px;">
						<input type="radio" name="answerType" value="matrix_sort_answer" <?php echo ($type === 'matrix_sort_answer') ? 'checked="checked"' : ''; ?>>
						<?php _e('"Matrix Sorting" choice', 'wp-pro-quiz'); ?>
					</label>
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><?php _e('Answers', 'wp-pro-quiz'); ?> <?php _e('(required)', 'wp-pro-quiz'); ?></h3>
				<div class="inside answer_felder">
					<div class="free_answer">
					<?php if($type === 'free_answer') { ?>
						<p style="border-bottom:1px dotted #ccc;">
							<textarea placeholder="<?php _e('correct answers (one per line) (answers will be converted to lower case)', 'wp-pro-quiz'); ?>" rows="6" cols="100" class="large-text" name="answerJson[free_answer][correct]"><?php echo $qa['free_answer']['correct']; ?></textarea>
						</p>
					<?php } else { ?>
						<p style="border-bottom:1px dotted #ccc;">
							<textarea placeholder="<?php _e('correct answers (one per line) (answers will be converted to lower case)', 'wp-pro-quiz'); ?>" rows="6" cols="100" class="large-text" name="answerJson[free_answer][correct]"></textarea>
						</p>
					<?php } ?>
					</div>
					<div class="sort_answer">
						<p class="description">
							<?php _e('Please sort the answers in right order with the "Move" - Button. The answers will be displayed randomly.', 'wp-pro-quiz'); ?>
						</p>
						<ul>
							<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; display:none; background-color: whiteSmoke;">
								<!--  <input class="small-text" name="answerJson[answer_sort][nr][]" value="" placeholder="Nr."> -->
								<textarea rows="2" cols="100" class="large-text" name="answerJson[answer_sort][answer][]"></textarea>
								<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'wp-pro-quiz'); ?>">
								<a href="#" class="button-secondary wpProQuiz_move" style="cursor: move;"><?php _e('Move', 'wp-pro-quiz'); ?></a>
								<label>
									<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
									<input type="checkbox" name="answerJson[answer_sort][html][]" value="0">
								</label>
							</li>
					<?php if($type === 'sort_answer') { 
						foreach($qa['answer_sort']['answer'] as $k => $v) { ?>
							<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;">
								<!-- <input class="small-text" name="answerJson[answer_sort][nr][]" value="<?php echo $qa['answer_sort']['nr'][$k]; ?>" placeholder="Nr.">-->
								<textarea rows="2" cols="100" class="large-text" name="answerJson[answer_sort][answer][]"><?php echo $v; ?></textarea>
								<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'wp-pro-quiz'); ?>">
								<a href="#" class="button-secondary wpProQuiz_move" style="cursor: move;"><?php _e('Move', 'wp-pro-quiz'); ?></a>
								<label>
									<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
									<input type="checkbox" name="answerJson[answer_sort][html][]" value="<?php echo $k; ?>" <?php echo (isset($qa['answer_sort']['html']) && in_array($k, $qa['answer_sort']['html'])) ? 'checked=checked' : '';  ?>>
								</label>
							</li>
					<?php } } else { ?>
							<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;">
								<textarea rows="2" cols="100" class="large-text" name="answerJson[answer_sort][answer][]"></textarea>
								<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'wp-pro-quiz'); ?>">
								<a href="#" class="button-secondary wpProQuiz_move" style="cursor: move;"><?php _e('Move', 'wp-pro-quiz'); ?></a>
								<label>
									<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
									<input type="checkbox" name="answerJson[answer_sort][html][]" value="1">
								</label>
							</li>
						<?php } ?>
						</ul>
						<input type="button" class="button-primary addAnswer" value="<?php _e('Add new answer', 'wp-pro-quiz'); ?>">
					</div>
					<div class="classic_answer">
						<ul>
							<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; display:none;">
								<textarea rows="2" cols="50" class="large-text" name="answerJson[classic_answer][answer][]"></textarea>
								<label>
									<input type="radio" name="answerJson[classic_answer][correct][]" value="0">
									<?php _e('correct?', 'wp-pro-quiz'); ?>
								</label>
								<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'wp-pro-quiz'); ?>">
								<a href="#" class="button-secondary wpProQuiz_move" style="cursor: move;"><?php _e('Move', 'wp-pro-quiz'); ?></a>
								<label>
									<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
									<input type="checkbox" name="answerJson[classic_answer][html][]" value="0">
								</label>
							</li>
						<?php if($type === 'single' || $type === 'multiple') {
							foreach($qa['classic_answer']['answer'] as $k => $v) { ?>
							<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px;">
								<textarea rows="2" cols="50" class="large-text" name="answerJson[classic_answer][answer][]"><?php echo $v; ?></textarea>
								<label>
									<input type="checkbox" name="answerJson[classic_answer][correct][]" value="<?php echo $k; ?>" 
										<?php echo (isset($qa['classic_answer']['correct']) && in_array($k, $qa['classic_answer']['correct'])) ? 'checked="checked"' : ''; ?>>
									<?php _e('correct?', 'wp-pro-quiz'); ?>
								</label>
								<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'wp-pro-quiz'); ?>">
								<a href="#" class="button-secondary wpProQuiz_move" style="cursor: move;"><?php _e('Move', 'wp-pro-quiz'); ?></a>
								<label>
									<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
									<input type="checkbox" name="answerJson[classic_answer][html][]" value="<?php echo $k; ?>" <?php echo (isset($qa['classic_answer']['html']) && in_array($k, $qa['classic_answer']['html'])) ? 'checked="checked"' : '';  ?>>
								</label>	
							</li>
						<?php } } else { ?>
							<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px;">
								<textarea rows="2" cols="50" class="large-text" name="answerJson[classic_answer][answer][]"></textarea>
								<label>
									<input type="radio" name="answerJson[classic_answer][correct][]" value="1" checked="checked">
									<?php _e('correct?', 'wp-pro-quiz'); ?>
								</label>
								<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'wp-pro-quiz'); ?>">
								<a href="#" class="button-secondary wpProQuiz_move" style="cursor: move;"><?php _e('Move', 'wp-pro-quiz'); ?></a>
								<label>
									<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
									<input type="checkbox" name="answerJson[classic_answer][html][]" value="1">
								</label>
							</li>
						<?php } ?>
						</ul>
						<input type="button" class="button-primary addAnswer" value="<?php _e('Add new answer', 'wp-pro-quiz'); ?>">
					</div>
					<div class="matrix_sort_answer">
							<p class="description">
							<?php _e('In this mode, not a list have to be sorted, but elements must be assigned to matching criterion.', 'wp-pro-quiz'); ?>
						</p>
						<ul>
							<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; display:none; background-color: whiteSmoke;">
								<table style="width: 100%; margin-bottom: 10px;">
									<thead>
										<td><?php _e('Criterion', 'wp-pro-quiz'); ?></td>
										<td><?php _e('Sort elements', 'wp-pro-quiz'); ?></td>
									</thead>
									<tbody>
										<tr>
											<td>
												<textarea rows="4" name="answerJson[answer_matrix_sort][answer][]" style="width: 100%; resize:none;"></textarea>
											</td>
											<td>
												<textarea rows="4" name="answerJson[answer_matrix_sort][sort_string][]" style="width: 100%; resize:none;"></textarea>
											</td>	
										</tr>
										<tr>
											<td>
												<label>
													<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
													<input type="checkbox" name="answerJson[answer_matrix_sort][answer_html][]" value="0">
												</label>
											</td>
											<td>
												<label>
													<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
													<input type="checkbox" name="answerJson[answer_matrix_sort][sort_string_html][]" value="0">
												</label>
											</td>
										</tr>
									</tbody>
								</table>
								<a href="#" class="button-secondary wpProQuiz_move" style="cursor: move;"><?php _e('Move', 'wp-pro-quiz'); ?></a>
								<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'wp-pro-quiz'); ?>">
							</li>
							<?php if($type === 'matrix_sort_answer') {
							foreach($qa['answer_matrix_sort']['answer'] as $k => $v) {
								$ms = $qa['answer_matrix_sort'];
							?>
							<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;">
								<table style="width: 100%; margin-bottom: 10px;">
									<thead>
										<td><?php _e('Criterion', 'wp-pro-quiz'); ?></td>
										<td><?php _e('Sort elements', 'wp-pro-quiz'); ?></td>
									</thead>
									<tbody>
										<tr>
											<td>
												<textarea rows="4" name="answerJson[answer_matrix_sort][answer][]" style="width: 100%; resize:none;"><?php echo $v; ?></textarea>
											</td>
											<td>
												<textarea rows="4" name="answerJson[answer_matrix_sort][sort_string][]" style="width: 100%; resize:none;"><?php echo $ms['sort_string'][$k]; ?></textarea>
											</td>	
										</tr>
										<tr>
											<td>
												<label>
													<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
													<input type="checkbox" name="answerJson[answer_matrix_sort][answer_html][]" value="<?php echo $k; ?>" <?php echo (isset($ms['answer_html']) && in_array($k, $ms['answer_html'])) ? 'checked="checked"' : '';  ?>>
												</label>
											</td>
											<td>
												<label>
													<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
													<input type="checkbox" name="answerJson[answer_matrix_sort][sort_string_html][]" value="<?php echo $k; ?>" <?php echo (isset($ms['sort_string_html']) && in_array($k, $ms['sort_string_html'])) ? 'checked="checked"' : '';  ?>>
												</label>
											</td>
										</tr>
									</tbody>
								</table>
								<a href="#" class="button-secondary wpProQuiz_move" style="cursor: move;"><?php _e('Move', 'wp-pro-quiz'); ?></a>
								<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'wp-pro-quiz'); ?>">
							</li>
							<?php } } else { ?>
							<li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;">
								<table style="width: 100%; margin-bottom: 10px;">
									<thead>
										<th><?php _e('Criterion', 'wp-pro-quiz'); ?></th>
										<th><?php _e('Sort elements', 'wp-pro-quiz'); ?></th>
									</thead>
									<tbody>
										<tr>
											<td>
												<textarea rows="4" name="answerJson[answer_matrix_sort][answer][]" style="width: 100%; resize:vertical;"></textarea>
											</td>
											<td>
												<textarea rows="4" name="answerJson[answer_matrix_sort][sort_string][]" style="width: 100%; resize:vertical;"></textarea>
											</td>	
										</tr>
										<tr>
											<td>
												<label>
													<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
													<input type="checkbox" name="answerJson[answer_matrix_sort][answer_html][]" value="1">
												</label>
											</td>
											<td>
												<label>
													<?php _e('Allow HTML', 'wp-pro-quiz'); ?>
													<input type="checkbox" name="answerJson[answer_matrix_sort][sort_string_html][]" value="1">
												</label>
											</td>
										</tr>
									</tbody>
								</table>
								<a href="#" class="button-secondary wpProQuiz_move" style="cursor: move;"><?php _e('Move', 'wp-pro-quiz'); ?></a>
								<input type="button" name="submit" class="button-primary deleteAnswer" value="<?php _e('Delete answer', 'wp-pro-quiz'); ?>">
							</li>
							<?php } ?>
						</ul>
						<input type="button" class="button-primary addAnswer" value="<?php _e('Add new answer', 'wp-pro-quiz'); ?>">
					</div>
				</div>
			</div>
			<input type="submit" name="submit" id="saveQuestion" class="button-primary" value="<?php _e('Save', 'wp-pro-quiz'); ?>">			
		</div>
	</form>
</div>

<?php
	}
}