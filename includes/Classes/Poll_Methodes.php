<?php

/**
 * @package SmppSimplePoll
 */

namespace Includes\Classes;

use Includes\Classes\Poll_Frontend_Template;

class Poll_Methodes
{
    public function register()
    {
        add_filter('single_template', array($this, 'get_smpp_template'));
        add_action('wp_ajax_smpp_vote', array($this, 'smpp_ajax_method'));
        add_action('wp_ajax_nopriv_smpp_vote', array($this, 'smpp_ajax_method'));
        add_filter('manage_smpp_poll_posts_columns', array($this, 'smpp_set_custom_edit_columns'));
        add_action('manage_smpp_poll_posts_custom_column', array($this, 'smpp_custom_column'), 10, 2);
    }

    /**
     * Create poll frontend template method
     */
    public function get_smpp_template($single_template)
    {
        global $post;
        if ('smpp_poll' === $post->post_type) {
            // $single_template = Poll_Frontend_Template::smpp_frontend_template();
            $single_template = DIRNAME . '/includes/Frontend_Template.php';
        }
        return $single_template;
    }

    /**
     * Implement poll ajax request when user click on vote option button
     * @return object poll data 
     */
    public function smpp_ajax_method()
    {

        if (isset($_POST['action']) and $_POST['action'] == 'smpp_vote') {

            if (isset($_POST['poll_id'])) {
                $poll_id = intval(sanitize_text_field($_POST['poll_id']));
            }

            if (isset($_POST['option_id'])) {
                $option_id = (float) sanitize_text_field($_POST['option_id']);
            }


            //Validate Poll ID
            if (!$poll_id) {
                $poll_id = '';
                die(json_encode(array("voting_status" => "error", "msg" => "Fields are required")));
            }

            //Validate Option ID
            if (!$option_id) {
                $option_id = '';
                die(json_encode(array("voting_status" => "error", "msg" => "Fields are required")));
            }

            $oldest_vote = 0;
            $oldest_total_vote = 0;
            if (get_post_meta($poll_id, 'smpp_vote_count_' . $option_id, true)) {
                $oldest_vote = get_post_meta($poll_id, 'smpp_vote_count_' . $option_id, true);
            }
            if (get_post_meta($poll_id, 'smpp_vote_total_count')) {
                $oldest_total_vote = get_post_meta($poll_id, 'smpp_vote_total_count', true);
            }

            if (!self::smpp_check_for_unique_voting($poll_id)) {

                $new_total_vote = intval($oldest_total_vote) + 1;
                $new_vote = (int) $oldest_vote + 1;
                update_post_meta($poll_id, 'smpp_vote_count_' . $option_id, $new_vote);
                update_post_meta($poll_id, 'smpp_vote_total_count', $new_total_vote);

                $outputdata = array();
                $outputdata['total_vote_count'] = $new_total_vote;
                $outputdata['total_opt_vote_count'] = $new_vote;
                $outputdata['option_id'] = $option_id;
                $outputdata['voting_status'] = "done";
                $outputdataPercentage = ($new_vote * 100) / $new_total_vote;
                $outputdata['total_vote_percentage'] = (int) $outputdataPercentage;
                $outputdata['options_ids'] = get_post_meta($poll_id, 'smpp_option_id', true);

                $i = 1;

                $array_parcentage = array();
                foreach ($outputdata['options_ids'] as $optionid) {
                    // $outputdata['option_id_' . $i] = $optionid;
                    $vote = (int) get_post_meta($poll_id, 'smpp_vote_count_' . (float) $optionid, true);
                    $parcenttage = ($vote * 100) /  get_post_meta($poll_id, 'smpp_vote_total_count', true);
                    $array_parcentage[] = number_format($parcenttage, 2);
                    $i++;
                }
                $outputdata['total_vote_percentage'] = $array_parcentage;

                print_r(json_encode($outputdata));
            }
        }
        die();
    }

    /**
     * Set custom column for all polls
     */

    public function smpp_set_custom_edit_columns($columns)
    {
        $columns['smpp_id'] = __('Poll ID', 'smp-simple-poll');
        $columns['poll_status'] = __('Poll Status', 'smp-simple-poll');
        $columns['shortcode'] = __('Shortcode', 'smp-simple-poll');
        $columns['view_result'] = __('Result', 'smp-simple-poll');
        return $columns;
    }

