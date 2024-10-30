<?php
/*
Plugin Name: Current Comments
Plugin URI: http://allendav.com/wordpress-plugins/
Description: Live comments widget for WordPress, powered by Backbone.js
Version: 0.4.0
Author: allendav
Author URI: http://www.allendav.com
Text Domain: current-comments
License: GPL2
*/

/*  Copyright 2014 Allen Snook (email: allendav@allendav.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require "current-comments-widget.php";

class Current_Comments {

	protected $newer_than = 0;

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {

		// Load plugin text domain
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

		// Register our widget
		add_action( 'widgets_init', array( $this, 'register_plugin_widget' ) );

		// Add our read-only comments endpoint
		add_action( 'wp_ajax_currcomm_read', array( $this, 'handle_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_currcomm_read', array( $this, 'handle_ajax_request' ) );
	}

	public function plugins_loaded() {
		load_plugin_textdomain( 'current-comments', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Registers and enqueues plugin-specific styles.
	 */
	public function register_plugin_styles() {
		wp_enqueue_style( 'current-comments-styles', plugins_url( 'current-comments/css/styles.css' ) );
	}

	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
	public function register_plugin_scripts() {
		// Note:  1) jquery will automatically be included by jquery-color and 2) underscore will be automatically included by backbone
		wp_enqueue_script( 'current-comments-moment-script', plugins_url( 'current-comments/js/moment.min.js' ), array( ) );
		wp_enqueue_script( 'current-comments-script', plugins_url( 'current-comments/js/script.js' ), array( 'jquery-color', 'backbone', 'current-comments-moment-script' ) );
		wp_localize_script( 'current-comments-script', 'Current_Comments_Ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * Register our widget.
	 */
	public function register_plugin_widget() {
		register_widget( 'Current_Comments_Widget' );
	}

	/**
	 * Repackage a comment array into an array of model attributes
	 */
	public function comments_to_models( $comments ) {
		$models = array();

		foreach ( (array) $comments as $comment ) {
			$models[] = array(
				'id'               => $comment->comment_ID,
				'author'           => $comment->comment_author,
				'author_url'       => $comment->comment_author_url,
				'post_title'       => get_the_title( $comment->comment_post_ID ),
				'permalink'        => get_comment_link( $comment ),
				'comment_date_gmt' => strtotime( $comment->comment_date_gmt )
			);
		}

		return $models;
	}

	/**
	 * Filter AJAX response based on newer_than
	 */
	public function comments_clauses( $query ) {
		if ( isset( $query['where'] ) ) {
			$query['where'] = $query['where'] . " AND comment_ID > '" . $this->newer_than . "'";
		} else {
			$query['where'] = "comment_ID > '" . $this->newer_than . "'";
		}
		return $query;
	}

	/**
	 * AJAX handler
	 */
	public function handle_ajax_request() {
		$this->newer_than = isset( $_POST['newerthan'] ) ? intval( $_POST['newerthan'] ) : 0;

		// get the newest comments
		add_filter( 'comments_clauses', array( $this, 'comments_clauses' ) );
		$comments = get_comments( array( 
			'status' => 'approve',
			'order'  => 'DESC',
			'number' => 10
			)
		);
		remove_filter( 'comments_clauses', array( $this, 'comments_clauses' ) );

		$models = $this->comments_to_models( $comments );

		header( 'Content-Type: application/json', true, 200 );
		die( json_encode( $models ) );
	}
}

/*
 * Instantiate!
 */
$current_comments = new Current_Comments();
