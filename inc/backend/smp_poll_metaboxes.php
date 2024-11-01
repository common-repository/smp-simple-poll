<?php

/**
 * Adds a box to the main column on the Poll edit screens.
 */
function smp_poll_metaboxes()
{

	add_meta_box(
		'smp_poll_',
		__('Add Poll Options', 'smp-simple-poll'),
		'smp_poll_metabox_forms',
		'smp_poll',
		'normal',
		'high'
	);
}

add_action('add_meta_boxes', 'smp_poll_metaboxes');

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function smp_poll_metabox_forms($post)
{

	// Add an nonce field so we can check for it later.
	wp_nonce_field('smp_poll__metabox_id', 'smp_poll__metabox_id_nonce');

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$smp_poll_status = get_post_meta($post->ID, 'smp_poll_status', true);
	$smp_poll_color_options = get_post_meta($post->ID, 'smp_poll_color_options', true);
	$smp_display_poll_result = get_post_meta($post->ID, 'smp_display_poll_result', true);
	$smp_poll_color = get_post_meta($post->ID, 'smp_poll_color', true);
	$smp_poll_second_color = get_post_meta($post->ID, 'smp_poll_second_color', true);


	if (!$smp_poll_color) {
		$smp_poll_color = '#1f2e75';
	}
	if (!$smp_poll_second_color) {
		$smp_poll_second_color = '#1a2558';
	}

	$smp_poll_end_date = get_post_meta($post->ID, 'smp_end_date', true);

	$smp_today = date("Y-m-d");
	if (!empty($smp_poll_end_date)) {
		if ($smp_today > $smp_poll_end_date) {
			$smp_poll_status = 'end';
			update_post_meta($post->ID, 'smp_poll_status', 'end');
		}
	} else {
		$smp_poll_end_date = $smp_today;
	}

	// update_post_meta( $post->ID, 'smp_display_poll_result', 'private' );

	if (get_post_meta($post->ID, 'smp_poll_option', true)) {
		$smp_poll_option = get_post_meta($post->ID, 'smp_poll_option', true);
	}

	$smp_poll_option_id = get_post_meta($post->ID, 'smp_poll_option_id', true);
	$smp_poll_vote_total_count = (int) get_post_meta($post->ID, 'smp_vote_total_count', true);

	?>
	<?php if (($post->post_type == 'smp_poll') && isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') { ?>
		<div class="smp_short_code">
			<?php _e('Shortcode for this poll is : <code>[SIMPLE_POLL id="' . $post->ID . '"]</code> (Insert it anywhere in your post/page and show your poll)', 'smp-simple-poll'); ?>
		</div>
	<?php } ?>
	<form action="/">
		<table class="form-table smp_meta_table">
			<tr>
				<td><?php _e('Poll Status', 'smp-simple-poll'); ?></td>
				<td>
					<select class="widefat" id="smp_poll_status" name="smp_poll_status" value="" required>
						<option value="live" <?php if ($smp_poll_status == 'live') echo esc_attr('selected'); ?>> <?php echo esc_html__('Live', 'smp-simple-poll'); ?></option>
						<option value="end" <?php if ($smp_poll_status == 'end') echo esc_attr('selected'); ?>><?php echo esc_html__('End', 'smp-simple-poll'); ?> </option>
					</select>
				</td>

			</tr>
			<tr>
				<td><?php _e('Display Poll Result', 'smp-simple-poll'); ?></td>
				<td>
					<select class="widefat" id="smp_display_poll_result" name="smp_display_poll_result" value="" required>
						<option value="private" <?php if ($smp_display_poll_result == 'private') echo esc_attr('selected'); ?>><?php echo esc_html__('Private', 'smp-simple-poll'); ?> </option>

						<option value="public" <?php if ($smp_display_poll_result == 'public') echo esc_attr('selected'); ?>><?php echo esc_html__('Public', 'smp-simple-poll'); ?></option>

						<option value="public_after_vote" <?php if ($smp_display_poll_result == 'public_after_vote') echo esc_attr('selected'); ?>><?php echo esc_html__('Public after Vote', 'smp-simple-poll'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php _e('Poll End Date', 'smp-simple-poll'); ?></td>
				<td>
					<input type="date" id="smp_end-date" name="smp_end_date" value="<?= $smp_poll_end_date; ?>" min="<?= date("Y-m-d") ?>">
				</td>
			</tr>
			<tr>
				<td><?php _e('Poll Color', 'smp-simple-poll'); ?></td>
				<td>
					<select class="widefat" id="smp_poll_color_options" name="smp_poll_color_options" value="" required>

						<option value="gradient" <?php if ($smp_poll_color_options == 'gradient') echo esc_attr('selected'); ?>><?php echo esc_html__('Gradient', 'smp-simple-poll'); ?></option>
						<option value="solid" <?php if ($smp_poll_color_options == 'solid') echo esc_attr('selected'); ?>><?php echo esc_html__('Solid', 'smp-simple-poll'); ?></option>
					</select>
				</td>
			</tr>

			<tr>
				<td></td>
				<td id="smp_poll_colors">
					<input type="color" id="smp_poll_color" name="smp_poll_color" value="<?= $smp_poll_color; ?>">
					<input type="color" id="smp_poll_second_color " class="color2 <?php if ($smp_poll_color_options === 'solid') echo esc_attr('hidden');  ?>" name="smp_poll_second_color" value="<?= $smp_poll_second_color; ?>">
				</td>
			</tr>
		</table>

		<table class="form-table" id="smp_append_option_filed">
			<?php if (!empty($smp_poll_option)) {
					$i = 0;
					foreach ($smp_poll_option as $smp_poll_opt) :
						$pollKEYIt = (float) $smp_poll_option_id[$i];
						$smp_poll_vote_count = (int) get_post_meta($post->ID, 'smp_vote_count_' . $pollKEYIt, true);

						if (!$smp_poll_vote_count) {
							$smp_poll_vote_count = 0;
						}
						?>
					<tr class="smp_append_option_filed_tr">
						<td>
							<table class="form-table">
								<tr>
									<td><?php _e('Option Name', 'smp-simple-poll'); ?></td>
									<td>
										<input type="text" class="widefat" id="smp_poll_option" name="smp_poll_option[]" value="<?php echo esc_attr($smp_poll_opt); ?>" required />
									</td>
								</tr>
								<tr>
									<td><?php echo wp_kses_post('Get <strong>' . $smp_poll_opt . '</strong>'); ?>
									</td>
									<td><input type="number" class="widefat" id="smp_indi_vote" name="smp_indi_vote[]" value="<?php echo esc_attr($smp_poll_vote_count); ?>" disabled="" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
				<?php
							$i++;
						endforeach;
					} else { ?>
				<tr class="smp_append_option_filed_tr">
					<td>
						<table class="form-table">
							<tr>
								<td><?php echo esc_html__('Option 1', 'smp-simple-poll'); ?></td>
								<td>
									<input type="text" value="Yes" class="widefat" id="smp_poll_option" name="smp_poll_option[]" required>
									<input type="hidden" name="smp_poll_option_id[]" id="smp_poll_option_id" value="<?php echo esc_attr(rand(947984929347923, 112984929347923)); ?>" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="smp_append_option_filed_tr">
					<td>
						<table class="form-table">
							<tr>
								<td><?php echo esc_html__('Option 2', 'smp-simple-poll'); ?></td>
								<td>
									<input type="text" value="No" class="widefat" id="smp_poll_option" name="smp_poll_option[]" required>
									<input type="hidden" name="smp_poll_option_id[]" id="smp_poll_option_id" value="<?php echo esc_attr(rand(947984929347923, 112984929347923)); ?>" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
			<?php } ?>
		</table>
	</form>

<?php
}


/***************
 * 
 * Sanitize fields
 */



/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function smp_poll_save_options($post_id)
{

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if (!isset($_POST['smp_poll__metabox_id_nonce'])) {
		return;
	}

	// Verify that the nonce is valid.
	if (!wp_verify_nonce($_POST['smp_poll__metabox_id_nonce'], 'smp_poll__metabox_id')) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	// Check the user's permissions.
	if (isset($_POST['post_type']) && 'smp_poll' == $_POST['post_type']) {

		if (!current_user_can('edit_page', $post_id)) {
			return;
		}
	} else {

		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
	}


	// Sanitize user input & Update the meta field in the database.

	//Updating Poll Status
	if (isset($_POST['smp_poll_status'])) {
		$smp_poll_status =  sanitize_text_field($_POST['smp_poll_status']);
		update_post_meta($post_id, 'smp_poll_status', $smp_poll_status);
	}

	//Updating Display Reuslt
	if (isset($_POST['smp_display_poll_result'])) {
		$smp_display_poll_result =  sanitize_text_field($_POST['smp_display_poll_result']);
		update_post_meta($post_id, 'smp_display_poll_result', $smp_display_poll_result);
	}

	//Updating Poll Ended date
	if (isset($_POST['smp_end_date'])) {
		$smp_end_date =  sanitize_text_field($_POST['smp_end_date']);
		update_post_meta($post_id, 'smp_end_date', $smp_end_date);
	}


	//Update Poll Options Name
	if (isset($_POST['smp_poll_option'])) {
		$smp_poll_option = array();
		$smp_poll_option = array_map('sanitize_text_field', $_POST['smp_poll_option']);
		update_post_meta($post_id, 'smp_poll_option', $smp_poll_option);
	} else {
		update_post_meta($post_id, 'smp_poll_option', array());
		update_post_meta($post_id, 'smp_poll_option_id', array());
	}

	//Update Poll Options Id
	if (isset($_POST['smp_poll_option_id'])) {
		$smp_poll_option_id = array_map('sanitize_text_field', $_POST['smp_poll_option_id']);
		update_post_meta($post_id, 'smp_poll_option_id', $smp_poll_option_id);
	}

	//Updating Poll Status
	if (isset($_POST['smp_poll_color_options'])) {
		$smp_poll_color_options =  sanitize_text_field($_POST['smp_poll_color_options']);
		update_post_meta($post_id, 'smp_poll_color_options', $smp_poll_color_options);
	}

	//Update UiUx color
	if (isset($_POST['smp_poll_color'])) {
		$smp_poll_color =  sanitize_text_field($_POST['smp_poll_color']);
		update_post_meta($post_id, 'smp_poll_color', $smp_poll_color);
	}

	//Update UiUx color
	if (isset($_POST['smp_poll_second_color'])) {
		$smp_poll_second_color =  sanitize_text_field($_POST['smp_poll_second_color']);
		update_post_meta($post_id, 'smp_poll_second_color', $smp_poll_second_color);
	}
}
add_action('save_post', 'smp_poll_save_options');
