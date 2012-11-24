<?php
class WpProQuiz_View_Statistics extends WpProQuiz_View_View {
	
	public function show() {
?>
<div class="wrap wpProQuiz_statistics">
	<h2><?php printf(__('Quiz: %s - Statistics', 'wp-pro-quiz'), $this->quiz->getName()); ?></h2>
	<p><a class="button-secondary" href="admin.php?page=wpProQuiz"><?php _e('back to overview', 'wp-pro-quiz'); ?></a></p>
	<?php if(!$this->quiz->isStatisticsOn()) { ?>
	<p style="padding: 30px; background: #F7E4E4; border: 1px dotted; width: 300px;">
		<span style="font-weight: bold; padding-right: 10px;"><?php _e('Stats not enabled', 'wp-pro-quiz'); ?></span>
		<a class="button-secondary" href="admin.php?page=wpProQuiz&action=edit&id=<?php echo $this->quiz->getId(); ?>"><?php _e('Activate statistics', 'wp-pro-quiz'); ?></a>
	</p>
	<?php return; } ?>
	<table class="wp-list-table widefat">
		<thead>
			<tr>
				<th scope="col" style="width: 50px;"></th>
				<th scope="col"><?php _e('Question', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 100px;"><?php _e('Incorrect', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 100px;"><?php _e('Correct', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 100px;"><?php _e('Hints used', 'wp-pro-quiz'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php 
		
		$index = 1;
		$gCount = 0;
		$gIncorrectCount = 0;
		$gCorrectCount = 0;
		$gTipCount = 0;
		
		foreach($this->question as $question) {
			$count = $question->getCorrectCount() + $question->getIncorrectCount();
			$p = $count / 100;
			$correctP = ($count > 0) ? (100 * $question->getCorrectCount() / $count) : 0;
			$incorrectP = ($count > 0) ? (100 * $question->getIncorrectCount() / $count) : 0;
			$gCount += $count;
			$gIncorrectCount += $question->getIncorrectCount();
			$gCorrectCount += $question->getCorrectCount();
			$gTipCount += $question->getTipCount();
		?>
			<tr>
				<th><?php echo $index++; ?></th>
				<th><?php echo $question->getTitle(); ?></th>
				<th style="color: red;"><?php echo $question->getIncorrectCount().' ('.round($incorrectP, 2).'%)'; ?></th>
				<th style="color: green;"><?php echo $question->getCorrectCount().' ('.round($correctP, 2).'%)'; ?></th>
				<th><?php echo $question->getTipCount(); ?></th>
			</tr>
		<?php } ?>
		</tbody>
		<tfoot>
			<?php 
			$gCorrectP = ($gCount > 0) ? (100 * $gCorrectCount / $gCount) : 0;
			$gIncorrectP = ($gCount > 0) ? (100 * $gIncorrectCount / $gCount) : 0;
			?>
			<th></th>
			<th><?php _e('Total', 'wp-pro-quiz'); ?></th>
			<th style="color: red;"><?php echo $gIncorrectCount.' ('.round($gIncorrectP, 2).'%)'; ?></th>
			<th style="color: green;"><?php echo $gCorrectCount.' ('.round($gCorrectP, 2).'%)'; ?></th>
			<th><?php echo $gTipCount; ?></th>
		</tfoot>
	</table>
	<p>
		<a class="button-secondary" href="admin.php?page=wpProQuiz&module=statistics&action=reset&id=<?php echo $this->quiz->getId(); ?>"><?php _e('Reset statistics', 'wp-pro-quiz'); ?></a>
	</p>
</div>

<?php 		
	}
}