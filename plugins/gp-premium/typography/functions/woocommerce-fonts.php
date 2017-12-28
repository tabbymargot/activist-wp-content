<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'generate_typography_wc_customizer' ) ) :
/**
 * Adds our WooCommerce color options
 */
add_action( 'customize_register', 'generate_typography_wc_customizer', 100 );
function generate_typography_wc_customizer( $wp_customize ) {
	
	// Bail if we don't have our defaults function
	if ( ! function_exists( 'generate_get_default_fonts' ) ) {
		return;
	}
	
	// Get our custom controls
	require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';
	
	// Get our defaults
	$defaults = generate_get_default_fonts();
	 
	// Register our custom control types
	if ( method_exists( $wp_customize,'register_control_type' ) ) {
		$wp_customize->register_control_type( 'GeneratePress_Pro_Range_Slider_Control' );
		$wp_customize->register_control_type( 'GeneratePress_Pro_Typography_Customize_Control' );
	}

	// Bail if WooCommerce isn't activated
	if ( ! $wp_customize->get_section( 'generate_woocommerce_typography' ) ) {
		return;
	}
	 
	// WooCommerce
	$wp_customize->add_setting( 
		'generate_settings[wc_product_title_font_weight]', 
		array(
			'default' => $defaults['wc_product_title_font_weight'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);

	// Text transform
	$wp_customize->add_setting( 
		'generate_settings[wc_product_title_font_transform]', 
		array(
			'default' => $defaults['wc_product_title_font_transform'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Pro_Typography_Customize_Control(
			$wp_customize,
			'google_wc_product_title_control', 
			array(
				'label' => __( 'Product titles', 'generate-typography' ), 
				'section' => 'generate_woocommerce_typography',
				'settings' => array( 
					'weight' => 'generate_settings[wc_product_title_font_weight]',
					'transform' => 'generate_settings[wc_product_title_font_transform]',
				),
			)
		)
	);

	// Font size
	$wp_customize->add_setting( 
		'generate_settings[wc_product_title_font_size]', 
		array(
			'default' => $defaults['wc_product_title_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_setting( 
		'generate_settings[mobile_wc_product_title_font_size]', 
		array(
			'default' => $defaults['mobile_wc_product_title_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[wc_product_title_font_size]', 
			array(
				'description' => __( 'Font size', 'generate-typography' ), 
				'section' => 'generate_woocommerce_typography',
				'priority' => 240,
				'settings' => array( 
					'desktop' => 'generate_settings[wc_product_title_font_size]',
					'mobile' => 'generate_settings[mobile_wc_product_title_font_size]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 10,
						'max' => 80,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
					'mobile' => array(
						'min' => 10,
						'max' => 80,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);

	// Font size
	$wp_customize->add_setting( 
		'generate_settings[wc_related_product_title_font_size]', 
		array(
			'default' => $defaults['wc_related_product_title_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[wc_related_product_title_font_size]', 
			array(
				'description' => __( 'Related/upsell title font size', 'generate-typography' ), 
				'section' => 'generate_woocommerce_typography',
				'priority' => 240,
				'settings' => array( 
					'desktop' => 'generate_settings[wc_related_product_title_font_size]',
					'mobile' => 'generate_settings[mobile_wc_product_title_font_size]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 10,
						'max' => 80,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
}
endif;