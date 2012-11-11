<?php
add_action('admin_init', 'editor_admin_init');
add_action('admin_head', 'editor_admin_head');
 
function editor_admin_init() {
  wp_enqueue_script('word-count');
  wp_enqueue_script('post');
  wp_enqueue_script('editor');
  wp_enqueue_script('media-upload');
}
 
function editor_admin_head() {
  wp_tiny_mce();
}

var_dump($_POST);

?>

<div style="padding-right: 20px;">
	<form method="post">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th>
						<label for="quiz_name">
							Name
						</label>
					</th>
					<td>
						<input name="quiz_name" type="text" class="regular-text">
					</td>
				</tr>
				<tr valign="top">
					<th>
						<label for="quiz_text">
							Text
						</label>
					</th>
					<td>ja
						<?php
						wp_editor("", "quiz_text"); 
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<p>
			<input type="submit" class="button-primary">
		</p>
	</form>
</div>