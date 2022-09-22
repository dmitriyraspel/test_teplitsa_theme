<?php
/**
 * Dima-theme Theme functions and definitions.
 *
 * @package dima-theme
 */

add_action( 'wp_enqueue_scripts', 'twentytwentyone_parent_theme_enqueue_styles' );

/**
 * Enqueue scripts and styles.
 */
function twentytwentyone_parent_theme_enqueue_styles() {
	wp_enqueue_style( 'twentytwentyone-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'dima-theme-style',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'twentytwentyone-style' ]
	);
}



add_action( 'cmb2_admin_init', 'cmb2_sample_metaboxes' );
/**
 * Define the metabox and field configurations.
 */
function cmb2_sample_metaboxes() {

	// Initiate the metabox
	$cmb = new_cmb2_box( array(
		'id'            => 'test_text_metabox',
		'title'         => __( 'Test Text', 'cmb2' ),
		'object_types'  => array( 'post' ), // Post type - 'post', 'community'
		'context'       => 'normal',
		'priority'      => 'high',
	) );

	// Regular text field
	$cmb->add_field( array(
		'name'       => __( 'Text', 'cmb2' ),
		'desc'       => __( 'Добавьте сюда текст для вывода после поста', 'cmb2' ),
		'id'         => 'text_after_content',
		'type'       => 'text',
		'show_on_cb' => 'cmb2_hide_if_no_cats',
	) );
}

add_filter('the_content', 'new_text_after_content');
/**
 * Вывод дополнительного текста после основного контента.
 */
function new_text_after_content( $content ){
	$dop_content = get_post_meta( get_the_ID(), 'text_after_content', true );

	if ( ! empty( $dop_content ) && is_single() ) {
		$new_content = $content . '<br> <p>' . $dop_content . '</p>';
	}
	else {
		$new_content = $content;
	}
	return $new_content;
}


/**
 * Custom post type Community
 */
require_once get_stylesheet_directory(). '/post-types/Community.php';

/**
 * Rest Controller Community
 */
require_once get_stylesheet_directory(). '/rest-api/rest_community.php';