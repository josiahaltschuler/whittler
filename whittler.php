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



		if ($_GET["category"] != "") {
			echo "<div id='you-searched-for'>";
			echo "You searched for: ";
			
			if ($_GET["category"] != "") {
				$categoryParameters = explode(" ", $_GET["category"]);
				foreach ($categoryParameters as $categoryParameter) {
					$term = get_term_by('slug', $categoryParameter, 'category');
					$name = $term->name;
					echo " <a class='topic-term' href='" . esc_url( home_url( '/' ) ) . "resources/evidence-based-practices/?category=" . $_GET["type"] . "'>" . $name . "</a>";
				}
			}
			echo "<p>Number of results: " . $GLOBALS['wp_query']->found_posts . "</p>";
			echo "<br /><p><a id='clear-search' href='" . esc_url( home_url( '/' ) ) . "'>Clear Search</a></p>";
			echo "</div>";
		} else {
			echo "<p>Number of results: " . $GLOBALS['wp_query']->found_posts . "</p>";
		}



		// Get all the terms that are used in this list of posts and put them in an array.
		// Need to do this so that we don't list category terms used by other post types.
		$dropdownTopics = array(); //This will collect all the topic terms to place in the sidebar dropdown

		query_posts( 'posts_per_page=-1' );
		while ( have_posts() ) : the_post();
			$id = get_the_id();
			$topicTerms = wp_get_object_terms($id, 'category');
			if ( ! empty( $topicTerms ) && ! is_wp_error( $topicTerms ) ) {
				foreach ( $topicTerms as $term ) {
					if (!in_array($term, $dropdownTopics)) {
						array_push($dropdownTopics, $term);
					}
				}
			}
		endwhile;
		wp_reset_postdata();

		if ( ! empty( $dropdownTopics ) && ! is_wp_error( $dropdownTopics ) ) {
			foreach ( $dropdownTopics as $term ) {
				$categorySeparatedTerms = '';
				$alreadySelected = FALSE;

				if ($_GET["category"] != '') {
					if (in_array($term->slug, $categoryParameters)) {
						$alreadySelected = TRUE;
					}
					$categorySeparatedTerms = implode("+", $categoryParameters);
					$categorySeparatedTerms .= "+";
				}

				if ($alreadySelected == TRUE) {
					echo '<p>&#x2713; ' . $term->name . '</p>';
				} else {
					echo '<a href="' . home_url( '/?category=' ) . $categorySeparatedTerms . $term->slug . '">' . $term->name . '</a><br />';
				}
			}
		}

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
