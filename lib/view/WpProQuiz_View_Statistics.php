<?php
class WpProQuiz_View_Statistics extends WpProQuiz_View_View {
	
	public function show() {
?>

<style>
.wpProQuiz_blueBox {
	padding: 20px; 
	background-color: rgb(223, 238, 255); 
	border: 1px dotted;
	margin-top: 10px;
}
</style>

<div class="wrap wpProQuiz_statistics">
	<h2><?php printf(__('Quiz: %s - Statistics', 'wp-pro-quiz'), $this->quiz->getName()); ?></h2>
	<p><a class="button-secondary" href="admin.php?page=wpProQuiz"><?php _e('back to overview', 'wp-pro-quiz'); ?></a></p>
	<?php if(!$this->quiz->isStatisticsOn()) { ?>
	<p style="padding: 30px; background: #F7E4E4; border: 1px dotted; width: 300px;">
		<span style="font-weight: bold; padding-right: 10px;"><?php _e('Stats not enabled', 'wp-pro-quiz'); ?></span>
		<a class="button-secondary" href="admin.php?page=wpProQuiz&action=edit&id=<?php echo $this->quiz->getId(); ?>"><?php _e('Activate statistics', 'wp-pro-quiz'); ?></a>
	</p>
	<?php return; } ?>
	
	<div style="padding: 10px 0px;">
		<a class="button-primary wpProQuiz_tab" id="wpProQuiz_typeAnonymeUser" href="#"><?php _e('Anonymous users', 'wp-pro-quiz'); ?></a>
		<a class="button-secondary wpProQuiz_tab" id="wpProQuiz_typeRegisteredUser" href="#"><?php _e('Registered users', 'wp-pro-quiz'); ?></a>
		<a class="button-secondary wpProQuiz_tab" id="wpProQuiz_typeOverview" href="#"><?php _e('Overview', 'wp-pro-quiz'); ?></a>
	</div>
	
	<div class="wpProQuiz_blueBox" id="wpProQuiz_userBox" style="margin-bottom: 20px; display: none;">
		<span>
			<?php _e('Please select user name:', 'wp-pro-quiz'); ?>
			<select name="userSelect" id="userSelect">
				<?php foreach($this->users as $user) { 
					echo '<option value="', $user->ID, '">', $user->user_login, ' (', $user->display_name, ')</option>';
				} ?>
			</select>
		</span>
	</div>
	
	<div id="wpProQuiz_loadData" class="wpProQuiz_blueBox" style="background-color: #F8F5A8;">
		<img alt="load" src="data:image/gif;base64,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">
		<?php _e('Loading', 'wp-pro-quiz'); ?>
	</div>
		
	<div id="wpProQuiz_statistics_content" style="display: none;">
		<table class="wp-list-table widefat wpProQuiz_statistics_table">
			<thead>
				<tr>
					<th scope="col" style="width: 50px;"></th>
					<th scope="col"><?php _e('Question', 'wp-pro-quiz'); ?></th>
					<th scope="col" style="width: 100px;"><?php _e('Points', 'wp-pro-quiz'); ?></th>
					<th scope="col" style="width: 100px;"><?php _e('Correct', 'wp-pro-quiz'); ?></th>
					<th scope="col" style="width: 100px;"><?php _e('Incorrect', 'wp-pro-quiz'); ?></th>
					<th scope="col" style="width: 100px;"><?php _e('Hints used', 'wp-pro-quiz'); ?></th>
					<th scope="col" style="width: 100px;"><?php _e('Points scored', 'wp-pro-quiz'); ?></th>
					<th scope="col" style="width: 60px;"><?php _e('Results', 'wp-pro-quiz'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$index = 1;
			$gPoints = 0;
			foreach($this->question as $question) {
				$gPoints += $question->getPoints();
			?>
				<tr id="wpProQuiz_tr_<?php echo $question->getId(); ?>">
					<th><?php echo $index++; ?></th>
					<th><?php echo $question->getTitle(); ?></th>
					<th class="wpProQuiz_points"><?php echo $question->getPoints(); ?></th>
					<th class="wpProQuiz_cCorrect" style="color: green;"></th>
					<th class="wpProQuiz_cIncorrect" style="color: red;"></th>
					<th class="wpProQuiz_cTip"></th>
					<th class="wpProQuiz_cPoints"></th>
					<th></th>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th><?php _e('Total', 'wp-pro-quiz'); ?></th>
					<th class="wpProQuiz_points"><?php echo $gPoints; ?></th>
					<th class="wpProQuiz_cCorrect" style="color: green;"></th>
					<th class="wpProQuiz_cIncorrect" style="color: red;"></th>
					<th class="wpProQuiz_cTip"></th>
					<th class="wpProQuiz_cPoints"></th>
					<th class="wpProQuiz_cResult" style="font-weight: bold;"></th>
				</tr>
			</tfoot>
		</table>
		
		<div style="margin-top: 10px;">
			<div style="float: left;">
				<?php if(current_user_can('wpProQuiz_reset_statistics')) { ?>
				<a class="button-secondary" href="#" id="wpProQuiz_reset"><?php _e('Reset statistics', 'wp-pro-quiz'); ?></a>
				<?php } ?>
				<a class="button-secondary wpProQuiz_update" href="#"><?php _e('Refresh', 'wp-pro-quiz'); ?></a>
			</div>
			<div style="float: right;">
				<?php if(current_user_can('wpProQuiz_reset_statistics')) { ?>
				<a class="button-secondary wpProQuiz_resetComplete" href="#"><?php _e('Reset entire statistic', 'wp-pro-quiz'); ?></a>
				<?php } ?>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	
	<div id="wpProQuiz_statistics_overview">
			<input type="hidden" value="<?php echo $gPoints; ?>" name="gPoints" id="wpProQuiz_gPoints">
			<div id="poststuff">
				<div class="postbox">
					<h3 class="hndle"><?php _e('Filter', 'wp-pro-quiz'); ?></h3>
					<div class="inside">
						<ul>
							<li>
								<label>
									<?php _e('Show only users, who solved the quiz:', 'wp-pro-quiz'); ?>
									<input type="checkbox" value="1" id="wpProQuiz_onlyCompleted">
								</label>
							</li>
							<li>
								<label>
									<?php _e('How many entries should be shown on one page:', 'wp-pro-quiz'); ?>
									<select id="wpProQuiz_pageLimit">
										<option>1</option>
										<option>10</option>
										<option>50</option>
										<option selected="selected">100</option>
										<option>500</option>
										<option>1000</option>
									</select>
								</label>
							</li>
						</ul>
					</div>
				</div>
			</div>
			
			<table class="wp-list-table widefat">
				<thead>
					<tr>
						<th scope="col"><?php _e('User', 'wp-pro-quiz'); ?></th>
						<th scope="col" style="width: 100px;"><?php _e('Points', 'wp-pro-quiz'); ?></th>
						<th scope="col" style="width: 100px;"><?php _e('Correct', 'wp-pro-quiz'); ?></th>
						<th scope="col" style="width: 100px;"><?php _e('Incorrect', 'wp-pro-quiz'); ?></th>
						<th scope="col" style="width: 100px;"><?php _e('Hints used', 'wp-pro-quiz'); ?></th>
						<th scope="col" style="width: 60px;"><?php _e('Results', 'wp-pro-quiz'); ?></th>
					</tr>
				</thead>
				<tbody id="wpProQuiz_statistics_overview_data">
					<tr>
						<th><a href="#"></a></th>
						<th class="wpProQuiz_points"></th>
						<th class="wpProQuiz_cCorrect" style="color: green;"></th>
						<th class="wpProQuiz_cIncorrect" style="color: red;"></th>
						<th class="wpProQuiz_cTip"></th>
						<th class="wpProQuiz_cResult" style="font-weight: bold;"></th>
					</tr>
				</tbody>
			</table>
		
			<div style="margin-top: 10px;">
				<div style="float: left;">
					<input style="font-weight: bold;" class="button-secondary" value="&lt;" type="button" id="wpProQuiz_pageLeft">
					<select id="wpProQuiz_currentPage"><option value="1">1</option></select>
					<input style="font-weight: bold;" class="button-secondary"value="&gt;" type="button" id="wpProQuiz_pageRight">
				</div>
				<div style="float: right;">
					<a class="button-secondary wpProQuiz_update" href="#"><?php _e('Refresh', 'wp-pro-quiz'); ?></a>
					<?php if(current_user_can('wpProQuiz_reset_statistics')) { ?>
					<a class="button-secondary wpProQuiz_resetComplete" href="#"><?php _e('Reset entire statistic', 'wp-pro-quiz'); ?></a>
					<?php } ?>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
</div>

<?php 		
	}
}