    /**
     * Added custom column for all polls
     */
    public function smpp_custom_column($column, $post_id)
    {
        switch ($column) {

            case 'shortcode':
                $code = '[SIMPLE_POLL id="' . $post_id . '"]';
                if (is_string($code))
                    echo wp_kses_post('<code>' . $code . '</code> <span class="invisible" >Copied</span>');
                else
                    _e('Unable to get shortcode', 'smp-simple-poll');
                break;
            case 'poll_status':
                self::smpp_poll_status($post_id, get_post_meta($post_id, 'smpp_end_date', true));
                echo wp_kses_post("<span style='text-transform:uppercase'>" . get_post_meta(get_the_id(), 'smpp_status', true) . "</span>");
                break;
            case 'smpp_id':
                echo wp_kses_post("<span style='text-transform:uppercase'>" . esc_attr(get_the_id()) . "</span>");
                break;

            case 'view_result':
                $option_id = '';
                $option_id = get_post_meta($post_id, 'smpp_option_id', true);

                if (is_array($option_id)) {
                    $i = 0;
                    $count = 0;
                    foreach ($option_id as $optionid) {
                        $i++;
                        if (get_post_meta($post_id, 'smpp_vote_count_' . (float) $optionid, true)) {
                            $count = get_post_meta($post_id, 'smpp_vote_count_' . (float) $optionid, true);
                        }
                        if (get_post_meta($post_id, 'smpp_option', true)) {
                            $smpp_option = get_post_meta($post_id, 'smpp_option', true);
                        }
                        echo esc_html($smpp_option[$i - 1] . ': ' . $count);

                        if (count($option_id) > $i) {
                            echo wp_kses_post('<br/>');
                        }
                        $count = 0;
                    }
                }
                break;
        }
    }

    /**
     * Check uniq vote or not
     * @return boolean value
     */
    public static function smpp_check_for_unique_voting($poll_id)
    {
        if (isset($_COOKIE['is_voted_' . $poll_id])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check poll is public or not
     * @return boolean value
     */
    public static function smpp_is_public($smp_display_poll_result)
    {
        if ($smp_display_poll_result === 'public') {
            return true;
        }
        return false;
    }

    /**
     * Check poll public or not after vote 
     * @return boolean value
     */
    public static function smpp_is_public_after_vote($smp_display_poll_result)
    {
        if ($smp_display_poll_result === 'public_after_vote') {
            return true;
        }
        return false;
    }

    /**
     * Find out poll status 
     * @return live or end
     */
    public static function smpp_poll_status($poll_id, $expire)
    {
        $today = date("Y-m-d");
        $today_time = strtotime($today);
        $expire_time = strtotime($expire);
        update_post_meta($poll_id, 'smpp_status', 'live');
        if ($expire_time < $today_time) {
            update_post_meta($poll_id, 'smpp_status', 'end');
        }
    }

    /**
     * Impletement dynamic style for each poll by poll id
     */
    public static function dynamic_poll_style($poll_id, $color1, $color2, $color_type, $poll_bg)
    {

        if ($color_type === 'gradient') {
            $bg_color = 'linear-gradient(to right, ' . $color1 . ', ' . $color2 . ')';
        } else {
            $bg_color = $color1;
        }


        return '<style>
				.smp-poll-' . esc_html($poll_id) . ' .smpp_fill-option,
				.smp-poll-' . esc_html($poll_id) . ' .smpp_survey-stage .smpp_live,
				.smp-poll-' . esc_html($poll_id) . ' .smpp_survey-stage .smpp_ended,
				.smp-poll-' . esc_html($poll_id) . ' .smpp_inner {
					background: ' . esc_html($bg_color) . '!important;
				}
				
				.smp-poll-' . esc_html($poll_id) . ' .smpp_survey-item-action-form input[role=vote]{
					border-color: ' . esc_html($color1) . '!important;
				}
				.smp-poll-' . esc_html($poll_id) . ' .smpp_inner:after {
					background: ' . esc_html($bg_color) . '!important;
				}
				.smp-poll-' . esc_html($poll_id) . ' .smpp_inner::before {
					background-image: url("' . esc_html($poll_bg) . '");
				}
			</style>';
    }
}
