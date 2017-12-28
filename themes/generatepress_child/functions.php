<?php
/**
 * Generate child theme functions and definitions
 *
 * @package Generate
 */
 
/**
 * Prevent WordPress from adding <p> tags automatically.
 */
remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );