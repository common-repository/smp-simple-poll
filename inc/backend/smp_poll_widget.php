<?php

//Registering Widget
function smp_poll_register_widget()
{
	register_widget('smp_poll_widget');
}
add_action('widgets_init', 'smp_poll_register_widget');

// Creating the widget 
class smp_poll_widget extends WP_Widget
{

	function __construct()
	{
		parent::__construct(

			// Base ID of your widget
			'smp_poll_widget',

			// Widget name will appear in UI
			__('Simple Poll', 'smp-simple-poll'),

			// Widget description
			array('description' => __('Add Poll via widget in sidebar', 'smp-simple-poll'),)
		);
	}

	// Creating widget front-end

	public function widget($args, $instance)
	{
		$poll_id = $instance['poll_id'];
		echo wp_kses_post($args['before_widget']);
		?>
		<div class="smp_poll_widget">
			<?php echo do_shortcode('[SIMPLE_POLL id="' . esc_attr($poll_id) . '" use_in="widget"]'); ?>
		</div>
	<?php
			echo wp_kses_post($args['after_widget']);
		}

		// Widget Backend 
		public function form($instance)
		{
			if (isset($instance['poll_id'])) {
				$poll_id = $instance['poll_id'];
			} else {
				$poll_id = 1;
			}
			// Widget admin form
			?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('poll_id')); ?>"><?php _e('Select A Poll:', 'smp-simple-poll'); ?></label>
			<select class="widefat" id="<?php echo esc_attr($this->get_field_id('poll_id')); ?>" name="<?php echo esc_attr($this->get_field_name('poll_id')); ?>">
				<option value="0"><?php echo esc_html__('Choose Poll', 'smp-simple-poll'); ?></option>
				<?php
						// WP_Query arguments
						$smp_backend_query_args = array(
							'post_type'              => array('smp_poll'),
							'post_status'            => array('publish'),
							'nopaging'               => false,
							'paged'                  => '0',
							'posts_per_page'         => '20',
							'order'                  => 'DESC',
						);

						// The Query
						$smp_backend_query = new WP_Query($smp_backend_query_args);

						// The Loop
						$i = 1;
						if ($smp_backend_query->have_posts()) {
							while ($smp_backend_query->have_posts()) {
								$smp_backend_query->the_post(); ?>
						<option value="<?php echo esc_attr(get_the_id()); ?>" <?php if ($poll_id == get_the_id()) echo esc_attr(" selected"); ?>>
							<?php the_title(); ?>
						</option>
				<?php }
						}
						?>
			</select>
		</p>

<?php
	}

	// Updating widget replacing old instances with new
	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['poll_id'] = (!empty($new_instance['poll_id'])) ? strip_tags($new_instance['poll_id']) : '';
		return $instance;
	}
}
