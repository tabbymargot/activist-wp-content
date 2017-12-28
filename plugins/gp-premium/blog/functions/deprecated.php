<?php
defined( 'WPINC' ) or die;

if ( ! function_exists( 'generate_blog_post_image' ) ) :
/**
 * Build our featured image HTML
 *
 * @deprecated 1.5
 */
function generate_blog_post_image() {
	// No longer needed
}
endif;

if ( ! function_exists( 'generate_get_masonry_post_width' ) ) :
/**
 * Set our masonry post width
 *
 * @deprecated 1.5
 */
function generate_get_masonry_post_width() {
	// Get our global variables
	global $post, $wp_query;

	// Figure out which page we're on
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	// Figure out if we're on the most recent post or not
	$most_recent = ( $wp_query->current_post == 0 && $paged == 1 ) ? true : false;

	// Get our Customizer options
	$generate_blog_settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	$masonry_post_width = $generate_blog_settings['masonry_width'];

	// Get our post meta option
	$stored_meta = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate-blog-post-class', true ) : '';

	// If our post meta option is set, use it
	// Or else, use our Customizer option
	if ( '' !== $stored_meta ) {
		if ( 'width4' == $stored_meta && 'width4' == $generate_blog_settings['masonry_width'] ) {
			$masonry_post_width = 'medium';
		} else {
			$masonry_post_width = $stored_meta;
		}
	}

	// Return our width class
	return apply_filters( 'generate_masonry_post_width', $masonry_post_width );
}
endif;

if ( ! function_exists( 'generate_blog_add_post_class_meta_box' ) ) :
/**
 * Create our masonry meta box
 *
 * @deprecated 1.5
 */
function generate_blog_add_post_class_meta_box() {
	$generate_blog_settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( 'true' !== $generate_blog_settings['masonry'] ) {
		return;
	}

	$post_types = apply_filters( 'generate_blog_masonry_metabox', array( 'post' ) );

	add_meta_box
	(
		'generate_blog_post_class_meta_box', // $id
		__('Masonry Post Width','generate-blog'), // $title
		'generate_blog_show_post_class_metabox', // $callback
		$post_types, // $page
		'side', // $context
		'default' // $priority
	);
}
endif;

if ( ! function_exists( 'generate_blog_show_post_class_metabox' ) ) :
/**
 * Outputs the content of the metabox
 * @deprecated 1.5
 */
function generate_blog_show_post_class_metabox( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'generate_blog_post_class_nonce' );
	$stored_meta = get_post_meta( $post->ID );

	// Set defaults to avoid PHP notices
	if ( isset($stored_meta['_generate-blog-post-class'][0]) ) {
		$stored_meta['_generate-blog-post-class'][0] = $stored_meta['_generate-blog-post-class'][0];
	} else {
		$stored_meta['_generate-blog-post-class'][0] = '';
	}
	?>
	<p>
		<label for="_generate-blog-post-class" class="example-row-title"><strong><?php _e( 'Masonry Post Width', 'generate-blog' );?></strong></label><br />
		<select name="_generate-blog-post-class" id="_generate-blog-post-class">
			<option value="" <?php selected( $stored_meta['_generate-blog-post-class'][0], '' ); ?>><?php _e( 'Global setting', 'generate-blog' );?></option>
			<option value="width2" <?php selected( $stored_meta['_generate-blog-post-class'][0], 'width2' ); ?>><?php _e( 'Small', 'generate-blog' );?></option>
			<option value="width4" <?php selected( $stored_meta['_generate-blog-post-class'][0], 'width4' ); ?>><?php _e( 'Medium', 'generate-blog' );?></option>
			<option value="width6" <?php selected( $stored_meta['_generate-blog-post-class'][0], 'width6' ); ?>><?php _e( 'Large', 'generate-blog' );?></option>
		</select>
	</p>
	<?php
}
endif;

if ( ! function_exists( 'generate_blog_save_post_class_meta' ) ) :
/**
 * Saves post class meta data
 *
 * @param int $post_id The post ID being saved
 * @deprecated 1.5
 */
function generate_blog_save_post_class_meta( $post_id ) {
	// Checks save status
	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST[ 'generate_blog_post_class_nonce' ] ) && wp_verify_nonce( $_POST[ 'generate_blog_post_class_nonce' ], basename( __FILE__ ) ) ) ? true : false;

	// Exits script depending on save status
	if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
		return;
	}

	// Checks for input and saves if needed
	if ( isset( $_POST[ '_generate-blog-post-class' ] ) ) {
		update_post_meta( $post_id, '_generate-blog-post-class', sanitize_text_field( $_POST[ '_generate-blog-post-class' ] ) );
	}
}
endif;

if ( ! function_exists( 'generate_blog_get_next_posts_url' ) ) :
/**
 * Get the URL of the next page
 * This is for the AJAX load more function
 */
function generate_blog_get_next_posts_url( $max_page = 0 ) {
	global $paged, $wp_query;

	if ( ! $max_page ) {
		$max_page = $wp_query->max_num_pages;
	}

	if ( ! $paged ) {
		$paged = 1;
	}

	$nextpage = intval( $paged ) + 1;

	if ( ! is_single() && ( $nextpage <= $max_page ) ) {
		return next_posts( $max_page, false );
	}
}
endif;