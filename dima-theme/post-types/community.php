<?php

/**
 * Registers the `community` post type.
 */
function community_init() {
	register_post_type(
		'community',
		[
			'labels'                => [
				'name'                  => __( 'Communities', 'twentytwentyone' ),
				'singular_name'         => __( 'Community', 'twentytwentyone' ),
				'all_items'             => __( 'All Communities', 'twentytwentyone' ),
				'archives'              => __( 'Community Archives', 'twentytwentyone' ),
				'attributes'            => __( 'Community Attributes', 'twentytwentyone' ),
				'insert_into_item'      => __( 'Insert into Community', 'twentytwentyone' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Community', 'twentytwentyone' ),
				'featured_image'        => _x( 'Featured Image', 'community', 'twentytwentyone' ),
				'set_featured_image'    => _x( 'Set featured image', 'community', 'twentytwentyone' ),
				'remove_featured_image' => _x( 'Remove featured image', 'community', 'twentytwentyone' ),
				'use_featured_image'    => _x( 'Use as featured image', 'community', 'twentytwentyone' ),
				'filter_items_list'     => __( 'Filter Communities list', 'twentytwentyone' ),
				'items_list_navigation' => __( 'Communities list navigation', 'twentytwentyone' ),
				'items_list'            => __( 'Communities list', 'twentytwentyone' ),
				'new_item'              => __( 'New Community', 'twentytwentyone' ),
				'add_new'               => __( 'Add New', 'twentytwentyone' ),
				'add_new_item'          => __( 'Add New Community', 'twentytwentyone' ),
				'edit_item'             => __( 'Edit Community', 'twentytwentyone' ),
				'view_item'             => __( 'View Community', 'twentytwentyone' ),
				'view_items'            => __( 'View Communities', 'twentytwentyone' ),
				'search_items'          => __( 'Search Communities', 'twentytwentyone' ),
				'not_found'             => __( 'No Communities found', 'twentytwentyone' ),
				'not_found_in_trash'    => __( 'No Communities found in trash', 'twentytwentyone' ),
				'parent_item_colon'     => __( 'Parent Community:', 'twentytwentyone' ),
				'menu_name'             => __( 'Communities', 'twentytwentyone' ),
			],
			'public'                => true,
			'hierarchical'          => false,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => [ 'title', 'editor' ],
			'has_archive'           => true,
			'rewrite'               => true,
			'query_var'             => true,
			'menu_position'         => 4,
			'menu_icon'             => 'dashicons-admin-post',
			'show_in_rest'          => null,
		]
	);

}

add_action( 'init', 'community_init' );

/**
 * Sets the post updated messages for the `community` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `community` post type.
 */
function community_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['community'] = [
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Community updated. <a target="_blank" href="%s">View Community</a>', 'twentytwentyone' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'twentytwentyone' ),
		3  => __( 'Custom field deleted.', 'twentytwentyone' ),
		4  => __( 'Community updated.', 'twentytwentyone' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Community restored to revision from %s', 'twentytwentyone' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Community published. <a href="%s">View Community</a>', 'twentytwentyone' ), esc_url( $permalink ) ),
		7  => __( 'Community saved.', 'twentytwentyone' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Community submitted. <a target="_blank" href="%s">Preview Community</a>', 'twentytwentyone' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Community scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Community</a>', 'twentytwentyone' ), date_i18n( __( 'M j, Y @ G:i', 'twentytwentyone' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Community draft updated. <a target="_blank" href="%s">Preview Community</a>', 'twentytwentyone' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	];

	return $messages;
}

add_filter( 'post_updated_messages', 'community_updated_messages' );

/**
 * Sets the bulk post updated messages for the `community` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `community` post type.
 */
function community_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages['community'] = [
		/* translators: %s: Number of Communities. */
		'updated'   => _n( '%s Community updated.', '%s Communities updated.', $bulk_counts['updated'], 'twentytwentyone' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Community not updated, somebody is editing it.', 'twentytwentyone' ) :
						/* translators: %s: Number of Communities. */
						_n( '%s Community not updated, somebody is editing it.', '%s Communities not updated, somebody is editing them.', $bulk_counts['locked'], 'twentytwentyone' ),
		/* translators: %s: Number of Communities. */
		'deleted'   => _n( '%s Community permanently deleted.', '%s Communities permanently deleted.', $bulk_counts['deleted'], 'twentytwentyone' ),
		/* translators: %s: Number of Communities. */
		'trashed'   => _n( '%s Community moved to the Trash.', '%s Communities moved to the Trash.', $bulk_counts['trashed'], 'twentytwentyone' ),
		/* translators: %s: Number of Communities. */
		'untrashed' => _n( '%s Community restored from the Trash.', '%s Communities restored from the Trash.', $bulk_counts['untrashed'], 'twentytwentyone' ),
	];

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', 'community_bulk_updated_messages', 10, 2 );
