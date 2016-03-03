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
		parent::__construct(
			'whittler_widget', 
			__('Whittler', 'wpb_widget_domain'), 
			array( 'description' => __( 'Creates a category list that whittles down posts on the fly as you select terms', 'wpb_widget_domain' ), ) 
		);
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo __( 'List terms here', 'wpb_widget_domain' );
		echo $args['after_widget'];
	}
		
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'wpb_widget_domain' );
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
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
}

function wpb_load_widget() {
	register_widget( 'whittler_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );
