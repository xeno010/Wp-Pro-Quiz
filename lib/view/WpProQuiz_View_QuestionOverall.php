<?php
class WpProQuiz_View_QuestionOverall extends WpProQuiz_View_View {
	
	public function show() {
?>
<div class="wrap wpProQuiz_questionOverall">
	<h2>Quiz: <?php echo $this->quiz->getName(); ?></h2>
	<div id="sortMsg" class="updated" style="display: none;"><p><strong><?php _e('Questions sorted', 'wp-pro-quiz'); ?></strong></p></div>
	<p><a class="button-secondary" href="admin.php?page=wpProQuiz"><?php _e('back to overview', 'wp-pro-quiz'); ?></a></p>
	<table class="wp-list-table widefat">
		<thead>
			<tr>
				<th style="width: 50px;"></th>
				<th scope="col"><?php _e('Name', 'wp-pro-quiz'); ?></th>
				<th><?php _e('Action', 'wp-pro-quiz'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$index = 1;
			foreach ($this->question as $question) {
			?>
			<tr id="wpProQuiz_questionId_<?php echo $question->getId(); ?>">
				<th><?php echo $index++; ?></th>
				<th><?php echo $question->getTitle(); ?></th>
				<th>
					<a class="button-secondary" href="admin.php?page=wpProQuiz&module=question&action=edit&quiz_id=<?php echo $this->quiz->getId(); ?>&id=<?php echo $question->getId(); ?>"><?php _e('Edit', 'wp-pro-quiz'); ?></a> 
					<a class="button-secondary wpProQuiz_delete" href="admin.php?page=wpProQuiz&module=question&action=delete&quiz_id=<?php echo $this->quiz->getId(); ?>&id=<?php echo $question->getId(); ?>"><?php _e('Delete', 'wp-pro-quiz'); ?></a>
					<a class="button-secondary wpProQuiz_move" href="#" style="cursor:move;"><?php _e('Move', 'wp-pro-quiz'); ?></a>
				</th>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<p>
		<a class="button-secondary" href="admin.php?page=wpProQuiz&module=question&action=add&quiz_id=<?php echo $this->quiz->getId(); ?>"><?php _e('Add question', 'wp-pro-quiz'); ?></a>
		<a class="button-secondary" href="#" id="wpProQuiz_saveSort"><?php _e('Save order', 'wp-pro-quiz'); ?></a>
	</p>
</div>
<?php 
	}
}