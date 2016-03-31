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

		global $post;

		$terms = $_GET["privateresourcecategory"];

		//Put all the parameters from the URL into an array
		if ($terms != "") {
			$parameters = explode(" ", $terms);
		}

		$sidebarTerms = array(); //This will collect all the topic terms to place in the sidebar dropdown

		while ( have_posts() ) : the_post();
			//Get all the category terms for this post
			$thisPostTerms = wp_get_object_terms($post->ID, 'privateresourcecategory');
			if ( ! empty( $thisPostTerms ) && ! is_wp_error( $thisPostTerms ) ) {

				foreach ( $thisPostTerms as $term ) {

					if (!in_array($term, $sidebarTerms)) {
						//Add the term to an array to list all of them in the sidebar later
						array_push($sidebarTerms, $term);

						//Add the term to a string to be printed as a list to the sidebar later
						$separatedTerms = '';
						$alreadySelected = FALSE;

						if ($terms != '') {
							if (in_array($term->slug, $parameters)) {
								$alreadySelected = TRUE;
							}
							$separatedTerms = implode("+", $parameters);
							$separatedTerms .= "+";
						}
						if ($alreadySelected == TRUE) {
							echo '<p class="selected-term">' . $term->name . '</p>';
						} else {
							echo '<a class="term" href="' . home_url( '/' ) . 'provider-portal/privateresources/?privateresourcecategory=' . $separatedTerms . $term->slug . '">' . $term->name . '</a>';
						}
					}
				}
			}
		endwhile;

		rewind_posts();

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

function no_pagination( $query ) {
   if ( is_post_type_archive( 'privateresources' ) ) {
        // Display 50 posts for a custom post type called 'movie'
        $query->set( 'posts_per_page', -1 );
        return;
    }
}
add_action( 'pre_get_posts', 'no_pagination' );