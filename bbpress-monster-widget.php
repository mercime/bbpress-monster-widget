<?php
/*
 * Plugin Name:       bbPress Monster Widget
 * Plugin URI:        http://wordpress.org/extend/plugins/bbpress-monster/widget/
 * Description:       A widget that allows for quick and easy testing of multiple bbPress widgets. Not intended for production sites.
 * Version:           0.2
 * License:           GPLv2 or later
 * Author:            mercime
 * Author URI:        https://profiles.wordpress.org/mercime
 * Text Domain:       bbpress-monster-widget
 * load_plugin_textdomain( 'bbpress-monster-widget' );
*/

/**
 * Register the bbPress Monster Widget.
 *
 * Hooks into the bbp_widgets_init action.
 *
 * @since 0.2
 */
function register_bbpress_monster_widget() {
	register_widget( 'bbPress_Monster_Widget' );
}
add_action( 'bbp_widgets_init', 'register_bbpress_monster_widget' );

/**
 * bbPress Monster Widget.
 *
 * A widget that allows for quick and easy testing of multiple bbPress widgets.
 *
 * @since 0.1
 */
class bbPress_Monster_Widget extends WP_Widget {

	/**
	 * Iterator (int).
	 *
	 * Used to set a unique html id attribute for each
	 * widget instance generated by BP_Monster_Widget::widget().
	 *
	 * @since 0.1
	 */
	static $iterator = 1;

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		parent::__construct( 'bbPressMonster', __( 'bbPress Monster Widget', 'bbpress-monster-widget' ), array(
			'classname'   => 'bbpress_monster_widget',
			'description' => __( 'Test multiple bbPress widgets at the same time.', 'bbpress-monster-widget' )
		) );
	}

	/**
	 * Print the bbPress Monster widget on the front-end.
	 *
	 * @uses $wp_registered_sidebars
	 * @uses bbPress_Monster_Widget::$iterator
	 * @uses bbPress_Monster_Widget::get_widget_class()
	 * @uses $this->get_widget_config()
	 *
	 * @since 0.1
	 */
	public function widget( $args, $instance ) {
		global $wp_registered_sidebars;

		$id = $args['id'];
		$args = $wp_registered_sidebars[$id];
		$before_widget = $args['before_widget'];

		foreach( $this->get_widget_config() as $widget ) {
			$_instance = ( isset( $widget[1] ) ) ? $widget[1] : null;

			$args['before_widget'] = sprintf(
				$before_widget,
				'bbpress-monster-widget-placeholder-' . self::$iterator,
				$this->get_widget_class( $widget[0] )
			);

			the_widget( $widget[0], $_instance, $args );

			self::$iterator++;
		}
    }

	/**
	 * Widgets (array).
	 *
	 * Numerically indexed array of Pre-configured widgets to
	 * display in every instance of a bbPress Monster widget. 
	 * Each entry requires two values:
	 *
	 * 0 - The name of the widget's class as registered with register_widget().
	 * 1 - An associative array representing an instance of the widget.
	 *
	 * This list can be altered by using the `bbpress-monster-widget-config` filter.
	 *
	 * @return array Widget configuration.
	 * @since 0.1
	 */
	public function get_widget_config() {
		$widgets = array(

			array( 'BBP_Login_Widget', array(
				'title'          => __( 'bbPress Login', 'bbpress-monster-widget' ),
				'register'       => '',
				'lostpass'       => '',
			) ),

			array( 'BBP_Views_Widget', array(
				'title'          => __( 'bbPress Views', 'bbpress-monster-widget' ),
			) ),

			array( 'BBP_Search_Widget', array(
				'title'          => __( 'bbPress Search', 'bbpress-monster-widget' ),
			) ),

			array( 'BBP_Forums_Widget', array(
				'title'          => __( 'bbPress Forums', 'bbpress-monster-widget' ),
				'parent_forum'   => 0,
			) ),

			array( 'BBP_Topics_Widget', array(
				'title'          => __( 'bbPress Forum Topics', 'bbpress-monster-widget' ),
				'max_shown'      => 5,
				'show_date'      => true,
				'show_user'      => true,
				'parent_forum'   => 'any',
				'order_by'       => false,
			) ),

			array( 'BBP_Stats_Widget', array(
				'title'          => __( 'bbPress Forum Statistics', 'bbpress-monster-widget' ),
			) ),

			array( 'BBP_Replies_Widget', array(
				'title'          => __( 'bbPress Forum Replies', 'bbpress-monster-widget' ),
				'show_date'      => true,
				'show_user'      => true,
				'max_shown'      => 5,
			) ),

		);

		return apply_filters( 'bbpress-monster-widget-config', $widgets );

	}

	/**
	 * Get the html class attribute value for a given widget.
	 *
	 * @uses $wp_widget_factory
	 *
	 * @param string $widget The name of a registered widget class.
	 * @return string Dynamic class name a given widget.
	 *
	 * @since 0.1
	 */
	public function get_widget_class( $widget ) {
		global $wp_widget_factory;

		$widget_obj = '';
		if ( isset( $wp_widget_factory->widgets[$widget] ) )
			$widget_obj = $wp_widget_factory->widgets[$widget];

		if ( ! is_a( $widget_obj, 'WP_Widget') )
			return '';

		if ( ! isset( $widget_obj->widget_options['classname'] ) )
			return '';

		return $widget_obj->widget_options['classname'];
	}

}