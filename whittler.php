<?php
/**
 * Plugin Name: Whittler
 * Plugin URI: http://awcdev.com
 * Description: This plugin creates a category list widget that whittles down the posts on the fly as you select terms.
 * Version: 1.0.0
 * Author: Josiah Altschuler
 * Author URI: http://awcdev.com
 * License: GPL2
 */

class Whittler_widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'whittler', 'description' => __( "Creates a category list that whittles down posts on the fly as you select terms." ) );
		parent::__construct('whittler_widget', __('Whittler'), $widget_ops);
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$cat_args = array(
			'orderby'	=> 'name',
			'title_li'	=> ''
		);

		echo "<ul>";
		wp_list_categories( apply_filters( 'widget_categories_args', $cat_args ) );
		echo "</ul>";

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
		if ( !empty( $new_instance['title'] ) ) {
			$instance['title'] = strip_tags( $new_instance['title'] );
		} else {
			$instance['title'] = '';
		}

		return $instance;
	}
		
	public function form( $instance ) {
		$title = sanitize_text_field( $instance['title'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}
	
}

function wpb_load_widget() {
	register_widget( 'whittler_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );
