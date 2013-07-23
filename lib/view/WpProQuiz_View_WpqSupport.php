<?php
class WpProQuiz_View_WpqSupport extends WpProQuiz_View_View {
	
	public function show() {
?>

<div class="wrap">
	<h2><?php _e('Support WP-Pro-Quiz', 'wp-pro-quiz'); ?></h2>

	<h3><?php _e('Donate', 'wp-pro-quiz'); ?></h3>
	
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="N9B7S4FT8CE2N">
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
	</form>
	
	<p>
		<?php _e('WP-Pro-Quiz is small but nice free quiz plugin for WordPress.', 'wp-pro-quiz'); ?> <br>
		<?php _e('I try to implement all wishes as fast as possible and help with problems.', 'wp-pro-quiz'); ?> <br> 
		<?php _e('Your donations can help to ensure that the project continues to remain free.', 'wp-pro-quiz'); ?>
	</p>
	
	<h3 style="margin-top: 40px;"><?php _e('Translate WP-Pro-Quiz', 'wp-pro-quiz'); ?></h3>
	
	<h4><?php _e('You need:', 'wp-pro-quiz'); ?></h4>
	<ul style="list-style: disc; padding-left: 10px; list-style-position: inside;">
		<li><a href="http://www.poedit.net/" target="_blank">PoEdit</a></li>
		<li><a href="http://plugins.svn.wordpress.org/wp-pro-quiz/trunk/languages/wp-pro-quiz.pot" target="_blank"><?php _e('Latest POT file', 'wp-pro-quiz'); ?></a></li>
	</ul>
	
	<h4>PoEdit:</h4>
	<ul style="list-style: disc; padding-left: 10px; list-style-position: inside;">
		<li><?php _e('Open PoEdit', 'wp-pro-quiz'); ?></li>
		<li><?php _e('File - New catalogue from POT file...', 'wp-pro-quiz'); ?></li>
		<li><?php _e('Choose wp-pro-quiz.pot', 'wp-pro-quiz'); ?></li>
		<li><?php _e('Set "Translation properties"', 'wp-pro-quiz'); ?></li>
		<li><?php _e('Save PO file - with the name "wp-pro-qioz-de_DE.po"', 'wp-pro-quiz'); ?>
			<ul style="list-style: disc; padding-left: 10px; list-style-position: inside;">
				<li><?php _e('replace de_DE with your countries short code (e.g. en_US, nl_NL...)', 'wp-pro-quiz'); ?></li>
			</ul>
		</li>
		<li><?php _e('Translate', 'wp-pro-quiz'); ?></li>
		<li><?php _e('Save', 'wp-pro-quiz'); ?></li>
		<li><?php _e('Upload generated *.mo file to your server, to /wp-content/plugins/wp-pro-quiz/languages', 'wp-pro-quiz'); ?></li>
		<li><?php _e('Finished', 'wp-pro-quiz'); ?></li>
	</ul>
	
	<h4><?php _e('Please note', 'wp-pro-quiz'); ?>:</h4>
	<p><?php _e('You can translate WP-Pro-Quiz from existing to existing language (e.g. english to english) e.g. to rename buttons.', 'wp-pro-quiz'); ?></p>
	
</div>

<?php
	}
}