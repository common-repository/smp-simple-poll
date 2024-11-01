<?php

/**
 * @package SmppSimplePoll
 */

namespace Includes\Classes;

use Includes\Classes\Poll_Methodes;

class Poll_Shortcode
{
    public function register()
    {
        add_shortcode('SIMPLE_POLL', array($this, 'smpp_add_shortcode'));
        add_filter('widget_text',  array($this, 'do_shortcode'));
        add_filter('content',  array($this, 'do_shortcode'));
    }

    /**
     * Implement poll shortcode
     * @return boolean value
     */
    public function smpp_add_shortcode($atts, $content = null)
    {
        $a = shortcode_atts(array(
            'id' => '1',
            'type' => '',
            'use_in' => 'post'
        ), $atts);

        $smpp_shortcode_args = array(
            'post_type'              => array('smpp_poll'),
            'post_status'            => array('publish'),
            'nopaging'               => true,
            'order'                  => 'DESC',
            'orderby'                => 'date',
            'p'                      => $a['id']
        );

        // The Query
        $smpp_post_query = new \WP_Query($smpp_shortcode_args);
        // The Loop
        ob_start();
        if ($smpp_post_query->have_posts()) {

            while ($smpp_post_query->have_posts()) : $smpp_post_query->the_post();

                Poll_Methodes::smpp_poll_status(get_the_id(), get_post_meta(get_the_id(), 'smpp_end_date', true));

                $smpp_option_names = array();
                if (get_post_meta(get_the_id(), 'smpp_option', true)) {
                    $smpp_option_names = get_post_meta(get_the_id(), 'smpp_option', true);
                }

                $smpp_status = get_post_meta(get_the_id(), 'smpp_status', true);
                $smpp_display_poll_result = get_post_meta(get_the_id(), 'smpp_display_poll_result', true);
                $smpp_option_id = get_post_meta(get_the_id(), 'smpp_option_id', true);
                $smpp_end_date = get_post_meta(get_the_id(), 'smpp_end_date', true);
                $smpp_vote_total_count = (int) get_post_meta(get_the_id(), 'smpp_vote_total_count', true);
                $color1 = get_post_meta(get_the_id(), 'smpp_color', true);
                $color2 = get_post_meta(get_the_id(), 'smpp_second_color', true);
                $color_type = get_post_meta(get_the_id(), 'smpp_color_options', true);

                $is_public = Poll_Methodes::smpp_is_public($smpp_display_poll_result);
                $is_pav = Poll_Methodes::smpp_is_public_after_vote($smpp_display_poll_result);

                $is_voted = 0;
                if (Poll_Methodes::smpp_check_for_unique_voting(get_the_id())) {
                    $is_voted = Poll_Methodes::smpp_check_for_unique_voting(get_the_id());
                }

                $today = date("Y-m-d");
                $expire = $smpp_end_date;
                $today_time = strtotime($today);
                $expire_time = strtotime($expire);
                if ($expire_time < $today_time) {
                    $smpp_status = 'end';
                    update_post_meta(get_the_id(), 'smpp_status', 'end');
                }

                $has_live = '';
                if ($smpp_status === 'live') {
                    $has_live = ' live';
                }
                $name_option = '';
                if (!$is_voted) {
                    $name_option = ' smpp_option-name';
                }

                ?>
                <div class="smpp_container text-align-center smp-poll-<?php echo esc_attr(get_the_id()); ?>" id="smp-poll-<?php echo esc_attr(get_the_id()); ?>">
                    <div class="smpp_survey-stage">
                        <span class="smpp_stage smpp_live smpp_active <?php if ($smpp_status !== 'live') echo esc_attr('hidden'); ?>"><?php echo esc_html__('Live', 'smp-simple-poll'); ?></span>
                        <span class="smpp_stage smpp_ended smpp_active <?php if ($smpp_status !== 'end') echo esc_attr('hidden'); ?>"><?php echo esc_html__('Ended', 'smp-simple-poll'); ?></span>
                    </div>
                    <div class="smpp_title">

                        <div class="smpp-title">
                            <h1><?php the_title();  ?></h1>
                        </div>

                        <?php if ($is_public || $is_pav) : ?>
                            <div class="smpp_survey-total-vote">
                                <span> <?php echo wp_kses_post('Total Vote: ' . '<span>' . $smpp_vote_total_count . '</span>'); ?> </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="smpp_inner">
                        <div class="smpp_surveys">

                            <?php if ($smpp_status !== 'end') : ?>
                                <div class="smpp-end-time text-align-center">
                                    <span><?php echo esc_html__('Will End : ' . date("M d, Y", strtotime($smpp_end_date)), 'smp-simple-poll'); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php $i = 0; ?>
                            <?php if ($smpp_option_names) : ?>
                                <?php foreach ($smpp_option_names as $smpp_option_name) : ?>
                                    <?php $smpp_vote_count = get_post_meta(get_the_id(), 'smpp_vote_count_' . (float) $smpp_option_id[$i], true);

                                                            $smpp_vote_percentage = 0;
                                                            if ($smpp_vote_count == 0) {
                                                                $smpp_vote_percentage = 0;
                                                            } else {
                                                                if ($smpp_vote_total_count > 0) {
                                                                    $smpp_vote_percentage = (float) $smpp_vote_count * 100 / $smpp_vote_total_count;
                                                                }
                                                            }
                                                            $smpp_vote_percentage = number_format($smpp_vote_percentage, 2);

                                                            ?>
                                    <div class="smpp_survey-item smpp-item-<?php echo esc_attr($i); ?> <?php echo esc_attr($smpp_display_poll_result); ?>">
                                        <div class="smpp_survey-item-inner smpp_card_front">
                                            <div class="smpp_survey-item-action <?php if ($is_voted) echo esc_attr('smpp_survey-item-action-disabled'); ?>">
                                                <form action="" name="smpp_survey-item-action-form" class="smpp_survey-item-action-form">
                                                    <input type="hidden" name="smpp_poll-id" class="smpp_poll-id" value="<?php echo esc_attr(get_the_id()); ?>">
                                                    <input type="hidden" name="smpp_survey-item-id" class="smpp_survey-item-id" value="<?php echo esc_attr($smpp_option_id[$i]); ?>">

                                                    <input type="button" role="vote" name="smpp_survey-vote-button" class="smpp_survey-vote-button <?php echo esc_attr($has_live);
                                                                                                                                                                            echo esc_attr($name_option); ?>" id="smpp_option-id-<?php echo esc_attr($i) ?>">
                                                </form>

                                                <div class="smpp_survey-name">
                                                    <span><?php echo esc_html($smpp_option_name); ?></span>
                                                </div>
                                            </div>


                                            <div class="smpp_pull-right">

                                                <div class="smpp_survey-progress">
                                                    <div class="smpp_survey-progress-bg">
                                                        <div class="smpp_survey-progress-fg smpp_orange_gradient" <?php if ($is_public || ($is_pav && $is_voted)) : ?> style="width:<?php echo esc_attr($smpp_vote_percentage); ?>%;" <?php endif; ?>>
                                                        </div>

                                                        <?php if ($is_public || $is_pav) : ?>
                                                            <div class="smpp_survey-progress-label <?php if ($is_pav && !$is_voted) echo esc_attr("temp-hide"); ?>">
                                                                <?php echo esc_html($smpp_vote_percentage); ?>%
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>

                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div style="clear:both;"></div>
                    </div>

                    <div class="smpp_user-partcipeted">
                        <?php if ($smpp_option_names) :
                                            foreach ($smpp_option_names as $smpp_option_name) :
                                                if ($is_voted) : ?>
                                    <p> <?php echo esc_html__('You already partcipeted.', 'smp-simple-poll'); ?></p>
                        <?php endif;
                                                break;
                                            endforeach;
                                        endif; ?>
                    </div>
                </div>

<?php echo Poll_Methodes::dynamic_poll_style(get_the_id(), $color1, $color2, $color_type, get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'));
            endwhile;
        }

        $output = ob_get_contents();
        ob_end_clean();
        return $output;
        // Restore original Post Data
        wp_reset_postdata();
    }
}
