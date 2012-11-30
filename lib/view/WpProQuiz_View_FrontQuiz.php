<?php
class WpProQuiz_View_FrontQuiz extends WpProQuiz_View_View {
	
	private function parseJson($data) {
		$r = array();
		
		foreach($data as $q) {
			$a = array();
			$a['answer_type'] = $q->getAnswerType();
			$a['id'] = $q->getId();
			$a['points'] = $q->getPoints();
			$j = $q->getAnswerJson();
			
			switch ($q->getAnswerType()) {
				case 'single':
				case 'multiple':
					$a['correct'] = $j['classic_answer']['correct'];
					break;
				case 'sort_answer':
					$a['correct'] = array_keys(array_values($j['answer_sort']['answer']));
					break;
				case 'free_answer':
					$t = str_replace("\r\n", "\n", strtolower($j['free_answer']['correct']));
					$t = str_replace("\r", "\n", $t);
					$t = explode("\n", $t);
					$a['correct'] = array_values(array_filter(array_map('trim', $t)));
					break;
				case 'matrix_sort_answer':
					$a['correct'] = array_keys(array_values($j['answer_matrix_sort']['sort_string']));
					break;
			}
			
			$r[] = $a;
		}

		return $r;
	}
	
	public function show($preview = false) {

		$question_count = count($this->question);
		
		$json = json_encode($this->parseJson($this->question));
		
		$result = $this->quiz->getResultText();

		if(!$this->quiz->isResultGradeEnabled()) {
			$r = array();
			$r['text'][] = $result;
			$r['prozent'][] = 0;
			
			$result = $r;
		}

		$resultsProzent = json_encode($result['prozent']);
		
		?>

<div class="wpProQuiz_content" id="wpProQuiz_<?php echo $this->quiz->getId(); ?>">
	<?php if(!$this->quiz->isTitleHidden()) { ?>
	<h2><?php echo $this->quiz->getName(); ?></h2>
	<?php } ?>
	<div class="wpProQuiz_text">
		<p>
			<?php echo do_shortcode(apply_filters('comment_text', $this->quiz->getText())); ?>
		</p>
		<div>
			<input type="button" value="<?php _e('Start quiz', 'wp-pro-quiz'); ?>" name="startQuiz">
		</div>
	</div>
	<div style="display: none;" class="wpProQuiz_results">
		<h3><?php _e('Results', 'wp-pro-quiz'); ?></h3>
		<p>
			<?php printf(__('%s of %s questions answered correctly', 'wp-pro-quiz'), '<span class="wpProQuiz_correct_answer"></span>', '<span>'.$question_count.'</span>'); ?>
		</p>
		<p class="wpProQuiz_quiz_time">
			<?php _e('Your time: <span></span>', 'wp-pro-quiz') ?>
		</p>
		<p class="wpProQuiz_time_limit_expired">
			<?php _e('Time has elapsed', 'wp-pro-quiz'); ?>
		</p>
		<p class="wpProQuiz_points">
			<?php _e('You have reached <span></span> of <span></span> points, (<span></span>%)', 'wp-pro-quiz') ?>
		</p>
		<div>
			<ul class="wpProQuiz_resultsList">
				<?php foreach($result['text'] as $resultText) { ?>
				<li>
					<div>
						<?php echo do_shortcode(apply_filters('comment_text', $resultText)); ?>
					</div>
				</li>
				<?php } ?>
			</ul>
		</div>
		<p>
			<input type="button" name="restartQuiz" value="<?php _e('Restart quiz', 'wp-pro-quiz'); ?>" >
			<input type="button" name="reShowQuestion" value="<?php _e('View question', 'wp-pro-quiz'); ?>">
		</p>
	</div>
	<div style="display: none;" class="wpProQuiz_time_limit">
		<div class="time"><?php _e('Time limit', 'wp-pro-quiz'); ?>: <span>00:03:15</span></div>
		<div class="progress"></div>
	</div>
	<div style="display: none;" class="wpProQuiz_quiz">
		<ol class="wpProQuiz_list">
		<?php 
			$index = 0; 
			foreach($this->question as $question) { 
				$index++;
				$answerArray = $question->getAnswerJson();
		?>
			<li class="wpProQuiz_listItem">
				<div class="wpProQuiz_question_page">
					<?php printf(__('Question %s of %s', 'wp-pro-quiz'), '<span>'.$index.'</span>', '<span>'.$question_count.'</span>'); ?>
					
					<?php if($this->quiz->isShowPoints()) { ?>
					<span style="float:right;"><?php printf(__('%d points', 'wp-pro-quiz'), $question->getPoints()); ?></span>
					<div style="clear: right;"></div>
					<?php } ?>
				</div>
				<h3><span><?php echo $index; ?></span>. <?php _e('Question', 'wp-pro-quiz'); ?></h3>
				<div class="wpProQuiz_question" style="margin: 10px 0px 0px 0px;">
					<div class="wpProQuiz_question_text">
						<?php echo do_shortcode(apply_filters('comment_text', $question->getQuestion())); ?>
					</div>
					<?php if($question->getAnswerType() === 'matrix_sort_answer') { ?>
					<div class="wpProQuiz_matrixSortString">
						<h3><?php _e('Sort elements', 'wp-pro-quiz'); ?></h3>
						<ul class="wpProQuiz_sortStringList">
						<?php
						 	foreach($answerArray['answer_matrix_sort']['sort_string'] as $k => $v) {
						 ?>
						 <li class="wpProQuiz_sortStringItem"><?php echo (isset($answerArray['answer_matrix_sort']['sort_string_html']) && in_array($k, $answerArray['answer_matrix_sort']['sort_string_html'])) ? $v : esc_html($v); ?></li>
						<?php } ?>
						</ul>
						<div style="clear: both;"></div>
					</div>
					<?php } ?>
					<ul class="wpProQuiz_questionList">
					<?php
						if($question->getAnswerType() === 'single' || $question->getAnswerType() === 'multiple') {
							$answer_index = 1; 
							foreach($answerArray['classic_answer']['answer'] as $k => $v) {
								$answer_text = (isset($answerArray['classic_answer']['html']) && in_array($k, $answerArray['classic_answer']['html'])) ? $v : esc_html($v); 
						?>
							
						<li class="wpProQuiz_questionListItem">
							<label>
								<input type="<?php echo $question->getAnswerType() === 'single' ? 'radio' : 'checkbox' ?>" name="question" value="<?php echo $answer_index; ?>"> <?php echo $answer_text; ?>
							</label>
						</li>
						
					<?php $answer_index++; } 
						} else if($question->getAnswerType() === 'sort_answer') {
							foreach($answerArray['answer_sort']['answer'] as $k => $v) {
					 ?>
						<li class="wpProQuiz_questionListItem">
							<div class="wpProQuiz_sortable">
								<?php echo (isset($answerArray['answer_sort']['html']) && in_array($k, $answerArray['answer_sort']['html'])) ? $v : esc_html($v); ?>
							</div>
						</li>
					 <?php } } else if($question->getAnswerType() === 'free_answer') {
					 		
					 	?>
					 	<li class="wpProQuiz_questionListItem">
							<label>
								<input type="text" name="question" style="width: 300px;">
							</label>
						</li>
					 <?php } else if($question->getAnswerType() === 'matrix_sort_answer') { 
					 	foreach($answerArray['answer_matrix_sort']['answer'] as $k => $v) {
							$ma = $answerArray['answer_matrix_sort'];
					 	?>
					 	
					 	<li class="wpProQuiz_questionListItem">
							<table>
								<tbody>
									<tr class="wpProQuiz_mextrixTr">
										<td width="20%"><div class="wpProQuiz_maxtrixSortText" ><?php echo (isset($ma['answer_html']) && in_array($k, $ma['answer_html'])) ? $v : esc_html($v); ?></div></td>
										<td width="80%" >
											<ul class="wpProQuiz_maxtrixSortCriterion"></ul>
										</td>
									</tr>
								</tbody>
							</table>
						</li>
					 <?php } } else if($question->getAnswerType() === 'cloze_answer') { ?>
					 	<li class="wpProQuiz_questionListItem">
					 		<?php 
					 			$clozeText = $answerArray['answer_cloze']['text'];
					 			
					 			$clozeText = do_shortcode(apply_filters('comment_text', $clozeText));
					 			
					 			$input = '<span class="wpProQuiz_cloze"><input type="text" value="">'; 

					 			$clozeText = preg_replace('#\{(.*?)\}#', $input.' <span class="wpProQuiz_clozeCorrect" style="display: none;">(\1)</span></span>', $clozeText);
					 			
					 			echo $clozeText;
					 		?>
					 	</li>
					 <?php } ?>
					</ul>
				</div>
				<div class="wpProQuiz_response">
					<div style="display: none;" class="wpProQuiz_correct">
						<span>
							<?php _e('Correct', 'wp-pro-quiz'); ?>
						</span>
						<p>
							<?php echo do_shortcode(apply_filters('comment_text', $question->getCorrectMsg())); ?>
						</p>
					</div>
					<div style="display: none;" class="wpProQuiz_incorrect">
						<span>
							<?php _e('Incorrect', 'wp-pro-quiz'); ?>
						</span>
						<p>
							<?php 
							
								if($question->isCorrectSameText()) {
									echo do_shortcode(apply_filters('comment_text', $question->getCorrectMsg()));
								} else {
									echo do_shortcode(apply_filters('comment_text', $question->getIncorrectMsg())); 
								}
							
							?>
						</p>
					</div>
				</div>
				<div class="wpProQuiz_tipp" style="display: none;">
					<h3><?php _e('Hint', 'wp-pro-quiz'); ?></h3>
					<?php 
						if($question->isTipEnabled()) {
							echo do_shortcode(apply_filters('comment_text', $question->getTipMsg()));
						}
					?>
				</div>
				<input type="button" name="check" value="<?php _e('Check', 'wp-pro-quiz'); ?>" class="wpProQuiz_QuestionButton" style="float: left !important; margin-right: 10px !important; display: none;">
				<input type="button" name="back" value="<?php _e('Back', 'wp-pro-quiz'); ?>" class="wpProQuiz_QuestionButton" style="float: left !important; margin-right: 10px !important; display: none;">
				<?php if($question->isTipEnabled()) { ?>
				<input type="button" name="tip" value="<?php _e('Hint', 'wp-pro-quiz'); ?>" class="wpProQuiz_QuestionButton wpProQuiz_TipButton" style="float: left !important; display: inline-block;">
				<?php } ?>
				<input type="button" name="next" value="<?php _e('Next exercise', 'wp-pro-quiz'); ?>" class="wpProQuiz_QuestionButton" style="float: right; display: none;" >
				<div style="clear: both;"></div>
			</li>
		
		<?php } ?>
		</ol>
	</div>
</div>
<script>
jQuery(document).ready(function($) {
	$('#wpProQuiz_<?php echo $this->quiz->getId(); ?>').wpProQuizFront({
		questionRandom: <?php echo (int)$this->quiz->isQuestionRandom(); ?>,
		answerRandom: <?php echo (int)$this->quiz->isAnswerRandom(); ?>,
		timeLimit: <?php echo (int)$this->quiz->getTimeLimit(); ?>,
		checkAnswer: <?php echo (int)$this->quiz->isCheckAnswer(); ?>,
		backButton: <?php echo (int)$this->quiz->isBackButton();?>,
		quizId: <?php echo (int)$this->quiz->getId(); ?>,
		statisticsOn: <?php echo $preview ? 0 : (int)$this->quiz->isStatisticsOn(); ?>,
		url: '<?php echo admin_url('admin-ajax.php'); ?>',
		resultsGrade: <?php echo $resultsProzent; ?>,
		json: <?php echo $json; ?>
	});
});
</script>	
		<?php 
	}
}