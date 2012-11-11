<?php

class WpProQuiz_View_QuizOverall extends WpProQuiz_View_View {
	
	public function show() {
?>
<div class="wrap wpProQuiz_quizOverall">
	<h2><?php _e('Quiz overview', 'wp-pro-quiz'); ?></h2>
	<table class="wp-list-table widefat">
		<thead>
			<tr>
				<th scope="col"><?php _e('ID', 'wp-pro-quiz'); ?></th>
				<th scope="col"><?php _e('Shortcode', 'wp-pro-quiz'); ?></th>
				<th scope="col"><?php _e('Name', 'wp-pro-quiz'); ?></th>
				<th scope="col"><?php _e('Action', 'wp-pro-quiz'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php 
			foreach ($this->quiz as $quiz) {
			?>
			<tr>
				<th><?php echo $quiz->getId(); ?></th>
				<th>[WpProQuiz <?php echo $quiz->getId(); ?>]</th>
				<th><?php echo $quiz->getName(); ?></th>
				<th>
					<a class="button-secondary" href="admin.php?page=wpProQuiz&module=question&quiz_id=<?php echo $quiz->getId(); ?>"><?php _e('Questions', 'wp-pro-quiz'); ?></a>
					<a class="button-secondary" href="admin.php?page=wpProQuiz&action=edit&id=<?php echo $quiz->getId(); ?>"><?php _e('Edit', 'wp-pro-quiz'); ?></a> 
					<a class="button-secondary wpProQuiz_delete" href="admin.php?page=wpProQuiz&action=delete&id=<?php echo $quiz->getId(); ?>"><?php _e('Delete', 'wp-pro-quiz'); ?></a>
					<a class="button-secondary wpProQuiz_prview" href="admin.php?page=wpProQuiz&module=preview&id=<?php echo $quiz->getId(); ?>"><?php _e('Preview', 'wp-pro-quiz'); ?></a>
				</th>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<p>
		<a class="button-secondary" href="admin.php?page=wpProQuiz&action=add"><?php echo __('Add quiz', 'wp-pro-quiz'); ?></a>
	</p>
</div>
		
		<?php 
	}
}