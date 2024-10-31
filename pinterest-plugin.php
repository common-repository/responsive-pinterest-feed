<?php
/*
Plugin Name: Pinterest image feed
Plugin URI: http://www.brightpathmedia.co.uk
Description: This plugin uses Pinterest's RSS to display the given users pins, from their whole collection or a specific board. It supports responsive and fixed themes with options available to easily adapt to your theme!
Version: 1.1
Author: Brightpath Media
Author URI: http://www.brightpathmedia.co.uk
*/

//Start of pinterest feed functions

function gettheFeed($feed_url, $numberofpins) {
	$content = file_get_contents($feed_url);
	$x = new SimpleXmlElement($content);
	$count = 1;
	echo '<div class="pinterest-widget">';
		echo '<ul>';
		foreach($x->channel->item as $entry) {
			$numofpins = $numberofpins;
			if($numofpins != '') {
				if($count > $numofpins) {
					break;
				}
			} elseif($numofpins == '') {
				if($count > 8) {
					break;
				}
			}
			echo "<li><a href='$entry->link' title='$entry->title'>";
			$pubdate = 'Pinned on: ' . date('d/m/Y', strtotime($entry->pubDate));
			$desc = $entry->description;
			
			$doc = new DOMDocument();
			$doc->loadHTML($desc);
			$xpath = new DOMXPath($doc);
			$src = $xpath->evaluate("string(//img/@src)");
			
			echo "<div class='imgcontainer'><img src=" . $src . " alt=" . $entry->title . " title='' /></div>";
			
			echo "</a></li>";
			$count++;
		}
		echo '</ul>';
	echo '</div>';
}

//End of Pinterest feed functions

/**
 * Adds pinterest_rss_feed widget.
 */
class pinterest_rss_feed extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'pinterest_rss_feed', // Base ID
			'Pinterest image feed', // Name
			array( 'description' => __( 'Pinterest image feed', 'brightpathmedia' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		$username = apply_filters('widget_text', $instance['username']);
		$board = str_replace(" ", "-", trim(strtolower($instance['board'])));
		$numberofpins = apply_filters('widget_text', $instance['numofpins']);
		$responsive_fixed = apply_filters('widget_text', $instance['responsive_fixed']);
		$width = apply_filters('widget_text', $instance['width']);
		$width = str_replace("px", "", $width);

		include('pinterest-styling.php');

		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}
		if($board == '') {
			$feed = 'http://pinterest.com/' . $username . '/feed.rss';
		} else {
			$feed = 'http://pinterest.com/' . $username . '/' . $board . '.rss';
		}

		gettheFeed($feed, $numberofpins);

		echo '<div style="clear:left;"></div>';
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['board'] = strip_tags( $new_instance['board'] );
		$instance['numofpins'] = strip_tags( $new_instance['numofpins'] );
		$instance['responsive_fixed'] = strip_tags( $new_instance['responsive_fixed'] );
		$instance['width'] = strip_tags( $new_instance['width'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if (isset($instance['title'])) {
			$title = $instance['title'];
		} else {
			$title = __('Pinterest feed', 'brightpathmedia');
		}
		if (isset($instance['username'])) {
			$username = $instance['username'];
		} else {
			$username = __('brightpathmedia', 'brightpathmedia');
		}
		if (isset($instance['board'])) {
			$board = $instance['board'];
		} else {
			$board = __('', 'brightpathmedia');
		}
		if (isset($instance['numofpins'])) {
			$numofpins = $instance['numofpins'];
		} else {
			$numofpins = __('4', 'brightpathmedia');
		}
		if (isset($instance['responsive_fixed'])) {
			$responsive_fixed = $instance['responsive_fixed'];
		} else {
			$responsive_fixed = __('No', 'brightpathmedia');
		}
		if (isset($instance['width'])) {
			$width = $instance['width'];
		} else {
			$width = __('', 'brightpathmedia');
		}


		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'Username:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'board' ); ?>"><?php _e( 'Board (optional):' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'board' ); ?>" name="<?php echo $this->get_field_name( 'board' ); ?>" type="text" value="<?php echo esc_attr( $board ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'numofpins' ); ?>"><?php _e( 'Number of Pins to show:' ); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id( 'numofpins' ); ?>" name="<?php echo $this->get_field_name( 'numofpins' ); ?>">
			<?php
			$pinscount = array('4', '8', '12', '16', '20');
			foreach($pinscount as $pincount) {
				echo '<option value="' . $pincount . '"';
				if(esc_attr($numofpins) == $pincount) {
					echo ' selected="selected">';
				} else {
					echo '>';
				}
				echo $pincount . '</option>';
			}
			?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'responsive_fixed' ); ?>"><?php _e( 'Responsive or Fixed?:' ); ?></label> 
		<small>If you set this option to 'Yes', the feed will act responsively within your theme's sidebar area where this feed is implemented.</small>
		<select class="widefat" id="<?php echo $this->get_field_id( 'responsive_fixed' ); ?>" name="<?php echo $this->get_field_name( 'responsive_fixed' ); ?>">
			<?php //echo esc_attr( $responsive_fixed );
			$yesnoresfix = array('Responsive' => 'Yes', 'Fixed' => 'No');
			foreach($yesnoresfix as $yesorno => $value) {
				echo '<option value="' . $value . '"';
				if(esc_attr($responsive_fixed) == $value) {
					echo ' selected="selected">';
				} else {
					echo '>';
				}
				echo $yesorno . '</option>';
			}
			?>
		</select>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:' ); ?></label> 
		<small>If you didn't set this feed to act responsively above, you can now enter a pixel value (i.e. 300px) to set the width of the image container for your pins (by default it is set to 100% of the existing sidebar/widget container width).</small>
		<input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>" />
		</p>
		<?php 
	}

} //End of class pinterest_rss_feed

// register pinterest_rss_feed wiget
add_action( 'widgets_init', create_function( '', 'register_widget( "pinterest_rss_feed" );' ) );

?>