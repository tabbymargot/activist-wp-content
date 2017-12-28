<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'generate_colors_wc_customizer' ) ) :
/**
 * Adds our WooCommerce color options
 */
add_action( 'customize_register', 'generate_colors_wc_customizer', 100 );
function generate_colors_wc_customizer( $wp_customize ) {
	
	// Bail if WooCommerce isn't activated
	if ( ! $wp_customize->get_section( 'generate_woocommerce_colors' ) ) {
		return;
	}
	
	if ( ! function_exists( 'generate_get_color_defaults' ) ) {
		return;
	}
	
	// Add our controls
	require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';
	
	// Get our defaults
	$defaults = generate_get_color_defaults();

	// Add control types so controls can be built using JS
	if ( method_exists( $wp_customize, 'register_control_type' ) ) {
		$wp_customize->register_control_type( 'GeneratePress_Alpha_Color_Customize_Control' );
	}
	
	// Get our palettes
	$palettes = generate_get_default_color_palettes();
	
	$wp_customize->add_setting(
		'generate_settings[wc_product_title_color]', array(
			'default' => $defaults['wc_product_title_color'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[wc_product_title_color]', 
			array(
				'label' => __( 'Product title','generate-colors' ), 
				'section' => 'generate_woocommerce_colors',
				'settings' => 'generate_settings[wc_product_title_color]',
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_product_title_color_hover]', array(
			'default' => $defaults['wc_product_title_color_hover'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[wc_product_title_color_hover]', 
			array(
				'label' => __( 'Product title hover','generate-colors' ), 
				'section' => 'generate_woocommerce_colors',
				'settings' => 'generate_settings[wc_product_title_color_hover]',
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_alt_button_background]',
		array(
			'default'     => $defaults['wc_alt_button_background'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[wc_alt_button_background]',
			array(
				'label'     => __( 'Alt button background', 'generate-colors' ),
				'section'   => 'generate_woocommerce_colors',
				'settings'  => 'generate_settings[wc_alt_button_background]',
				'palette'   => $palettes,
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_alt_button_background_hover]',
		array(
			'default'     => $defaults['wc_alt_button_background_hover'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[wc_alt_button_background_hover]',
			array(
				'label'     => __( 'Alt button background hover', 'generate-colors' ),
				'section'   => 'generate_woocommerce_colors',
				'settings'  => 'generate_settings[wc_alt_button_background_hover]',
				'palette'   => $palettes,
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_alt_button_text]', array(
			'default' => $defaults['wc_alt_button_text'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[wc_alt_button_text]', 
			array(
				'label' => __( 'Alt button text','generate-colors' ), 
				'section' => 'generate_woocommerce_colors',
				'settings' => 'generate_settings[wc_alt_button_text]',
			)
		)
	);
	

	$wp_customize->add_setting(
		'generate_settings[wc_alt_button_text_hover]', array(
			'default' => $defaults['wc_alt_button_text_hover'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[wc_alt_button_text_hover]', 
			array(
				'label' => __( 'Alt button text hover','generate-colors' ), 
				'section' => 'generate_woocommerce_colors',
				'settings' => 'generate_settings[wc_alt_button_text_hover]',
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_rating_stars]',
		array(
			'default'     => $defaults['wc_rating_stars'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => '',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[wc_rating_stars]',
			array(
				'label'     => __( 'Star ratings', 'generate-colors' ),
				'section'   => 'generate_woocommerce_colors',
				'settings'  => 'generate_settings[wc_rating_stars]',
				'palette'   => $palettes,
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_rating_stars]',
		array(
			'default'     => $defaults['wc_rating_stars'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[wc_rating_stars]',
			array(
				'label'     => __( 'Star ratings', 'generate-colors' ),
				'section'   => 'generate_woocommerce_colors',
				'settings'  => 'generate_settings[wc_rating_stars]',
				'palette'   => $palettes,
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_sale_sticker_background]',
		array(
			'default'     => $defaults['wc_sale_sticker_background'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[wc_sale_sticker_background]',
			array(
				'label'     => __( 'Sale sticker background', 'generate-colors' ),
				'section'   => 'generate_woocommerce_colors',
				'settings'  => 'generate_settings[wc_sale_sticker_background]',
				'palette'   => $palettes,
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_sale_sticker_text]', array(
			'default' => $defaults['wc_sale_sticker_text'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[wc_sale_sticker_text]', 
			array(
				'label' => __( 'Sale sticker text','generate-colors' ), 
				'section' => 'generate_woocommerce_colors',
				'settings' => 'generate_settings[wc_sale_sticker_text]',
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_price_color]', array(
			'default' => $defaults['wc_price_color'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[wc_price_color]', 
			array(
				'label' => __( 'Price','generate-colors' ), 
				'section' => 'generate_woocommerce_colors',
				'settings' => 'generate_settings[wc_price_color]',
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_product_tab]', array(
			'default' => $defaults['wc_product_tab'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[wc_product_tab]', 
			array(
				'label' => __( 'Product tab text','generate-colors' ), 
				'section' => 'generate_woocommerce_colors',
				'settings' => 'generate_settings[wc_product_tab]',
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_product_tab_highlight]', array(
			'default' => $defaults['wc_product_tab_highlight'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[wc_product_tab_highlight]', 
			array(
				'label' => __( 'Product tab active','generate-colors' ), 
				'section' => 'generate_woocommerce_colors',
				'settings' => 'generate_settings[wc_product_tab_highlight]',
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_success_message_background]',
		array(
			'default'     => $defaults['wc_success_message_background'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[wc_success_message_background]',
			array(
				'label'     => __( 'Success message background', 'generate-colors' ),
				'section'   => 'generate_woocommerce_colors',
				'settings'  => 'generate_settings[wc_success_message_background]',
				'palette'   => $palettes,
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_success_message_text]', array(
			'default' => $defaults['wc_success_message_text'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[wc_success_message_text]', 
			array(
				'label' => __( 'Success message text','generate-colors' ), 
				'section' => 'generate_woocommerce_colors',
				'settings' => 'generate_settings[wc_success_message_text]',
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_info_message_background]',
		array(
			'default'     => $defaults['wc_info_message_background'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[wc_info_message_background]',
			array(
				'label'     => __( 'Info message background', 'generate-colors' ),
				'section'   => 'generate_woocommerce_colors',
				'settings'  => 'generate_settings[wc_info_message_background]',
				'palette'   => $palettes,
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_info_message_text]', array(
			'default' => $defaults['wc_info_message_text'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[wc_info_message_text]', 
			array(
				'label' => __( 'Info message text','generate-colors' ), 
				'section' => 'generate_woocommerce_colors',
				'settings' => 'generate_settings[wc_info_message_text]',
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_error_message_background]',
		array(
			'default'     => $defaults['wc_error_message_background'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[wc_error_message_background]',
			array(
				'label'     => __( 'Error message background', 'generate-colors' ),
				'section'   => 'generate_woocommerce_colors',
				'settings'  => 'generate_settings[wc_error_message_background]',
				'palette'   => $palettes,
			)
		)
	);
	
	$wp_customize->add_setting(
		'generate_settings[wc_error_message_text]', array(
			'default' => $defaults['wc_error_message_text'],
			'type' => 'option', 
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[wc_error_message_text]', 
			array(
				'label' => __( 'Error message text','generate-colors' ), 
				'section' => 'generate_woocommerce_colors',
				'settings' => 'generate_settings[wc_error_message_text]',
			)
		)
	);
	
}
endif;