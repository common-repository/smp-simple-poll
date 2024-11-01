<?php
get_header();

while (have_posts()) : the_post();
	$smp_option_names = array();
	if (get_post_meta(get_the_id(), 'smp_poll_option', true)) {
		$smp_option_names = get_post_meta(get_the_id(), 'smp_poll_option', true);
	}

	$smp_poll_status = get_post_meta(get_the_id(), 'smp_poll_status', true);
	$smp_display_poll_result = get_post_meta(get_the_id(), 'smp_display_poll_result', true);
	$smp_poll_option_id = get_post_meta(get_the_id(), 'smp_poll_option_id', true);
	$smp_poll_end_date = get_post_meta(get_the_id(), 'smp_end_date', true);
	$smp_poll_vote_total_count = (int) get_post_meta(get_the_id(), 'smp_vote_total_count', true);
	$color1 = get_post_meta(get_the_id(), 'smp_poll_color', true);
	$color2 = get_post_meta(get_the_id(), 'smp_poll_second_color', true);
	$color_type = get_post_meta(get_the_id(), 'smp_poll_color_options', true);

	$is_public = smp_poll_is_public($smp_display_poll_result);
	$is_pav = smp_poll_is_public_after_vote($smp_display_poll_result);

	$is_voted = 0;
	if (smp_poll_check_for_unique_voting(get_the_id())) {
		$is_voted = smp_poll_check_for_unique_voting(get_the_id());
	}


	$has_live = '';
	if ($smp_poll_status === 'live') {
		$has_live = ' live';
	}
	$name_option = '';
	if (!$is_voted) {
		$name_option = ' smp_option-name';
	}

	?>
	<div class="smp_container text-align-center smp-poll-<?php echo esc_attr(get_the_id()); ?>" id="smp-poll-<?php echo esc_attr(get_the_id()); ?>">
		<div class="smp_survey-stage">
			<span class="smp_stage smp_live smp_active <?php if ($smp_poll_status !== 'live') echo esc_attr('hidden'); ?>"><?php echo esc_html__('Live', 'smp-simple-poll'); ?></span>
			<span class="smp_stage smp_ended smp_active <?php if ($smp_poll_status !== 'end') echo esc_attr('hidden'); ?>"><?php echo esc_html__('Ended', 'smp-simple-poll'); ?></span>
		</div>
		<div class="smp_title">

			<div class="smp_poll-title">
				<h1><?php the_title();  ?></h1>
			</div>

			<?php if ($is_public || $is_pav) : ?>
				<div class="smp_survey-total-vote">
					<span> <?php echo wp_kses_post('Total Vote: ' . '<span>' . $smp_poll_vote_total_count . '</span>'); ?> </span>
				</div>
			<?php endif; ?>
		</div>
		<div class="smp_inner">
			<div class="smp_surveys">

				<?php if ($smp_poll_status !== 'end') : ?>
					<div class="smp_poll-end-time text-align-center">
						<span><?php echo esc_html__('Will End : ' . date("M d, Y", strtotime($smp_poll_end_date)), 'smp-simple-poll'); ?></span>
					</div>
				<?php endif; ?>

				<?php
					$i = 0;
					if ($smp_option_names) :
						foreach ($smp_option_names as $smp_option_name) :
							$smp_poll_vote_count =  get_post_meta(get_the_id(), 'smp_vote_count_' . (float) $smp_poll_option_id[$i], true);

							$smp_poll_vote_percentage = 0;
							if ($smp_poll_vote_count == 0) {
								$smp_poll_vote_percentage = 0;
							} else {
								if ($smp_poll_vote_total_count > 0) {
									$smp_poll_vote_percentage = (float) $smp_poll_vote_count * 100 / $smp_poll_vote_total_count;
								}
							}
							$smp_poll_vote_percentage = number_format($smp_poll_vote_percentage, 2);

							?>
						<div class="smp_survey-item <?php echo esc_attr($smp_display_poll_result); ?>">
							<div class="smp_survey-item-inner smp_card_front">
								<div class="smp_survey-item-action <?php if ($is_voted) echo esc_attr('smp_survey-item-action-disabled'); ?>">
									<form action="" name="smp_survey-item-action-form" class="smp_survey-item-action-form">
										<input type="hidden" name="smp_poll-id" class="smp_poll-id" value="<?php echo esc_attr(get_the_id()); ?>">
										<input type="hidden" name="smp_survey-item-id" class="smp_survey-item-id" value="<?php echo esc_attr($smp_poll_option_id[$i]); ?>">

										<input type="button" role="vote" name="smp_survey-vote-button" class="smp_survey-vote-button <?php echo esc_attr($has_live);
																																					echo esc_attr($name_option); ?>" id="smp_option-id-<?php echo esc_attr($i) ?>">
									</form>

									<div class="smp_survey-name">
										<span><?php echo esc_html($smp_option_name); ?></span>
									</div>
								</div>


								<div class="smp_pull-right">

									<div class="smp_survey-progress">
										<div class="smp_survey-progress-bg">
											<div class="smp_survey-progress-fg smp_orange_gradient" <?php if ($is_public || ($is_pav && $is_voted)) : ?> style="width:<?php echo esc_attr($smp_poll_vote_percentage); ?>%;" <?php endif; ?>>
											</div>

											<?php if ($is_public || $is_pav) : ?>
												<div class="smp_survey-progress-label <?php if ($is_pav && !$is_voted) echo esc_attr("temp-hide"); ?>">
													<?php echo esc_html($smp_poll_vote_percentage); ?>%
												</div>
											<?php endif; ?>
										</div>

									</div>
								</div>


							</div>
						</div>

					<?php $i++;
							endforeach; ?>
				<?php endif; ?>
			</div>
			<div style="clear:both;"></div>
		</div>

		<div class="smp_user-partcipeted">
			<?php if ($smp_option_names) :
					foreach ($smp_option_names as $smp_option_name) :
						if ($is_voted) : ?>
						<p> <?php echo esc_html__('You already partcipeted.', 'smp-simple-poll'); ?></p>
			<?php endif;
						break;
					endforeach;
				endif; ?>
		</div>
	</div>

	<?php echo dynamic_poll_style(get_the_id(), $color1, $color2, $color_type, get_the_post_thumbnail_url(get_the_ID(), 'thumbnail')); ?>

<?php endwhile;

get_footer();
?>