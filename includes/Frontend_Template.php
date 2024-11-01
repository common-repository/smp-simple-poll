<?php

/**
 * @package SmppSimplePoll
 */

get_header();
while (have_posts()) : the_post();
    $poll_id = get_the_id();
endwhile;

echo do_shortcode('[SIMPLE_POLL id="' . esc_attr($poll_id) . '"]');
get_footer();
