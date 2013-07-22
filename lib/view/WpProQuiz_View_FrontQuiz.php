<?php
class WpProQuiz_View_FrontQuiz extends WpProQuiz_View_View {
	
	/**
	 * @var WpProQuiz_Model_Quiz
	 */
	public $quiz;
	
	private $_clozeTemp = array();
	private $_assessmetTemp = array();
	
	private function getFreeCorrect($data) {
		$t = str_replace("\r\n", "\n", strtolower($data->getAnswer()));
		$t = str_replace("\r", "\n", $t);
		$t = explode("\n", $t);
		return array_values(array_filter(array_map('trim', $t)));
	}
	
	public function show($preview = false) {

		$question_count = count($this->question);
		
		$globalPoints = 0;
		
		$result = $this->quiz->getResultText();

		if(!$this->quiz->isResultGradeEnabled()) {
			$result = array(
				'text' => array($result),
				'prozent' => array(0)
			);
		}

		$resultsProzent = json_encode($result['prozent']);
		
		$mode = $this->quiz->getQuizModus();
			
		$json = array();
		$catPoints = array();
		
?>

<div class="wpProQuiz_content" id="wpProQuiz_<?php echo $this->quiz->getId(); ?>">
	<?php if(!$this->quiz->isTitleHidden()) { ?>
	<h2><?php echo $this->quiz->getName(); ?></h2>
	<?php } ?>
	<div style="display: none;" class="wpProQuiz_time_limit">
		<div class="time"><?php _e('Time limit', 'wp-pro-quiz'); ?>: <span>0</span></div>
		<div class="wpProQuiz_progress"></div>
	</div>
	<div class="wpProQuiz_checkPage" style="display: none;">
		<h4 class="wpProQuiz_header"><?php _e('Quiz-summary', 'wp-pro-quiz'); ?></h4>
		<p>
			<?php printf(__('%s of %s questions completed', 'wp-pro-quiz'), '<span>0</span>', $question_count); ?>
		</p>
		<p><?php _e('Questions', 'wp-pro-quiz'); ?>:</p>
		<div style="margin-bottom: 20px;">
			<ol>
				<?php for($xy = 1; $xy <= $question_count; $xy++) { ?>
					<li><?php echo $xy; ?></li>
				<?php } ?>
			</ol>
			<div style="clear: both;"></div>
		</div>
		<input type="button" name="endQuizSummary" value="<?php _e('Finish quiz', 'wp-pro-quiz'); ?>" class="wpProQuiz_button" >
	</div>
	<div class="wpProQuiz_text">
		<p>
			<?php echo do_shortcode(apply_filters('comment_text', $this->quiz->getText())); ?>
		</p>
		<div>
			<input class="wpProQuiz_button" type="button" value="<?php _e('Start quiz', 'wp-pro-quiz'); ?>" name="startQuiz">
		</div>
	</div>
	<div style="display: none;" class="wpProQuiz_lock">
		<p>
			<?php _e('You have already completed the quiz before. Hence you can not start it again.', 'wp-pro-quiz'); ?>
		</p>
	</div>
	<div style="display: none;" class="wpProQuiz_prerequisite">
		<p>
			<?php _e('You have to finish following quiz, to start this quiz:', 'wp-pro-quiz'); ?> 
			<span></span>
		</p>
	</div>
	<div style="display: none;" class="wpProQuiz_results">
		<h4 class="wpProQuiz_header"><?php _e('Results', 'wp-pro-quiz'); ?></h4>
		<?php if(!$this->quiz->isHideResultCorrectQuestion()) { ?>
		<p>
			<?php printf(__('%s of %s questions answered correctly', 'wp-pro-quiz'), '<span class="wpProQuiz_correct_answer">0</span>', '<span>'.$question_count.'</span>'); ?>
		</p>
		<?php } if(!$this->quiz->isHideResultQuizTime()) { ?>
		<p class="wpProQuiz_quiz_time">
			<?php _e('Your time: <span></span>', 'wp-pro-quiz'); ?>
		</p>
		<?php } ?>
		<p class="wpProQuiz_time_limit_expired" style="display: none;">
			<?php _e('Time has elapsed', 'wp-pro-quiz'); ?>
		</p>
		<?php if(!$this->quiz->isHideResultPoints()) { ?>
		<p class="wpProQuiz_points">
			<?php printf(__('You have reached %s of %s points, (%s)', 'wp-pro-quiz'), '<span>0</span>', '<span>0</span>', '<span>0</span>'); ?>
		</p>
		<?php } ?>
		<?php if($this->quiz->isShowAverageResult()) { ?>
		<div class="wpProQuiz_resultTable">
			<table>
				<tbody>
					<tr>
						<td class="wpProQuiz_resultName"><?php _e('Average score', 'wp-pro-quiz'); ?></td>
						<td class="wpProQuiz_resultValue">
							<div style="background-color: #6CA54C;">&nbsp;</div>
							<span>&nbsp;</span>
						</td>
					</tr>
					<tr>
						<td class="wpProQuiz_resultName"><?php _e('Your score', 'wp-pro-quiz'); ?></td>
						<td class="wpProQuiz_resultValue">
							<div style="background-color: #F79646;">&nbsp;</div>
							<span>&nbsp;</span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php } ?>
		<div class="wpProQuiz_catOverview" <?php $this->isDisplayNone($this->quiz->isShowCategoryScore()); ?>>
			<h4><?php _e('Categories', 'wp-pro-quiz'); ?></h4>
			<div style="margin-top: 10px;">
				<ol>
					<?php foreach($this->category as $cat) {
						if(!$cat->getCategoryId()) {
							$cat->setCategoryName(__('Not categorized', 'wp-pro-quiz'));
						}
					?>
					<li data-category_id="<?php echo $cat->getCategoryId(); ?>">
						<span class="wpProQuiz_catName"><?php echo $cat->getCategoryName(); ?></span>
						<span class="wpProQuiz_catPercent">0%</span>
					</li>
					<?php } ?>
				</ol>
			</div>
		</div>
		<div>
			<ul class="wpProQuiz_resultsList">
				<?php foreach($result['text'] as $resultText) { ?>
				<li style="display: none;">
					<div>
						<?php echo do_shortcode(apply_filters('comment_text', $resultText)); ?>
					</div>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php 
			if($this->quiz->isToplistActivated()) {
				if($this->quiz->getToplistDataShowIn() == WpProQuiz_Model_Quiz::QUIZ_TOPLIST_SHOW_IN_NORMAL) {
					echo do_shortcode('[WpProQuiz_toplist '.$this->quiz->getId().' q="true"]');
				}
				
				$this->showAddToplist();
			}
		?>
		<div style="margin: 10px 0px;">
			<?php if(!$this->quiz->isBtnRestartQuizHidden()) { ?>
			<input class="wpProQuiz_button" type="button" name="restartQuiz" value="<?php _e('Restart quiz', 'wp-pro-quiz'); ?>" >
			<?php } if(!$this->quiz->isBtnViewQuestionHidden()) { ?>
			<input class="wpProQuiz_button" type="button" name="reShowQuestion" value="<?php _e('View questions', 'wp-pro-quiz'); ?>">
			<?php } ?>
			<?php if($this->quiz->isToplistActivated() && $this->quiz->getToplistDataShowIn() == WpProQuiz_Model_Quiz::QUIZ_TOPLIST_SHOW_IN_BUTTON) { ?>
			<input class="wpProQuiz_button" type="button" name="showToplist" value="<?php _e('Show leaderboard', 'wp-pro-quiz'); ?>">
			<?php } ?>
		</div>
	</div>
	<?php 
	if($this->quiz->getToplistDataShowIn() == WpProQuiz_Model_Quiz::QUIZ_TOPLIST_SHOW_IN_BUTTON) { ?>
	<div class="wpProQuiz_toplistShowInButton" style="display: none;">
		<?php echo do_shortcode('[WpProQuiz_toplist '.$this->quiz->getId().' q="true"]'); ?>
	</div>
	<?php } ?>	
	<div class="wpProQuiz_reviewDiv" style="display: none;">
		<div class="wpProQuiz_reviewQuestion">
			<ol>
				<?php for($xy = 1; $xy <= $question_count; $xy++) { ?>
					<li><?php echo $xy; ?></li>
				<?php } ?>
			</ol>
			<div style="display: none;"></div>
		</div>
		<div class="wpProQuiz_reviewLegend">
			<ol>
				<li>
					<span class="wpProQuiz_reviewColor" style="background-color: #6CA54C;"></span>
					<span class="wpProQuiz_reviewText"><?php _e('Answered', 'wp-pro-quiz'); ?></span>
				</li>
				<li>
					<span class="wpProQuiz_reviewColor" style="background-color: #FFB800;"></span>
					<span class="wpProQuiz_reviewText"><?php _e('Review', 'wp-pro-quiz'); ?></span>
				</li>
			</ol>
			<div style="clear: both;"></div>
		</div>
		<div>
			<?php if($this->quiz->getQuizModus() != WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE) { ?>
			<input type="button" name="review" value="<?php _e('Review question', 'wp-pro-quiz'); ?>" class="wpProQuiz_button2" style="float: left; display: block;">
			<?php if(!$this->quiz->isQuizSummaryHide()) { ?>
				<input type="button" name="quizSummary" value="<?php _e('Quiz-summary', 'wp-pro-quiz'); ?>" class="wpProQuiz_button2" style="float: right;" >
			<?php } ?>
			<div style="clear: both;"></div>
			<?php } ?>
		</div>
	</div>
	<div style="display: none;" class="wpProQuiz_quiz">
		<ol class="wpProQuiz_list">
		<?php 
			$index = 0; 
			foreach($this->question as $question) { 
				$index++;
				$answerArray = $question->getAnswerData();
				
				$globalPoints += $question->getPoints();
				
				
				$json[$question->getId()]['type'] = $question->getAnswerType();
				$json[$question->getId()]['id'] = (int)$question->getId();
				$json[$question->getId()]['catId'] = (int)$question->getCategoryId();
				
				if($question->isAnswerPointsActivated() && $question->isAnswerPointsDiffModusActivated() && $question->isDisableCorrect()) {
					$json[$question->getId()]['disCorrect'] = (int)$question->isDisableCorrect();
				}
				
				if(!isset($catPoints[$question->getCategoryId()]))
					$catPoints[$question->getCategoryId()] = 0;
				
				$catPoints[$question->getCategoryId()] += $question->getPoints();
				
				if(!$question->isAnswerPointsActivated()) {
					$json[$question->getId()]['points'] = $question->getPoints();
// 					$catPoints[$question->getCategoryId()] += $question->getPoints();
				}
				
				if($question->isAnswerPointsActivated() && $question->isAnswerPointsDiffModusActivated()) {
// 					$catPoints[$question->getCategoryId()] += $question->getPoints();
					$json[$question->getId()]['diffMode'] = 1;
				}
				
		?>
			<li class="wpProQuiz_listItem" style="display: none;">
				<div class="wpProQuiz_question_page" <?php $this->isDisplayNone($this->quiz->getQuizModus() != WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE && !$this->quiz->isHideQuestionPositionOverview()); ?> >
					<?php printf(__('Question %s of %s', 'wp-pro-quiz'), '<span>'.$index.'</span>', '<span>'.$question_count.'</span>'); ?>
				</div>
				<h5 style="<?php echo $this->quiz->isHideQuestionNumbering() ? 'display: none;' : 'display: inline-block;'?>" class="wpProQuiz_header">
					<span><?php echo $index; ?></span>. <?php _e('Question', 'wp-pro-quiz'); ?>
				</h5>
				
				<?php if($this->quiz->isShowPoints()) { ?>
					<span style="font-weight: bold; float: right;"><?php printf(__('%d points', 'wp-pro-quiz'), $question->getPoints()); ?></span>
					<div style="clear: both;"></div>
				<?php } ?>

				<div class="wpProQuiz_question" style="margin: 10px 0px 0px 0px;">
					<div class="wpProQuiz_question_text">
						<?php echo do_shortcode(apply_filters('comment_text', $question->getQuestion())); ?>
					</div>
					<?php if($question->getAnswerType() === 'matrix_sort_answer') { ?>
					<div class="wpProQuiz_matrixSortString">
						<h5 class="wpProQuiz_header"><?php _e('Sort elements', 'wp-pro-quiz'); ?></h5>
						<ul class="wpProQuiz_sortStringList">
						<?php
						 	foreach($answerArray as $k => $v) {
						 ?>
						 <li class="wpProQuiz_sortStringItem" data-pos="<?php echo $k; ?>"><?php echo $v->isSortStringHtml() ? $v->getSortString() : esc_html($v->getSortString()); ?></li>
						<?php } ?>
						</ul>
						<div style="clear: both;"></div>
					</div>
					<?php } ?>
					<ul class="wpProQuiz_questionList" data-question_id="<?php echo $question->getId(); ?>" data-type="<?php echo $question->getAnswerType(); ?>">
					<?php
						$answer_index = 0;

						foreach($answerArray as $v) {
							$answer_text = $v->isHtml() ? $v->getAnswer() : esc_html($v->getAnswer()); 
							
							if($answer_text == '') {
								continue;
							}
							
							if($question->isAnswerPointsActivated()) {
								$json[$question->getId()]['points'][] = $v->getPoints();
								
// 								if(!$question->isAnswerPointsDiffModusActivated())
// 									$catPoints[$question->getCategoryId()] += $question->getPoints();
							}
							
						?>
							
							<li class="wpProQuiz_questionListItem" data-pos="<?php echo $answer_index;?>">
							
						<?php if($question->getAnswerType() === 'single' || $question->getAnswerType() === 'multiple') { ?>
							<?php $json[$question->getId()]['correct'][] = (int)$v->isCorrect(); ?>
								<span <?php echo $this->quiz->isNumberedAnswer() ? '' : 'style="display:none;"'?>></span>
								<label>
									<input class="wpProQuiz_questionInput" type="<?php echo $question->getAnswerType() === 'single' ? 'radio' : 'checkbox'; ?>" name="question_<?php echo $this->quiz->getId(); ?>_<?php echo $question->getId(); ?>" value="<?php echo ($answer_index+1); ?>"> <?php echo $answer_text; ?>
								</label>
						
						<?php } else if($question->getAnswerType() === 'sort_answer') { ?>
							<?php $json[$question->getId()]['correct'][] = (int)$answer_index; ?>
								<div class="wpProQuiz_sortable">
									<?php echo $answer_text; ?>
								</div>
					 	<?php } else if($question->getAnswerType() === 'free_answer') { ?>
					 		<?php $json[$question->getId()]['correct'] = $this->getFreeCorrect($v); ?>
								<label>
									<input class="wpProQuiz_questionInput" type="text" name="question_<?php echo $this->quiz->getId(); ?>_<?php echo $question->getId(); ?>" style="width: 300px;">
								</label>
					 	<?php } else if($question->getAnswerType() === 'matrix_sort_answer') { ?>
					 		<?php
					 			$json[$question->getId()]['correct'][] = (int)$answer_index;
					 		?>
								<table>
									<tbody>
										<tr class="wpProQuiz_mextrixTr">
											<td width="20%"><div class="wpProQuiz_maxtrixSortText" ><?php echo $answer_text; ?></div></td>
											<td width="80%" >
												<ul class="wpProQuiz_maxtrixSortCriterion"></ul>
											</td>
										</tr>
									</tbody>
								</table>
								
						 <?php } else if($question->getAnswerType() === 'cloze_answer') {
						 		$clozeData = $this->fetchCloze($v->getAnswer());

						 		$this->_clozeTemp = $clozeData['data'];
						 		
						 		$json[$question->getId()]['correct'] = $clozeData['correct'];
						 		
						 		if($question->isAnswerPointsActivated()) {
									$json[$question->getId()]['points'] = $clozeData['points'];
								}
						 		
								$cloze = do_shortcode(apply_filters('comment_text', $clozeData['replace']));
								$cloze = $clozeData['replace'];
								
								echo preg_replace_callback('#@@wpProQuizCloze@@#im', array($this, 'clozeCallback'), $cloze);
					 		} else if($question->getAnswerType() === 'assessment_answer') {
					 			$assessmentData = $this->fetchAssessment($v->getAnswer(), $this->quiz->getId(), $question->getId());
					 			
					 			
					 			
					 			$assessment = do_shortcode(apply_filters('comment_text', $assessmentData['replace']));
					 			
					 			echo preg_replace_callback('#@@wpProQuizAssessment@@#im', array($this, 'assessmentCallback'), $assessment);
					 			
					 		} ?>
					 		</li> 
					 <?php
					 	$answer_index++;
						}  
					 ?>
					</ul>
				</div>
				<?php if(!$this->quiz->isHideAnswerMessageBox()) { ?>
					<div class="wpProQuiz_response" style="display: none;">
						<div style="display: none;" class="wpProQuiz_correct">
							<?php if($question->isShowPointsInBox() && $question->isAnswerPointsActivated()) { ?>
							<div>
								<span style="float: left;">
									<?php _e('Correct', 'wp-pro-quiz'); ?>
								</span>
								<span style="float: right;"><?php echo $question->getPoints().' / '.$question->getPoints(); ?> <?php _e('Points', 'wp-pro-quiz'); ?></span>
								<div style="clear: both;"></div>
							</div>		
						<?php } else { ?>
							<span>
								<?php _e('Correct', 'wp-pro-quiz'); ?>
							</span>
						<?php } ?>
							<p>
								<?php echo do_shortcode(apply_filters('comment_text', $question->getCorrectMsg())); ?>
							</p>
						</div>
						<div style="display: none;" class="wpProQuiz_incorrect">
						<?php if($question->isShowPointsInBox() && $question->isAnswerPointsActivated()) { ?>
							<div>
								<span style="float: left;">
									<?php _e('Incorrect', 'wp-pro-quiz'); ?>
								</span>
								<span style="float: right;"><span class="wpProQuiz_responsePoints"></span> / <?php echo $question->getPoints(); ?> <?php _e('Points', 'wp-pro-quiz'); ?></span>
								<div style="clear: both;"></div>
							</div>		
						<?php } else { ?>
							<span>
								<?php _e('Incorrect', 'wp-pro-quiz'); ?>
							</span>
						<?php } ?>
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
				<?php } ?>
				
				<?php if($question->isTipEnabled()) { ?>
				<div class="wpProQuiz_tipp" style="display: none; position: relative;">
					<div>
						<h5 style="margin: 0px 0px 10px;" class="wpProQuiz_header"><?php _e('Hint', 'wp-pro-quiz'); ?></h5>
						<?php  echo do_shortcode(apply_filters('comment_text', $question->getTipMsg())); ?>
					</div>
				</div>
				<?php } ?>
				
					<?php if($this->quiz->getQuizModus() == WpProQuiz_Model_Quiz::QUIZ_MODUS_CHECK && !$this->quiz->isSkipQuestionDisabled() && $this->quiz->isShowReviewQuestion()) { ?>
						<input type="button" name="skip" value="<?php _e('Skip question', 'wp-pro-quiz'); ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: left; margin-right: 10px !important;" >
					<?php } ?>
					<input type="button" name="back" value="<?php _e('Back', 'wp-pro-quiz'); ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: left !important; margin-right: 10px !important; display: none;">
					<?php if($question->isTipEnabled()) { ?>
						<input type="button" name="tip" value="<?php _e('Hint', 'wp-pro-quiz'); ?>" class="wpProQuiz_button wpProQuiz_QuestionButton wpProQuiz_TipButton" style="float: left !important; display: inline-block; margin-right: 10px !important;">
					<?php } ?>
					<input type="button" name="check" value="<?php _e('Check', 'wp-pro-quiz'); ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: right !important; margin-right: 10px !important; display: none;">
					<input type="button" name="next" value="<?php _e('Next', 'wp-pro-quiz'); ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: right; display: none;" >
					<div style="clear: both;"></div>
					
				<?php if($this->quiz->getQuizModus() == WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE) { ?>
					<div style="margin-bottom: 20px;"></div>
				<?php } ?>
				
			</li>
		
		<?php } ?>
		</ol>
		<?php if($this->quiz->getQuizModus() == WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE) { ?>
			<div>
				<?php if($this->quiz->isShowReviewQuestion() && !$this->quiz->isQuizSummaryHide()) { ?>
					<input type="button" name="checkSingle" value="<?php _e('Quiz-summary', 'wp-pro-quiz'); ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" >
				<?php } else { ?>
					<input type="button" name="checkSingle" value="<?php _e('Finish quiz', 'wp-pro-quiz'); ?>" class="wpProQuiz_button wpProQuiz_QuestionButton" >
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</div>


<?php 
//Create Options

	$bo = 0;
	
	$bo |= ((int)$this->quiz->isAnswerRandom()) << 0;
	$bo |= ((int)$this->quiz->isQuestionRandom()) << 1;
	$bo |= ((int)$this->quiz->isDisabledAnswerMark()) << 2;
	$bo |= ((int)($this->quiz->isQuizRunOnce() || $this->quiz->isPrerequisite())) << 3;
	$bo |= ((int)$preview) << 4;
	$bo |= ((int)get_option('wpProQuiz_corsActivated')) << 5;
	$bo |= ((int)$this->quiz->isToplistDataAddAutomatic()) << 6;
	$bo |= ((int)$this->quiz->isShowReviewQuestion()) << 7;
	$bo |= ((int)$this->quiz->isQuizSummaryHide()) << 8;
	$bo |= ((int)($this->quiz->isSkipQuestion() && $this->quiz->isShowReviewQuestion())) << 9;
	$bo |= ((int)$this->quiz->isAutostart()) << 10;
	$bo |= ((int)$this->quiz->isForcingQuestionSolve()) << 11;
	$bo |= ((int)$this->quiz->isHideQuestionPositionOverview()) << 12;
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#wpProQuiz_<?php echo $this->quiz->getId(); ?>').wpProQuizFront({
		quizId: <?php echo (int)$this->quiz->getId(); ?>,
		mode: <?php echo (int)$mode; ?>,
		globalPoints: <?php echo (int)$globalPoints; ?>,
		timelimit: <?php echo (int)$this->quiz->getTimeLimit(); ?>,
		resultsGrade: <?php echo $resultsProzent; ?>,
		bo: <?php echo $bo ?>,
		catPoints: <?php echo json_encode($catPoints); ?>,
		lbn: <?php echo json_encode(($this->quiz->isShowReviewQuestion() && !$this->quiz->isQuizSummaryHide()) ? __('Quiz-summary', 'wp-pro-quiz') : __('Finish quiz', 'wp-pro-quiz')); ?>,
		json: <?php echo json_encode($json); ?>
	});
});
</script>	
		<?php 
	}
	
	private function showAddToplist() {
?>
		<div class="wpProQuiz_addToplist" style="display: none;">
			<span style="font-weight: bold;"><?php _e('Your result has been entered into leaderboard', 'wp-pro-quiz'); ?></span>
			<div style="margin-top: 6px;">
				<div class="wpProQuiz_addToplistMessage" style="display: none;"><?php _e('Loading', 'wp-pro-quiz'); ?></div>
				<div class="wpProQuiz_addBox">
					<div>
						<span>
							<label>
								<?php _e('Name', 'wp-pro-quiz'); ?>: <input type="text" placeholder="<?php _e('Name', 'wp-pro-quiz'); ?>" name="wpProQuiz_toplistName" maxlength="15" size="16" style="width: 150px;" >
							</label>
							<label> 
								<?php _e('E-Mail', 'wp-pro-quiz'); ?>: <input type="email" placeholder="<?php _e('E-Mail', 'wp-pro-quiz'); ?>" name="wpProQuiz_toplistEmail" size="20" style="width: 150px;">
							</label>
						</span>
						<div style="margin-top: 5px;">
							<label>
								<?php _e('Captcha', 'wp-pro-quiz'); ?>: <input type="text" name="wpProQuiz_captcha" size="8" style="width: 50px;">
							</label>
							<input type="hidden" name="wpProQuiz_captchaPrefix" value="0">
							<img alt="captcha" src="" class="wpProQuiz_captchaImg" style="vertical-align: middle;">
						</div>
					</div>
					<input class="wpProQuiz_button2" type="submit" value="<?php _e('Send', 'wp-pro-quiz'); ?>" name="wpProQuiz_toplistAdd">
				</div>
			</div>
		</div>
<?php 
	}
	
	private function fetchCloze($answer_text) {
		preg_match_all('#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', $answer_text, $matches, PREG_SET_ORDER);
		
		$data = array();
		
		foreach($matches as $k => $v) {
			$text = $v[1];
			$points = !empty($v[2]) ? (int)$v[2] : 1;
			$rowText = $multiTextData = array();
			$len = array();
			
			if(preg_match_all('#\[(.*?)\]#im', $text, $multiTextMatches)) {
				foreach($multiTextMatches[1] as $multiText) {
					$x = mb_strtolower(trim(html_entity_decode($multiText, ENT_QUOTES)));
					
					$len[] = strlen($x);
					$multiTextData[] = $x;
					$rowText[] = $multiText;
				}
			} else {
				$x = mb_strtolower(trim(html_entity_decode($text, ENT_QUOTES)));
				
				$len[] = strlen($x);
				$multiTextData[] = $x;
				$rowText[] = $text;
			}
			
			$a = '<span class="wpProQuiz_cloze"><input data-wordlen="'.max($len).'" type="text" value=""> ';
			$a .= '<span class="wpProQuiz_clozeCorrect" style="display: none;">('.implode(', ', $rowText).')</span></span>'; 
			
			$data['correct'][] = $multiTextData;
			$data['points'][] = $points;
			$data['data'][] = $a;
		}
		
		$data['replace'] = preg_replace('#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', '@@wpProQuizCloze@@', $answer_text);
		
		return $data;
	}
	
	private function clozeCallback($t) {
		$a = array_shift($this->_clozeTemp);
		
		return $a === null ? '' : $a;
	}
	
	private function fetchAssessment($answerText, $quizId, $questionId) {
		preg_match_all('#\{(.*?)\}#im', $answerText, $matches);
		
		$this->_assessmetTemp = array();
		$data = array();

		for($i = 0, $ci = count($matches[1]); $i < $ci; $i++) {
			$match = $matches[1][$i];
			
			preg_match_all('#\[([^\|\]]+)(?:\|(\d+))?\]#im', $match, $ms);
			
			$a = '';
			
			for($j = 0, $cj = count($ms[1]); $j < $cj; $j++) {
				$v = $ms[1][$j];

				$a .= '<label>
					<input type="radio" value="'.($j+1).'" name="question_'.$quizId.'_'.$questionId.'_'.$i.'" class="wpProQuiz_questionInput">
					'.$v.'
				</label>';
				
			}
			
			$this->_assessmetTemp[] = $a;
		}
		
		$data['replace'] = preg_replace('#\{(.*?)\}#im', '@@wpProQuizAssessment@@', $answerText);
		
		return $data;
	}
	
	private function assessmentCallback($t) {
		$a = array_shift($this->_assessmetTemp);
	
		return $a === null ? '' : $a;
	}
}