<?php
class WpProQuiz_View_QuestionOverall extends WpProQuiz_View_View {
	
	public function show() {
?>
<style>
.wpProQuiz_questionCopy {
	padding: 20px; 
	background-color: rgb(223, 238, 255); 
	border: 1px dotted;
	margin-top: 10px;
	display: none;
}
</style>
<div class="wrap wpProQuiz_questionOverall">
	<h2>Quiz: <?php echo $this->quiz->getName(); ?></h2>
	<div id="sortMsg" class="updated" style="display: none;"><p><strong><?php _e('Questions sorted', 'wp-pro-quiz'); ?></strong></p></div>
	<p>
		<a class="button-secondary" href="admin.php?page=wpProQuiz"><?php _e('back to overview', 'wp-pro-quiz'); ?></a>
		<?php if(current_user_can('wpProQuiz_edit_quiz')) { ?>
			<a class="button-secondary" href="admin.php?page=wpProQuiz&action=addEdit&quizId=<?php echo $this->quiz->getId(); ?>"><?php _e('Edit quiz', 'wp-pro-quiz'); ?></a> 
		<?php } ?>
	</p>
	<table class="wp-list-table widefat">
		<thead>
			<tr>
				<th scope="col" style="width: 50px;"></th>
				<th scope="col"><?php _e('Name', 'wp-pro-quiz'); ?></th>
				<th scope="col"><?php _e('Category', 'wp-pro-quiz'); ?></th>
				<th scope="col"><?php _e('Points', 'wp-pro-quiz'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$index = 1;
			$points = 0;

			if(count($this->question)) {

			foreach ($this->question as $question) {
				$points += $question->getPoints();
				
			?>
			<tr id="wpProQuiz_questionId_<?php echo $question->getId(); ?>">
				<th><?php echo $index++; ?></th>
				<td>
					<strong><?php echo $question->getTitle(); ?></strong>
					<div class="row-actions">
						<?php if(current_user_can('wpProQuiz_edit_quiz')) { ?>
						<span>
							<a href="admin.php?page=wpProQuiz&module=question&action=addEdit&quiz_id=<?php echo $this->quiz->getId(); ?>&questionId=<?php echo $question->getId(); ?>"><?php _e('Edit', 'wp-pro-quiz'); ?></a> | 
						</span>
						<?php } if(current_user_can('wpProQuiz_delete_quiz')) { ?>
						<span>
							<a style="color: red;" class="wpProQuiz_delete" href="admin.php?page=wpProQuiz&module=question&action=delete&quiz_id=<?php echo $this->quiz->getId(); ?>&id=<?php echo $question->getId(); ?>"><?php _e('Delete', 'wp-pro-quiz'); ?></a> | 
						</span>
						<?php } if(current_user_can('wpProQuiz_edit_quiz')) { ?>
						<span>
							<a class="wpProQuiz_move" href="#" style="cursor:move;"><?php _e('Move', 'wp-pro-quiz'); ?></a>
						</span>
						<?php } ?>
					</div>
				</td>
				<td>
					<?php echo $question->getCategoryName(); ?>
				</td>
				<td><?php echo $question->getPoints(); ?></td>
			</tr>
			<?php } } else { ?>
				<tr>
					<td colspan="4" style="text-align: center; font-weight: bold; padding: 10px;"><?php _e('No data available', 'wp-pro-quiz'); ?></td>
				</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th></th>
				<th style="font-weight: bold;"><?php _e('Total', 'wp-pro-quiz'); ?></th>
				<th></th>
				<th style="font-weight: bold;"><?php echo $points; ?></th>
			</tr>
		</tfoot>
	</table>
	<p>
		<?php if(current_user_can('wpProQuiz_edit_quiz')) { ?>
		<a class="button-secondary" href="admin.php?page=wpProQuiz&module=question&action=addEdit&quiz_id=<?php echo $this->quiz->getId(); ?>"><?php _e('Add question', 'wp-pro-quiz'); ?></a>
		<a class="button-secondary" href="#" id="wpProQuiz_saveSort"><?php _e('Save order', 'wp-pro-quiz'); ?></a>
		<a class="button-secondary" href="#" id="wpProQuiz_questionCopy"><?php _e('Copy questions from another Quiz', 'wp-pro-quiz'); ?></a>
		<?php } ?>
	</p>
	<div class="wpProQuiz_questionCopy">
		<form action="admin.php?page=wpProQuiz&module=question&quiz_id=<?php echo $this->quiz->getId(); ?>&action=copy_question" method="POST">
			<h3 style="margin-top: 0;"><?php _e('Copy questions from another Quiz', 'wp-pro-quiz'); ?></h3>
			<p><?php echo __('Here you can copy questions from another quiz into this quiz. (Multiple selection enabled)', 'wp-pro-quiz'); ?></p>
			
			<div style="padding: 20px; display: none;" id="loadDataImg">
				<img alt="load" src="data:image/gif;base64,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">
				<?php echo __('Loading', 'wp-pro-quiz'); ?>
			</div>
			
			<div style="padding: 10px;">
				<select name="copyIds[]" size="15" multiple="multiple" style="min-width: 200px; display: none;" id="questionCopySelect">
				</select>
			</div>
			
			<input class="button-primary" name="questionCopy" value="<?php echo __('Copy questions', 'wp-pro-quiz'); ?>" type="submit">
		</form>
	</div>
</div>
<?php 
	}
}