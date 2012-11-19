<?php
class WpProQuiz_View_FrontQuiz extends WpProQuiz_View_View {
	
	private function parseJson($data) {
		$r = array();
		
		foreach($data as $q) {
			$a = array();
			$a['answer_type'] = $q->getAnswerType();
			$a['id'] = $q->getId();
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
			}
			
			$r[] = $a;
		}
		
		return $r;
	}
	
	public function show($preview = false) {
			
		$question_count = count($this->question);
		
		$json = json_encode($this->parseJson($this->question));
		
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
	<div class="wpProQuiz_results">
		<h3><?php _e('Results', 'wp-pro-quiz'); ?></h3>
		<p>
			<?php printf(__('%s from %s questions answered correctly', 'wp-pro-quiz'), '<span class="wpProQuiz_points"></span>', '<span>'.$question_count.'</span>'); ?> <span class="wpProQuiz_points_prozent">(0%)</span>
		</p>
		<p class="wpProQuiz_time_limit_expired">
			<?php _e('Time has elapsed', 'wp-pro-quiz'); ?>
		</p>
		<p class="wpProQuiz_quiz_time">
			<?php _e('Your time: <span></span>', 'wp-pro-quiz') ?>
		</p>
		<div>
			<?php echo do_shortcode(apply_filters('comment_text', $this->quiz->getResultText())); ?>
		</div>
		<p>
			<input type="button" name="restartQuiz" value="<?php _e('Restart quiz', 'wp-pro-quiz'); ?>" >
			<input type="button" name="reShowQuestion" value="<?php _e('View question', 'wp-pro-quiz'); ?>">
		</p>
	</div>
	<div class="wpProQuiz_time_limit">
		<div class="time"><?php _e('Time limit', 'wp-pro-quiz'); ?>: <span>00:03:15</span></div>
		<div class="progress"></div>
	</div>
	<div class="wpProQuiz_quiz">
		<ol class="wpProQuiz_list">
		<?php 
			$index = 0; 
			foreach($this->question as $question) { 
				$index++; 
		?>
			<li class="wpProQuiz_listItem">
				<div class="wpProQuiz_question_page">
					<?php printf(__('Question %s from %s', 'wp-pro-quiz'), '<span>'.$index.'</span>', '<span>'.$question_count.'</span>'); ?>
				</div>
				<h3><span><?php echo $index; ?></span>. <?php _e('Question', 'wp-pro-quiz'); ?></h3>
				<div class="wpProQuiz_question">
					<div class="wpProQuiz_question_text">
						<?php echo do_shortcode(apply_filters('comment_text', $question->getQuestion())); ?>
					</div>
					<ul class="wpProQuiz_questionList">
					<?php
						$answerArray = $question->getAnswerJson();
						
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
					 <?php } ?>
					</ul>
				</div>
				<div class="wpProQuiz_response">
					<div class="wpProQuiz_correct">
						<span>
							<?php _e('Right', 'wp-pro-quiz'); ?>
						</span>
						<p>
							<?php echo do_shortcode(apply_filters('comment_text', $question->getCorrectMsg())); ?>
						</p>
					</div>
					<div class="wpProQuiz_incorrect">
						<span>
							<?php _e('Wrong', 'wp-pro-quiz'); ?>
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
				<input type="button" name="check" value="<?php _e('Check', 'wp-pro-quiz'); ?>" class="wpProQuiz_QuestionButton" style="float: left;">
				<input type="button" name="back" value="<?php _e('Back', 'wp-pro-quiz'); ?>" class="wpProQuiz_QuestionButton" style="float: left;">
				<input type="button" name="next" value="<?php _e('Next exercise', 'wp-pro-quiz'); ?>" class="wpProQuiz_QuestionButton" style="float: right;" >
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
		json: <?php echo $json; ?>
	});
});

</script>	
		<?php 
	}
}