<?php

class Current_Comments_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
	 		'current-comments-widget', // Base ID
			__( 'Current Comments Widget', 'current-comments' ), // Name
			array( 'description' => __( 'Live comments widget for WordPress, powered by Backbone.js', 'current-comments' ) )
		);
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		$title = $instance['title'];
		$title = apply_filters( 'widget_title', $title );

		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		$time = time();
		echo "<ul class='current-comments-container'></ul>";

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	function form($instance) {
		$defaults = array( 'title' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title_ID = esc_attr( $this->get_field_id( 'title' ) );
		$title_name = esc_attr( $this->get_field_name( 'title' ) );
		$title_value = esc_attr( $instance['title'] );

		echo "<p>";
		echo "<label for='$title_ID'>" . esc_html__( 'Title:', 'current-comments' ) . "</label><br/>";
		echo "<input type='text' id='$title_ID' name='$title_name' value='$title_value' />";
		echo "</p>";
	}
}
