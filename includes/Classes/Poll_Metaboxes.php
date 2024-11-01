<?php

/**
 * @package SmppSimplePoll
 */

namespace Includes\Classes;

class Poll_Metaboxes
{
    public function register()
    {
        add_action('add_meta_boxes', array($this, 'smpp_metaboxes'));
        add_action('save_post', array($this, 'smpp_save_options'));
    }


    public function smpp_metaboxes()
    {
        add_meta_box(
            'smpp_id_',
            __('Poll Metabox', 'smp-simple-poll'),
            array($this, 'smpp_metabox_forms'),
            'smpp_poll',
            'normal',
            'high'
        );
    }

    /**
     * Prints the box content.
     * 
     * @param WP_Post $post The object for the current post/page.
     */
    public function smpp_metabox_forms($post)
    {

        // Add an nonce field so we can check for it later.
        wp_nonce_field('smpp__metabox_id', 'smpp__metabox_id_nonce');

        /*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
        $smpp_status = get_post_meta($post->ID, 'smpp_status', true);
        $smpp_color_options = get_post_meta($post->ID, 'smpp_color_options', true);
        $smpp_display_poll_result = get_post_meta($post->ID, 'smpp_display_poll_result', true);
        $smpp_color = get_post_meta($post->ID, 'smpp_color', true);
        $smpp_second_color = get_post_meta($post->ID, 'smpp_second_color', true);


        if (!$smpp_color) {
            $smpp_color = '#1f2e75';
        }
        if (!$smpp_second_color) {
            $smpp_second_color = '#1a2558';
        }

        $smpp_end_date = get_post_meta($post->ID, 'smpp_end_date', true);

        $smpp_today = date("Y-m-d");
        if (!empty($smpp_end_date)) {
            if ($smpp_today > $smpp_end_date) {
                $smpp_status = 'end';
                update_post_meta($post->ID, 'smpp_status', 'end');
            }
        } else {
            $smpp_end_date = $smpp_today;
        }

        // update_post_meta( $post->ID, 'smpp_display_poll_result', 'private' );

        if (get_post_meta($post->ID, 'smpp_option', true)) {
            $smpp_option = get_post_meta($post->ID, 'smpp_option', true);
        }

        $smpp_option_id = get_post_meta($post->ID, 'smpp_option_id', true);
        $smpp_vote_total_count = (int) get_post_meta($post->ID, 'smpp_vote_total_count', true);

        ?>
        <?php if (($post->post_type == 'smpp_poll') && isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') { ?>
            <div class="smpp_short_code">
                <?php _e('Shortcode for this poll is : <code>[SIMPLE_POLL id="' . $post->ID . '"]</code> (Insert it anywhere in your post/page and show your poll)', 'smp-simple-poll'); ?>
            </div>
        <?php } ?>
        <form action="/">
            <table class="form-table smpp_meta_table">
                <tr>
                    <td><?php _e('Poll Status', 'smp-simple-poll'); ?></td>
                    <td>
                        <select class="widefat" id="smpp_status" name="smpp_status" value="" required>
                            <option value="live" <?php if ($smpp_status == 'live') echo esc_attr('selected'); ?>> <?php echo esc_html__('Live', 'smp-simple-poll'); ?></option>
                            <option value="end" <?php if ($smpp_status == 'end') echo esc_attr('selected'); ?>><?php echo esc_html__('End', 'smp-simple-poll'); ?> </option>
                        </select>
                    </td>

                </tr>
                <tr>
                    <td><?php _e('Display Poll Result', 'smp-simple-poll'); ?></td>
                    <td>
                        <select class="widefat" id="smpp_display_poll_result" name="smpp_display_poll_result" value="" required>

                            <option value="public_after_vote" <?php if ($smpp_display_poll_result == 'public_after_vote') echo esc_attr('selected'); ?>><?php echo esc_html__('Public after Vote', 'smp-simple-poll'); ?></option>

                            <option value="private" <?php if ($smpp_display_poll_result == 'private') echo esc_attr('selected'); ?>><?php echo esc_html__('Private', 'smp-simple-poll'); ?> </option>

                            <option value="public" <?php if ($smpp_display_poll_result == 'public') echo esc_attr('selected'); ?>><?php echo esc_html__('Public', 'smp-simple-poll'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php _e('Poll End Date', 'smp-simple-poll'); ?></td>
                    <td>
                        <input type="date" id="smpp_end-date" name="smpp_end_date" value="<?= $smpp_end_date; ?>" min="<?= date("Y-m-d") ?>">
                    </td>
                </tr>
                <tr>
                    <td><?php _e('Poll Color', 'smp-simple-poll'); ?></td>
                    <td>
                        <select class="widefat" id="smpp_color_options" name="smpp_color_options" value="" required>

                            <option value="gradient" <?php if ($smpp_color_options == 'gradient') echo esc_attr('selected'); ?>><?php echo esc_html__('Gradient', 'smp-simple-poll'); ?></option>
                            <option value="solid" <?php if ($smpp_color_options == 'solid') echo esc_attr('selected'); ?>><?php echo esc_html__('Solid', 'smp-simple-poll'); ?></option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td id="smpp_colors">
                        <input type="color" id="smpp_color" name="smpp_color" value="<?= $smpp_color; ?>">
                        <input type="color" id="smpp_second_color " class="color2 <?php if ($smpp_color_options === 'solid') echo esc_attr('hidden');  ?>" name="smpp_second_color" value="<?= $smpp_second_color; ?>">
                    </td>
                </tr>
            </table>

            <table class="form-table" id="smpp_append_option_filed">
                <?php $current_total_vote = 0; ?>
                <?php if (!empty($smpp_option)) {

                            $i = 0;
                            foreach ($smpp_option as $smpp_opt) :
                                $pollKEYIt = (float) $smpp_option_id[$i];
                                $smpp_vote_count = (int) get_post_meta($post->ID, 'smpp_vote_count_' . $pollKEYIt, true);

                                if (!$smpp_vote_count) {
                                    $smpp_vote_count = 0;
                                }
                                $current_total_vote = $current_total_vote + $smpp_vote_count;
                                ?>
                        <tr class="smpp_append_option_filed_tr">
                            <td>
                                <table class="form-table">
                                    <tr>
                                        <td><?php _e('Option Name ' . $i + 1 . '', 'smp-simple-poll'); ?></td>
                                        <td>
                                            <input type="text" class="widefat" id="smpp_option" name="smpp_option[]" value="<?php echo esc_attr($smpp_opt); ?>" required />
                                            <input type="hidden" name="smpp_option_id[]" id="smpp_option_id" value="<?php echo esc_attr($smpp_option_id[$i]); ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo wp_kses_post('Vote for <strong>' . $smpp_opt . '</strong>'); ?>
                                        </td>
                                        <td><input type="number" class="widefat" id="smpp_indi_vote" name="smpp_indi_vote[]" value="<?php echo esc_attr($smpp_vote_count); ?>" disabled="" />
                                        </td>

                                    </tr>
                                </table>
                            </td>
                            <td><button class="remove-option">—</button></td>
                        </tr>
                    <?php
                                    $i++;
                                endforeach;
                                update_post_meta($post->ID, 'smpp_vote_total_count', $current_total_vote);
                            } else { ?>
                    <tr class="smpp_append_option_filed_tr">
                        <td>
                            <table class="form-table">
                                <tr>
                                    <td><?php echo esc_html__('Option Name', 'smp-simple-poll'); ?></td>
                                    <td>
                                        <input type="text" value="Yes" class="widefat" id="smpp_option" name="smpp_option[]" required>
                                        <input type="hidden" name="smpp_option_id[]" id="smpp_option_id" value="<?php echo esc_attr(rand(947984929347923, 112984929347923)); ?>" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="smpp_append_option_filed_tr">
                        <td>
                            <table class="form-table">
                                <tr>
                                    <td><?php echo esc_html__('Option Name', 'smp-simple-poll'); ?></td>
                                    <td>
                                        <input type="text" value="No" class="widefat" id="smpp_option" name="smpp_option[]" required>
                                        <input type="hidden" name="smpp_option_id[]" id="smpp_option_id" value="<?php echo esc_attr(rand(947984929347923, 112984929347923)); ?>" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                <?php } ?>
            </table>

            <table>
                <tr>
                    <td><button class="smpp_add_option_btn button"><?php echo esc_html__('Add Option'); ?></button></td>
                </tr>
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
    public function smpp_save_options($post_id)
    {

        /*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

        // Check if our nonce is set.
        if (!isset($_POST['smpp__metabox_id_nonce'])) {
            return;
        }

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST['smpp__metabox_id_nonce'], 'smpp__metabox_id')) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions.
        if (isset($_POST['post_type']) && 'smpp_poll' == $_POST['post_type']) {

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
        if (isset($_POST['smpp_status'])) {
            $smpp_status =  sanitize_text_field($_POST['smpp_status']);
            update_post_meta($post_id, 'smpp_status', $smpp_status);
        }

        //Updating Display Reuslt
        if (isset($_POST['smpp_display_poll_result'])) {
            $smpp_display_poll_result =  sanitize_text_field($_POST['smpp_display_poll_result']);
            update_post_meta($post_id, 'smpp_display_poll_result', $smpp_display_poll_result);
        }

        //Updating Poll Ended date
        if (isset($_POST['smpp_end_date'])) {
            $smpp_end_date =  sanitize_text_field($_POST['smpp_end_date']);
            update_post_meta($post_id, 'smpp_end_date', $smpp_end_date);
        }


        //Update Poll Options Name
        if (isset($_POST['smpp_option'])) {
            $smpp_option = array();
            $smpp_option = array_map('sanitize_text_field', $_POST['smpp_option']);
            update_post_meta($post_id, 'smpp_option', $smpp_option);
        } else {
            update_post_meta($post_id, 'smpp_option', array());
            update_post_meta($post_id, 'smpp_option_id', array());
        }


        //Update Poll Options Id
        if (isset($_POST['smpp_option_id'])) {
            $smpp_option_id = array_map('sanitize_text_field', $_POST['smpp_option_id']);
            update_post_meta($post_id, 'smpp_option_id', $smpp_option_id);
        }


        //Updating Poll Status
        if (isset($_POST['smpp_color_options'])) {
            $smpp_color_options =  sanitize_text_field($_POST['smpp_color_options']);
            update_post_meta($post_id, 'smpp_color_options', $smpp_color_options);
        }

        //Update UiUx color
        if (isset($_POST['smpp_color'])) {
            $smpp_color =  sanitize_text_field($_POST['smpp_color']);
            update_post_meta($post_id, 'smpp_color', $smpp_color);
        }

        //Update UiUx color
        if (isset($_POST['smpp_second_color'])) {
            $smpp_second_color =  sanitize_text_field($_POST['smpp_second_color']);
            update_post_meta($post_id, 'smpp_second_color', $smpp_second_color);
        }
    }
}
