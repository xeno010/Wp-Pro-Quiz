<?php

class WpProQuiz_View_QuizOverall extends WpProQuiz_View_View {
	
	public function show() {
?>
<style>
.wpProQuiz_exportList ul {
	list-style: none;
	margin: 0;
	padding: 0;
}
.wpProQuiz_exportList li {
	float: left;
	padding: 3px;
	border: 1px solid #B3B3B3;
	margin-right: 5px;
	background-color: #F3F3F3;
}
.wpProQuiz_exportList, .wpProQuiz_importList {
	padding: 20px; 
	background-color: rgb(223, 238, 255); 
	border: 1px dotted;
	margin-top: 10px;
	display: none;
}
.wpProQuiz_exportCheck {
	display: none;
}
</style>
<div class="wrap wpProQuiz_quizOverall">
	<h2><?php _e('Quiz overview', 'wp-pro-quiz'); ?></h2>
	<div class="updated">
		<h3><?php _e('In case of problems', 'wp-pro-quiz'); ?></h3>
		<p>
			<?php _e('If quiz doesn\'t work in front-end, please try following:', 'wp-pro-quiz'); ?>
		</p>
		<p>
			[raw][WpProQuiz X][/raw]
		</p>
		<p>
			<?php _e('Own themes changes internal  order of filters, what causes the problems. With additional shortcode [raw] this is prevented.', 'wp-pro-quiz'); ?>
		</p>
	</div>
	<table class="wp-list-table widefat">
		<thead>
			<tr>
				<th scope="col" class="wpProQuiz_exportCheck"><input type="checkbox" name="exportItemsAll" value="0"></th>
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
				<th class="wpProQuiz_exportCheck"><input type="checkbox" name="exportItems" value="<?php echo $quiz->getId(); ?>"></th>
				<th><?php echo $quiz->getId(); ?></th>
				<th>[WpProQuiz <?php echo $quiz->getId(); ?>]</th>
				<th class="wpProQuiz_quizName"><?php echo $quiz->getName(); ?></th>
				<th>
					<a class="button-secondary" href="admin.php?page=wpProQuiz&module=question&quiz_id=<?php echo $quiz->getId(); ?>"><?php _e('Questions', 'wp-pro-quiz'); ?></a>
					<a class="button-secondary" href="admin.php?page=wpProQuiz&action=edit&id=<?php echo $quiz->getId(); ?>"><?php _e('Edit', 'wp-pro-quiz'); ?></a> 
					<a class="button-secondary wpProQuiz_delete" href="admin.php?page=wpProQuiz&action=delete&id=<?php echo $quiz->getId(); ?>"><?php _e('Delete', 'wp-pro-quiz'); ?></a>
					<a class="button-secondary wpProQuiz_prview" href="admin.php?page=wpProQuiz&module=preview&id=<?php echo $quiz->getId(); ?>"><?php _e('Preview', 'wp-pro-quiz'); ?></a>
					<a class="button-secondary" href="admin.php?page=wpProQuiz&module=statistics&id=<?php echo $quiz->getId(); ?>"><?php _e('Statistics', 'wp-pro-quiz'); ?></a>
				</th>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<p>
		<a class="button-secondary" href="admin.php?page=wpProQuiz&action=add"><?php echo __('Add quiz', 'wp-pro-quiz'); ?></a>
		<a class="button-secondary wpProQuiz_import" href="#"><?php echo __('Import', 'wp-pro-quiz'); ?></a>
		<a class="button-secondary wpProQuiz_export" href="#"><?php echo __('Export', 'wp-pro-quiz'); ?></a>
	</p>
	<div class="wpProQuiz_exportList">
		<form action="admin.php?page=wpProQuiz&module=importExport&action=export&noheader=true" method="POST">
			<h3 style="margin-top: 0;"><?php _e('Export', 'wp-pro-quiz'); ?></h3>
			<p><?php echo __('Choose the respective question, which you would like to export and press on "Start export"', 'wp-pro-quiz'); ?></p>
			<ul></ul>
			<div style="clear: both; margin-bottom: 15px;"></div>
			<div id="exportHidden"></div>
			<input class="button-primary" name="exportStart" id="exportStart" value="<?php echo __('Start export', 'wp-pro-quiz'); ?>" type="submit">
		</form>
	</div>
	<div class="wpProQuiz_importList">
		<form action="admin.php?page=wpProQuiz&module=importExport&action=import" method="POST" enctype="multipart/form-data">
			<h3 style="margin-top: 0;"><?php _e('Import', 'wp-pro-quiz'); ?></h3>
			<p><?php echo __('Import only *.wpq files from known and trusted sources.', 'wp-pro-quiz'); ?></p>
			<div style="margin-bottom: 10px">
			<?php 
				$maxUpload = (int)(ini_get('upload_max_filesize'));
				$maxPost = (int)(ini_get('post_max_size'));
				$memoryLimit = (int)(ini_get('memory_limit'));
				$uploadMB = min($maxUpload, $maxPost, $memoryLimit);
			?>
				<input type="file" name="import" accept="*application/octet-stream, .wpq" required="required"> <?php printf(__('Maximal %d MiB', 'wp-pro-quiz'), $uploadMB); ?>
			</div>
			<input class="button-primary" name="exportStart" id="exportStart" value="<?php echo __('Start import', 'wp-pro-quiz'); ?>" type="submit">
		</form>
	</div>
</div>
		
		<?php 
	}
}