<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'generate_backgrounds_secondary_nav_customizer' ) ) :
/**
 * Adds our Secondary Nav background image options
 *
 * These options are in their own function so we can hook it in late to
 * make sure Secondary Nav is activated.
 *
 * 1000 priority is there to make sure Secondary Nav is registered (999)
 * as we check to see if the layout control exists.
 *
 * Secondary Nav now uses 100 as a priority.
 */
add_action( 'customize_register', 'generate_backgrounds_secondary_nav_customizer', 1000 );
function generate_backgrounds_secondary_nav_customizer( $wp_customize )
{
	
	// Bail if we don't have our defaults
	if ( ! function_exists( 'generate_secondary_nav_get_defaults' ) ) {
		return;
	}
	
	// Make sure Secondary Nav is activated
	if ( ! $wp_customize->get_section( 'secondary_nav_section' ) ) {
		return;
	}
	
	// Get our defaults
	$defaults = generate_secondary_nav_get_defaults();
	
	// Get our controls
	require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

	// Add our section
	$wp_customize->add_section(
		'secondary_bg_images_section',
		array(
			'title' => __( 'Secondary Navigation','backgrounds' ),
			'capability' => 'edit_theme_options',
			'description' => '',
			'panel' => 'generate_backgrounds_panel',
			'priority' => 21
		)
	);

	// Background
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[nav_image]', array(
			'default' => $defaults['nav_image'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw'
		)
	);
	
	$wp_customize->add_control( 
		new WP_Customize_Image_Control( 
			$wp_customize, 
			'generate_secondary_backgrounds-nav-image', 
			array(
				'section'    => 'secondary_bg_images_section',
				'settings'   => 'generate_secondary_nav_settings[nav_image]',
				'priority' => 750,
				'label' => __( 'Navigation','backgrounds' ), 
			)
		)
	);
	
	// Repeat
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[nav_repeat]',
		array(
			'default' => $defaults['nav_repeat'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[nav_repeat]',
		array(
			'type' => 'select',
			'section' => 'secondary_bg_images_section',
			'choices' => array(
				'' => __( 'Repeat','backgrounds' ),
				'repeat-x' => __( 'Repeat x','backgrounds' ),
				'repeat-y' => __( 'Repeat y','backgrounds' ),
				'no-repeat' => __( 'No Repeat','backgrounds' )
			),
			'settings' => 'generate_secondary_nav_settings[nav_repeat]',
			'priority' => 800
		)
	);
	
	// Item background
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[nav_item_image]', array(
			'default' => $defaults['nav_item_image'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw'
		)
	);
	
	$wp_customize->add_control( 
		new WP_Customize_Image_Control( 
			$wp_customize, 
			'generate_secondary_backgrounds-nav-item-image', 
			array(
				'section'    => 'secondary_bg_images_section',
				'settings'   => 'generate_secondary_nav_settings[nav_item_image]',
				'priority' => 950,
				'label' => __( 'Navigation Item','backgrounds' ),
			)
		)
	);
	
	// Item repeat
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[nav_item_repeat]',
		array(
			'default' => $defaults['nav_item_repeat'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[nav_item_repeat]',
		array(
			'type' => 'select',
			'section' => 'secondary_bg_images_section',
			'choices' => array(
				'' => __( 'Repeat','backgrounds' ),
				'repeat-x' => __( 'Repeat x','backgrounds' ),
				'repeat-y' => __( 'Repeat y','backgrounds' ),
				'no-repeat' => __( 'No Repeat','backgrounds' )
			),
			'settings' => 'generate_secondary_nav_settings[nav_item_repeat]',
			'priority' => 1000
		)
	);
	
	// Item hover
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[nav_item_hover_image]', array(
			'default' => $defaults['nav_item_hover_image'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw'
		)
	);
	
	$wp_customize->add_control( 
		new WP_Customize_Image_Control( 
			$wp_customize, 
			'generate_secondary_backgrounds-nav-item-hover-image', 
			array(
				'section'    => 'secondary_bg_images_section',
				'settings'   => 'generate_secondary_nav_settings[nav_item_hover_image]',
				'priority' => 1150,
				'label' => __( 'Navigation Item Hover','backgrounds' ), 
			)
		)
	);
	
	// Item hover repeat
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[nav_item_hover_repeat]',
		array(
			'default' => $defaults['nav_item_hover_repeat'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[nav_item_hover_repeat]',
		array(
			'type' => 'select',
			'section' => 'secondary_bg_images_section',
			'choices' => array(
				'' => __( 'Repeat','backgrounds' ),
				'repeat-x' => __( 'Repeat x','backgrounds' ),
				'repeat-y' => __( 'Repeat y','backgrounds' ),
				'no-repeat' => __( 'No Repeat','backgrounds' )
			),
			'settings' => 'generate_secondary_nav_settings[nav_item_hover_repeat]',
			'priority' => 1200
		)
	);
	
	// Current background
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[nav_item_current_image]', array(
			'default' => $defaults['nav_item_current_image'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw'
		)
	);
	
	$wp_customize->add_control( 
		new WP_Customize_Image_Control( 
			$wp_customize, 
			'generate_secondary_backgrounds-nav-item-current-image', 
			array(
				'section'    => 'secondary_bg_images_section',
				'settings'   => 'generate_secondary_nav_settings[nav_item_current_image]',
				'priority' => 1350,
				'label' => __( 'Navigation Item Current','backgrounds' ), 
			)
		)
	);
	
	// Current repeat
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[nav_item_current_repeat]',
		array(
			'default' => $defaults['nav_item_current_repeat'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[nav_item_current_repeat]',
		array(
			'type' => 'select',
			'section' => 'secondary_bg_images_section',
			'choices' => array(
				'' => __( 'Repeat','backgrounds' ),
				'repeat-x' => __( 'Repeat x','backgrounds' ),
				'repeat-y' => __( 'Repeat y','backgrounds' ),
				'no-repeat' => __( 'No Repeat','backgrounds' )
			),
			'settings' => 'generate_secondary_nav_settings[nav_item_current_repeat]',
			'priority' => 1400
		)
	);
	
	// Sub-navigation section
	$wp_customize->add_section(
		'secondary_subnav_bg_images_section',
		array(
			'title' => __( 'Secondary Sub-Navigation','backgrounds' ),
			'capability' => 'edit_theme_options',
			'description' => '',
			'panel' => 'generate_backgrounds_panel',
			'priority' => 22
		)
	);
	
	// Item background
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[sub_nav_item_image]', array(
			'default' => $defaults['sub_nav_item_image'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw'
		)
	);
	
	$wp_customize->add_control( 
		new WP_Customize_Image_Control( 
			$wp_customize, 
			'generate_secondary_backgrounds-sub-nav-item-image', 
			array(
				'section'    => 'secondary_subnav_bg_images_section',
				'settings'   => 'generate_secondary_nav_settings[sub_nav_item_image]',
				'priority' => 1700,
				'label' => __( 'Sub-Navigation Item','backgrounds' ), 
			)
		)
	);
	
	// Item repeat
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[sub_nav_item_repeat]',
		array(
			'default' => $defaults['sub_nav_item_repeat'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[sub_nav_item_repeat]',
		array(
			'type' => 'select',
			'section' => 'secondary_subnav_bg_images_section',
			'choices' => array(
				'' => __( 'Repeat','backgrounds' ),
				'repeat-x' => __( 'Repeat x','backgrounds' ),
				'repeat-y' => __( 'Repeat y','backgrounds' ),
				'no-repeat' => __( 'No Repeat','backgrounds' )
			),
			'settings' => 'generate_secondary_nav_settings[sub_nav_item_repeat]',
			'priority' => 1800
		)
	);
	
	// Item hover
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[sub_nav_item_hover_image]', array(
			'default' => $defaults['sub_nav_item_hover_image'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw'
		)
	);
	
	$wp_customize->add_control( 
		new WP_Customize_Image_Control( 
			$wp_customize, 
			'generate_secondary_backgrounds-sub-nav-item-hover-image', 
			array(
				'section'    => 'secondary_subnav_bg_images_section',
				'settings'   => 'generate_secondary_nav_settings[sub_nav_item_hover_image]',
				'priority' => 2000,
				'label' => __( 'Sub-Navigation Item Hover','backgrounds' ), 
			)
		)
	);
	
	// Item hover repeat
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[sub_nav_item_hover_repeat]',
		array(
			'default' => $defaults['sub_nav_item_hover_repeat'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[sub_nav_item_hover_repeat]',
		array(
			'type' => 'select',
			'section' => 'secondary_subnav_bg_images_section',
			'choices' => array(
				'' => __( 'Repeat','backgrounds' ),
				'repeat-x' => __( 'Repeat x','backgrounds' ),
				'repeat-y' => __( 'Repeat y','backgrounds' ),
				'no-repeat' => __( 'No Repeat','backgrounds' )
			),
			'settings' => 'generate_secondary_nav_settings[sub_nav_item_hover_repeat]',
			'priority' => 2100
		)
	);
	
	// Current background
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[sub_nav_item_current_image]', array(
			'default' => $defaults['sub_nav_item_current_image'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw'
		)
	);
	
	$wp_customize->add_control( 
		new WP_Customize_Image_Control( 
			$wp_customize, 
			'generate_secondary_backgrounds-sub-nav-item-current-image', 
			array(
				'section'    => 'secondary_subnav_bg_images_section',
				'settings'   => 'generate_secondary_nav_settings[sub_nav_item_current_image]',
				'priority' => 2300,
				'label' => __( 'Sub-Navigation Item Current','backgrounds' ), 
			)
		)
	);
	
	// Current background repeat
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[sub_nav_item_current_repeat]',
		array(
			'default' => $defaults['sub_nav_item_current_repeat'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[sub_nav_item_current_repeat]',
		array(
			'type' => 'select',
			'section' => 'secondary_subnav_bg_images_section',
			'choices' => array(
				'' => __( 'Repeat','backgrounds' ),
				'repeat-x' => __( 'Repeat x','backgrounds' ),
				'repeat-y' => __( 'Repeat y','backgrounds' ),
				'no-repeat' => __( 'No Repeat','backgrounds' )
			),
			'settings' => 'generate_secondary_nav_settings[sub_nav_item_current_repeat]',
			'priority' => 2400
		)
	);
}
endif;