<?php
class WpProQuiz_View_GobalSettings extends WpProQuiz_View_View {
	
	public function show() {
		
		if($this->isRaw) {
			$rawSystem = __('to activate', 'wp-pro-quiz');
		} else {
			$rawSystem = __('not to activate', 'wp-pro-quiz');
		}

?>		
<div class="wrap">
	<h2 style="margin-bottom: 10px;"><?php _e('Settings in case of problems', 'wp-pro-quiz'); ?></h2>
	<div class="updated">
		<h3><?php _e('Please note', 'wp-pro-quiz'); ?></h3>
		<p>
			<?php _e('These settings should only be set in cases of problems with Wp-Pro-Quiz.', 'wp-pro-quiz'); ?>
		</p>
	</div>
	<a class="button-secondary" href="admin.php?page=wpProQuiz"><?php _e('back to overview', 'wp-pro-quiz'); ?></a>
	<form method="post">
		<div id="poststuff">
			<div class="postbox">
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