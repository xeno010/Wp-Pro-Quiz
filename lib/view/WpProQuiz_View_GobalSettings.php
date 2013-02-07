<?php
class WpProQuiz_View_GobalSettings extends WpProQuiz_View_View {
	
	public function show() {
		
		if($this->isRaw) {
			$rawSystem = __('to activate', 'wp-pro-quiz');
		} else {
			$rawSystem = __('not to activate', 'wp-pro-quiz');
		}

?>		
<div class="wrap wpProQuiz_globalSettings">
	<h2 style="margin-bottom: 10px;"><?php _e('Global settings', 'wp-pro-quiz'); ?></h2>
	<div class="updated" id="problemInfo" style="display: none;">
		<h3><?php _e('Please note', 'wp-pro-quiz'); ?></h3>
		<p>
			<?php _e('These settings should only be set in cases of problems with Wp-Pro-Quiz.', 'wp-pro-quiz'); ?>
		</p>
	</div>
	<a class="button-secondary" href="admin.php?page=wpProQuiz"><?php _e('back to overview', 'wp-pro-quiz'); ?></a>
	
	<div style="padding: 10px 0px;">
		<a class="button-primary wpProQuiz_tab" id="globalTab" href="#"><?php _e('Global settings', 'wp-pro-quiz'); ?></a>
		<a class="button-secondary wpProQuiz_tab" href="#"><?php _e('Settings in case of problems', 'wp-pro-quiz'); ?></a>
	</div>
	
	<form method="post">
		<div id="poststuff">
			<div class="postbox" id="globalContent">
				<h3 class="hndle"><?php _e('Global settings', 'wp-pro-quiz'); ?></h3>
				<div class="wrap">
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<?php _e('Leaderboard time format', 'wp-pro-quiz'); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php _e('Leaderboard time format', 'wp-pro-quiz'); ?></span>
										</legend>
										<label>
											<input type="radio" name="toplist_date_format" value="d.m.Y H:i" <?php $this->checked($this->toplistDataFormat, 'd.m.Y H:i'); ?>> 06.11.2010 12:50
										</label> <br>
										<label>
											<input type="radio" name="toplist_date_format" value="Y/m/d g:i A" <?php $this->checked($this->toplistDataFormat, 'Y/m/d g:i A'); ?>> 2010/11/06 12:50 AM
										</label> <br>
										<label>
											<input type="radio" name="toplist_date_format" value="Y/m/d \a\t g:i A" <?php $this->checked($this->toplistDataFormat, 'Y/m/d \a\t g:i A'); ?>> 2010/11/06 at 12:50 AM
										</label> <br>
										<label>
											<input type="radio" name="toplist_date_format" value="Y/m/d \a\t g:ia" <?php $this->checked($this->toplistDataFormat, 'Y/m/d \a\t g:ia'); ?>> 2010/11/06 at 12:50am
										</label> <br>
										<label>
											<input type="radio" name="toplist_date_format" value="F j, Y g:i a" <?php $this->checked($this->toplistDataFormat, 'F j, Y g:i a'); ?>> November 6, 2010 12:50 am
										</label> <br>
										<label>
											<input type="radio" name="toplist_date_format" value="M j, Y @ G:i" <?php $this->checked($this->toplistDataFormat, 'M j, Y @ G:i'); ?>> Nov 6, 2010 @ 0:50
										</label> <br>
										<label>
											<input type="radio" name="toplist_date_format" value="custom" <?php echo in_array($this->toplistDataFormat, array('d.m.Y H:i', 'Y/m/d g:i A', 'Y/m/d \a\t g:i A', 'Y/m/d \a\t g:ia', 'F j, Y g:i a', 'M j, Y @ G:i')) ? '' : 'checked="checked"'; ?> >
											<?php _e('Custom', 'wp-pro-quiz'); ?>:
											<input class="medium-text" name="toplist_date_format_custom" style="width: 100px;" value="<?php echo $this->toplistDataFormat; ?>">
										</label>
										<p>
											<a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank"><?php _e('Documentation on date and time formatting', 'wp-pro-quiz'); ?></a>
										</p>
									</fieldset>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="postbox" id="problemContent" style="display: none;">
				<h3 class="hndle"><?php _e('Settings in case of problems', 'wp-pro-quiz'); ?></h3>
				<div class="wrap">
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<?php _e('Automatically add [raw] shortcode', 'wp-pro-quiz'); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php _e('Automatically add [raw] shortcode', 'wp-pro-quiz'); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="addRawShortcode" <?php echo $this->settings->isAddRawShortcode() ? 'checked="checked"' : '' ?> >
											<?php _e('Activate', 'wp-pro-quiz'); ?> <span class="description">( <?php printf(__('It is recommended %s this option on your system.', 'wp-pro-quiz'), '<span style=" font-weight: bold;">'.$rawSystem.'</span>'); ?> )</span>
										</label>
										<p class="description">
											<?php _e('If this option is activated, a [raw] shortcode is automatically set around WpProQuiz shortcode ( [WpProQuiz X] ) into [raw] [WpProQuiz X] [/raw]', 'wp-pro-quiz'); ?>
										</p>
										<p class="description">
											<?php _e('Own themes changes internal  order of filters, what causes the problems. With additional shortcode [raw] this is prevented.', 'wp-pro-quiz'); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php _e('Do not load the Javascript-files in the footer', 'wp-pro-quiz'); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php _e('Do not load the Javascript-files in the footer', 'wp-pro-quiz'); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="jsLoadInHead" <?php echo $this->settings->isJsLoadInHead() ? 'checked="checked"' : '' ?> >
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('Generally all WpProQuiz-Javascript files are loaded in the footer and only when they are really needed.', 'wp-pro-quiz'); ?>
										</p>
										<p class="description">
											<?php _e('In very old Wordpress themes this can lead to problems.', 'wp-pro-quiz'); ?>
										</p>
										<p class="description">
											<?php _e('If you activate this option, all WpProQuiz-Javascript files are loaded in the header even if they are not needed.', 'wp-pro-quiz'); ?>
										</p>
										<p class="description">
											<?php printf(__('Anyone who wants to learn more about this topic should read through the following websites %s and %s.', 'wp-pro-quiz'),
												'<a href="http://codex.wordpress.org/Theme_Development#Footer_.28footer.php.29" target="_blank">Theme Development</a>', 
												'<a href="http://codex.wordpress.org/Function_Reference/wp_footer" target="_blank">Function Reference/wp footer</a>'); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php _e('Touch Library', 'wp-pro-quiz'); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php _e('Touch Library', 'wp-pro-quiz'); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="touchLibraryDeactivate" <?php echo $this->settings->isTouchLibraryDeactivate() ? 'checked="checked"' : '' ?> >
											<?php _e('Deactivate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('In Version 0.13 a new Touch Library was added for mobile devices.', 'wp-pro-quiz'); ?>
										</p>
										<p class="description">
											<?php _e('If you have any problems with the Touch Library, please deactivate it.', 'wp-pro-quiz'); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php _e('jQuery support cors', 'wp-pro-quiz'); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php _e('jQuery support cors', 'wp-pro-quiz'); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="corsActivated" <?php echo $this->settings->isCorsActivated() ? 'checked="checked"' : '' ?> >
											<?php _e('Activate', 'wp-pro-quiz'); ?>
										</label>
										<p class="description">
											<?php _e('Is required only in rare cases.', 'wp-pro-quiz'); ?>
										</p>
										<p class="description">
											<?php _e('If you have problems with the front ajax, please activate it.', 'wp-pro-quiz'); ?>
										</p>
										<p class="description">
											<?php _e('e.g. Domain with special characters in combination with IE', 'wp-pro-quiz'); ?>
										</p>
									</fieldset>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<input type="submit" name="submit" class="button-primary" id="wpProQuiz_save" value="<?php _e('Save', 'wp-pro-quiz'); ?>">
		</div>
	</form>
</div>
		
<?php		
	}
}