<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

// Add our Secondary Nav settings
require_once trailingslashit( dirname(__FILE__) ) . 'secondary-nav-colors.php';

// Add our WooCommerce settings
require_once trailingslashit( dirname(__FILE__) ) . 'woocommerce-colors.php';

if ( ! function_exists( 'generate_colors_customize_register' ) ) :
/**
 * Add our Customizer options
 * @since 0.1
 */
add_action( 'customize_register', 'generate_colors_customize_register' );
function generate_colors_customize_register( $wp_customize ) {

	// Bail if we don't have our color defaults
	if ( ! function_exists( 'generate_get_color_defaults' ) )
		return;

	// Add our controls
	require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

	// Get our defaults
	$defaults = generate_get_color_defaults();

	// Add control types so controls can be built using JS
	if ( method_exists( $wp_customize, 'register_control_type' ) ) {
		$wp_customize->register_control_type( 'GeneratePress_Alpha_Color_Customize_Control' );
		$wp_customize->register_control_type( 'GeneratePress_Title_Customize_Control' );
	}

	// Get our palettes
	$palettes = generate_get_default_color_palettes();

	// Add our Colors panel
	if ( class_exists( 'WP_Customize_Panel' ) ) :
		$wp_customize->add_panel( 'generate_colors_panel', array(
			'priority'       => 30,
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => __( 'Colors','generate-colors' ),
			'description'    => '',
		) );
	endif;

	// Add Top Bar Colors section
	if ( isset( $defaults[ 'top_bar_background_color' ] ) && function_exists( 'generate_is_top_bar_active' ) ) {
		$wp_customize->add_section(
			'generate_top_bar_colors',
			array(
				'title' => __( 'Top Bar', 'generate-colors' ),
				'capability' => 'edit_theme_options',
				'priority' => 40,
				'panel' => 'generate_colors_panel'
			)
		);

		$wp_customize->add_setting(
			'generate_settings[top_bar_background_color]',
			array(
				'default'     => $defaults['top_bar_background_color'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[top_bar_background_color]',
				array(
					'label'     => __( 'Background', 'generatepress' ),
					'section'   => 'generate_top_bar_colors',
					'settings'  => 'generate_settings[top_bar_background_color]',
					'palette'   => $palettes,
					'show_opacity'  => true,
					'priority' => 1,
					'active_callback' => 'generate_is_top_bar_active'
				)
			)
		);

		// Add color settings
		$top_bar_colors = array();
		$top_bar_colors[] = array(
			'slug' => 'top_bar_text_color',
			'default' => $defaults['top_bar_text_color'],
			'label' => __('Text', 'generate-colors'),
			'priority' => 2
		);
		$top_bar_colors[] = array(
			'slug' => 'top_bar_link_color',
			'default' => $defaults['top_bar_link_color'],
			'label' => __('Link', 'generate-colors'),
			'priority' => 3
		);
		$top_bar_colors[] = array(
			'slug' => 'top_bar_link_color_hover',
			'default' => $defaults['top_bar_link_color_hover'],
			'label' => __('Link Hover', 'generate-colors'),
			'priority' => 4
		);

		foreach( $top_bar_colors as $color ) {
			$wp_customize->add_setting(
				'generate_settings[' . $color['slug'] . ']', array(
					'default' => $color['default'],
					'type' => 'option',
					'capability' => 'edit_theme_options',
					'sanitize_callback' => 'generate_premium_sanitize_hex_color',
					'transport' => 'postMessage'
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'generate_top_bar_colors',
						'settings' => 'generate_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
						'palette'   => $palettes,
						'active_callback' => 'generate_is_top_bar_active'
					)
				)
			);
		}
	}

	// Add Header Colors section
	$wp_customize->add_section(
		'header_color_section',
		array(
			'title' => __( 'Header', 'generate-colors' ),
			'capability' => 'edit_theme_options',
			'priority' => 50,
			'panel' => 'generate_colors_panel'
		)
	);

	$wp_customize->add_setting(
		'generate_settings[header_background_color]',
		array(
			'default'     => $defaults['header_background_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[header_background_color]',
			array(
				'label'     => __( 'Background', 'generate-colors' ),
				'section'   => 'header_color_section',
				'settings'  => 'generate_settings[header_background_color]',
				'palette'   => $palettes,
				'show_opacity'  => true,
				'priority' => 1
			)
		)
	);

	// Add color settings
	$header_colors = array();
	$header_colors[] = array(
		'slug' => 'header_text_color',
		'default' => $defaults['header_text_color'],
		'label' => __('Text', 'generate-colors'),
		'priority' => 2
	);
	$header_colors[] = array(
		'slug' => 'header_link_color',
		'default' => $defaults['header_link_color'],
		'label' => __('Link', 'generate-colors'),
		'priority' => 3
	);
	$header_colors[] = array(
		'slug' => 'header_link_hover_color',
		'default' => $defaults['header_link_hover_color'],
		'label' => __('Link Hover', 'generate-colors'),
		'priority' => 4
	);
	$header_colors[] = array(
		'slug' => 'site_title_color',
		'default' => $defaults['site_title_color'],
		'label' => __('Site Title', 'generate-colors'),
		'priority' => 5
	);
	$header_colors[] = array(
		'slug' => 'site_tagline_color',
		'default' => $defaults['site_tagline_color'],
		'label' => __('Tagline', 'generate-colors'),
		'priority' => 6
	);

	foreach( $header_colors as $color ) {
		$wp_customize->add_setting(
			'generate_settings[' . $color['slug'] . ']', array(
				'default' => $color['default'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$color['slug'],
				array(
					'label' => $color['label'],
					'section' => 'header_color_section',
					'settings' => 'generate_settings[' . $color['slug'] . ']',
					'priority' => $color['priority'],
					'palette'   => $palettes,
				)
			)
		);
	}

	// Add Navigation section
	$wp_customize->add_section(
		'navigation_color_section',
		array(
			'title' => __( 'Primary Navigation', 'generate-colors' ),
			'capability' => 'edit_theme_options',
			'priority' => 60,
			'panel' => 'generate_colors_panel'
		)
	);

	$wp_customize->add_setting(
		'generate_settings[navigation_background_color]',
		array(
			'default'     => $defaults['navigation_background_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[navigation_background_color]',
			array(
				'label'     => __( 'Background', 'generate-colors' ),
				'section'   => 'navigation_color_section',
				'settings'  => 'generate_settings[navigation_background_color]',
				'palette'   => $palettes,
				'priority' => 1
			)
		)
	);

	$wp_customize->add_setting(
		'generate_settings[navigation_background_hover_color]',
		array(
			'default'     => $defaults['navigation_background_hover_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[navigation_background_hover_color]',
			array(
				'label'     => __( 'Background Hover', 'generate-colors' ),
				'section'   => 'navigation_color_section',
				'settings'  => 'generate_settings[navigation_background_hover_color]',
				'palette'   => $palettes,
				'priority' => 3
			)
		)
	);

	$wp_customize->add_setting(
		'generate_settings[navigation_background_current_color]',
		array(
			'default'     => $defaults['navigation_background_current_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[navigation_background_current_color]',
			array(
				'label'     => __( 'Background Current', 'generate-colors' ),
				'section'   => 'navigation_color_section',
				'settings'  => 'generate_settings[navigation_background_current_color]',
				'palette'   => $palettes,
				'priority' => 5
			)
		)
	);

	// Add color settings
	$navigation_colors = array();
	$navigation_colors[] = array(
		'slug'=>'navigation_text_color',
		'default' => $defaults['navigation_text_color'],
		'label' => __('Text', 'generate-colors'),
		'priority' => 2
	);
	$navigation_colors[] = array(
		'slug'=>'navigation_text_hover_color',
		'default' => $defaults['navigation_text_hover_color'],
		'label' => __('Text Hover', 'generate-colors'),
		'priority' => 4
	);
	$navigation_colors[] = array(
		'slug'=>'navigation_text_current_color',
		'default' => $defaults['navigation_text_current_color'],
		'label' => __('Text Current', 'generate-colors'),
		'priority' => 6
	);

	foreach( $navigation_colors as $color ) {
		$wp_customize->add_setting(
			'generate_settings[' . $color['slug'] . ']', array(
				'default' => $color['default'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$color['slug'],
				array(
					'label' => $color['label'],
					'section' => 'navigation_color_section',
					'settings' => 'generate_settings[' . $color['slug'] . ']',
					'priority' => $color['priority']
				)
			)
		);
	}

	// Add Sub-Navigation section
	$wp_customize->add_section(
		'subnavigation_color_section',
		array(
			'title' => __( 'Primary Sub-Navigation', 'generate-colors' ),
			'capability' => 'edit_theme_options',
			'priority' => 70,
			'panel' => 'generate_colors_panel'
		)
	);

	$wp_customize->add_setting(
		'generate_settings[subnavigation_background_color]',
		array(
			'default'     => $defaults['subnavigation_background_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[subnavigation_background_color]',
			array(
				'label'     => __( 'Background', 'generate-colors' ),
				'section'   => 'subnavigation_color_section',
				'settings'  => 'generate_settings[subnavigation_background_color]',
				'palette'   => $palettes,
				'priority' => 1
			)
		)
	);

	$wp_customize->add_setting(
		'generate_settings[subnavigation_background_hover_color]',
		array(
			'default'     => $defaults['subnavigation_background_hover_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[subnavigation_background_hover_color]',
			array(
				'label'     => __( 'Background Hover', 'generate-colors' ),
				'section'   => 'subnavigation_color_section',
				'settings'  => 'generate_settings[subnavigation_background_hover_color]',
				'palette'   => $palettes,
				'priority' => 3
			)
		)
	);

	$wp_customize->add_setting(
		'generate_settings[subnavigation_background_current_color]',
		array(
			'default'     => $defaults['subnavigation_background_current_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[subnavigation_background_current_color]',
			array(
				'label'     => __( 'Background Current', 'generate-colors' ),
				'section'   => 'subnavigation_color_section',
				'settings'  => 'generate_settings[subnavigation_background_current_color]',
				'palette'   => $palettes,
				'priority' => 5
			)
		)
	);

	// Add color settings
	$subnavigation_colors = array();
	$subnavigation_colors[] = array(
		'slug'=>'subnavigation_text_color',
		'default' => $defaults['subnavigation_text_color'],
		'label' => __('Text', 'generate-colors'),
		'priority' => 2
	);
	$subnavigation_colors[] = array(
		'slug'=>'subnavigation_text_hover_color',
		'default' => $defaults['subnavigation_text_hover_color'],
		'label' => __('Text Hover', 'generate-colors'),
		'priority' => 4
	);
	$subnavigation_colors[] = array(
		'slug'=>'subnavigation_text_current_color',
		'default' => $defaults['subnavigation_text_current_color'],
		'label' => __('Text Current', 'generate-colors'),
		'priority' => 6
	);
	foreach( $subnavigation_colors as $color ) {
		$wp_customize->add_setting(
			'generate_settings[' . $color['slug'] . ']', array(
				'default' => $color['default'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$color['slug'],
				array(
					'label' => $color['label'],
					'section' => 'subnavigation_color_section',
					'settings' => 'generate_settings[' . $color['slug'] . ']',
					'priority' => $color['priority']
				)
			)
		);
	}

	$wp_customize->add_section(
		'buttons_color_section',
		array(
			'title' => __( 'Buttons', 'generate-colors' ),
			'capability' => 'edit_theme_options',
			'priority' => 75,
			'panel' => 'generate_colors_panel'
		)
	);

	$wp_customize->add_setting(
		'generate_settings[form_button_background_color]',
		array(
			'default'     => $defaults['form_button_background_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[form_button_background_color]',
			array(
				'label'     => __( 'Background', 'generate-colors' ),
				'section'   => 'buttons_color_section',
				'settings'  => 'generate_settings[form_button_background_color]',
				'palette'   => $palettes,
			)
		)
	);

	$wp_customize->add_setting(
		'generate_settings[form_button_text_color]', array(
			'default' => $defaults['form_button_text_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'form_button_text_color',
			array(
				'label' => __( 'Text', 'generate-colors' ),
				'section' => 'buttons_color_section',
				'settings' => 'generate_settings[form_button_text_color]',
			)
		)
	);

	$wp_customize->add_setting(
		'generate_settings[form_button_background_color_hover]',
		array(
			'default'     => $defaults['form_button_background_color_hover'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[form_button_background_color_hover]',
			array(
				'label'     => __( 'Background Hover', 'generate-colors' ),
				'section'   => 'buttons_color_section',
				'settings'  => 'generate_settings[form_button_background_color_hover]',
				'palette'   => $palettes,
			)
		)
	);

	$wp_customize->add_setting(
		'generate_settings[form_button_text_color_hover]', array(
			'default' => $defaults['form_button_text_color_hover'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'form_button_text_color_hover',
			array(
				'label' => __( 'Text Hover', 'generate-colors' ),
				'section' => 'buttons_color_section',
				'settings' => 'generate_settings[form_button_text_color_hover]',
			)
		)
	);

	// Add Content Colors section
	$wp_customize->add_section(
		'content_color_section',
		array(
			'title' => __( 'Content', 'generate-colors' ),
			'capability' => 'edit_theme_options',
			'priority' => 80,
			'panel' => 'generate_colors_panel'
		)
	);

	$wp_customize->add_setting(
		'generate_settings[content_background_color]',
		array(
			'default'     => $defaults['content_background_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[content_background_color]',
			array(
				'label'     => __( 'Background', 'generate-colors' ),
				'section'   => 'content_color_section',
				'settings'  => 'generate_settings[content_background_color]',
				'palette'   => $palettes,
				'priority' => 1
			)
		)
	);

	// Add color settings
	$content_colors = array();
	$content_colors[] = array(
		'slug' => 'content_text_color',
		'default' => $defaults['content_text_color'],
		'label' => __('Text', 'generate-colors'),
		'priority' => 2
	);
	$content_colors[] = array(
		'slug' => 'content_link_color',
		'default' => $defaults['content_link_color'],
		'label' => __('Link', 'generate-colors'),
		'priority' => 3
	);
	$content_colors[] = array(
		'slug' => 'content_link_hover_color',
		'default' => $defaults['content_link_hover_color'],
		'label' => __('Link Hover', 'generate-colors'),
		'priority' => 4
	);
	$content_colors[] = array(
		'slug' => 'content_title_color',
		'default' => $defaults['content_title_color'],
		'label' => __('Content Title', 'generate-colors'),
		'priority' => 5
	);
	$content_colors[] = array(
		'slug' => 'blog_post_title_color',
		'default' => $defaults['blog_post_title_color'],
		'label' => __('Blog Post Title', 'generate-colors'),
		'priority' => 6
	);
	$content_colors[] = array(
		'slug' => 'blog_post_title_hover_color',
		'default' => $defaults['blog_post_title_hover_color'],
		'label' => __('Blog Post Title Hover', 'generate-colors'),
		'priority' => 7
	);
	$content_colors[] = array(
		'slug' => 'entry_meta_text_color',
		'default' => $defaults['entry_meta_text_color'],
		'label' => __('Entry Meta Text', 'generate-colors'),
		'priority' => 8
	);
	$content_colors[] = array(
		'slug' => 'entry_meta_link_color',
		'default' => $defaults['entry_meta_link_color'],
		'label' => __('Entry Meta Links', 'generate-colors'),
		'priority' => 9
	);
	$content_colors[] = array(
		'slug' => 'entry_meta_link_color_hover',
		'default' => $defaults['entry_meta_link_color_hover'],
		'label' => __('Entry Meta Links Hover', 'generate-colors'),
		'priority' => 10
	);
	$content_colors[] = array(
		'slug' => 'h1_color',
		'default' => $defaults['h1_color'],
		'label' => __('Heading 1 (H1) Color', 'generate-colors'),
		'priority' => 11
	);
	$content_colors[] = array(
		'slug' => 'h2_color',
		'default' => $defaults['h2_color'],
		'label' => __('Heading 2 (H2) Color', 'generate-colors'),
		'priority' => 12
	);
	$content_colors[] = array(
		'slug' => 'h3_color',
		'default' => $defaults['h3_color'],
		'label' => __('Heading 3 (H3) Color', 'generate-colors'),
		'priority' => 13
	);

	if ( isset( $defaults['h4_color'] ) ) {
		$content_colors[] = array(
			'slug' => 'h4_color',
			'default' => $defaults['h4_color'],
			'label' => __('Heading 4 (H4) Color', 'generate-colors'),
			'priority' => 14
		);
	}

	if ( isset( $defaults['h5_color'] ) ) {
		$content_colors[] = array(
			'slug' => 'h5_color',
			'default' => $defaults['h5_color'],
			'label' => __('Heading 5 (H5) Color', 'generate-colors'),
			'priority' => 15
		);
	}

	foreach( $content_colors as $color ) {
		$wp_customize->add_setting(
			'generate_settings[' . $color['slug'] . ']', array(
				'default' => $color['default'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$color['slug'],
				array(
					'label' => $color['label'],
					'section' => 'content_color_section',
					'settings' => 'generate_settings[' . $color['slug'] . ']',
					'priority' => $color['priority']
				)
			)
		);
	}

	// Add Sidebar Widget colors
	$wp_customize->add_section(
		'sidebar_widget_color_section',
		array(
			'title' => __( 'Sidebar Widgets', 'generate-colors' ),
			'capability' => 'edit_theme_options',
			'priority' => 90,
			'panel' => 'generate_colors_panel'
		)
	);

	$wp_customize->add_setting(
		'generate_settings[sidebar_widget_background_color]',
		array(
			'default'     => $defaults['sidebar_widget_background_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[sidebar_widget_background_color]',
			array(
				'label'     => __( 'Background', 'generate-colors' ),
				'section'   => 'sidebar_widget_color_section',
				'settings'  => 'generate_settings[sidebar_widget_background_color]',
				'palette'   => $palettes,
				'priority' => 1
			)
		)
	);

	// Add color settings
	$sidebar_widget_colors = array();
	$sidebar_widget_colors[] = array(
		'slug' => 'sidebar_widget_text_color',
		'default' => $defaults['sidebar_widget_text_color'],
		'label' => __('Text', 'generate-colors'),
		'priority' => 2
	);
	$sidebar_widget_colors[] = array(
		'slug' => 'sidebar_widget_link_color',
		'default' => $defaults['sidebar_widget_link_color'],
		'label' => __('Link', 'generate-colors'),
		'priority' => 3
	);
	$sidebar_widget_colors[] = array(
		'slug' => 'sidebar_widget_link_hover_color',
		'default' => $defaults['sidebar_widget_link_hover_color'],
		'label' => __('Link Hover', 'generate-colors'),
		'priority' => 4
	);
	$sidebar_widget_colors[] = array(
		'slug' => 'sidebar_widget_title_color',
		'default' => $defaults['sidebar_widget_title_color'],
		'label' => __('Widget Title', 'generate-colors'),
		'priority' => 5
	);

	foreach( $sidebar_widget_colors as $color ) {
		$wp_customize->add_setting(
			'generate_settings[' . $color['slug'] . ']', array(
				'default' => $color['default'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$color['slug'],
				array(
					'label' => $color['label'],
					'section' => 'sidebar_widget_color_section',
					'settings' => 'generate_settings[' . $color['slug'] . ']',
					'priority' => $color['priority']
				)
			)
		);
	}

	// Add Footer widget colors
	$wp_customize->add_section(
		'footer_widget_color_section',
		array(
			'title' => __( 'Footer Widgets', 'generate-colors' ),
			'capability' => 'edit_theme_options',
			'priority' => 100,
			'panel' => 'generate_colors_panel'
		)
	);

	$wp_customize->add_setting(
		'generate_settings[footer_widget_background_color]',
		array(
			'default'     => $defaults['footer_widget_background_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[footer_widget_background_color]',
			array(
				'label'     => __( 'Background', 'generate-colors' ),
				'section'   => 'footer_widget_color_section',
				'settings'  => 'generate_settings[footer_widget_background_color]',
				'palette'   => $palettes,
				'priority' => 1
			)
		)
	);

	// Add color settings
	$footer_widget_colors = array();
	$footer_widget_colors[] = array(
		'slug' => 'footer_widget_text_color',
		'default' => $defaults['footer_widget_text_color'],
		'label' => __('Text', 'generate-colors'),
		'priority' => 2
	);
	$footer_widget_colors[] = array(
		'slug' => 'footer_widget_link_color',
		'default' => $defaults['footer_widget_link_color'],
		'label' => __('Link', 'generate-colors'),
		'priority' => 3
	);
	$footer_widget_colors[] = array(
		'slug' => 'footer_widget_link_hover_color',
		'default' => $defaults['footer_widget_link_hover_color'],
		'label' => __('Link Hover', 'generate-colors'),
		'priority' => 4
	);
	$footer_widget_colors[] = array(
		'slug' => 'footer_widget_title_color',
		'default' => $defaults['footer_widget_title_color'],
		'label' => __('Widget Title', 'generate-colors'),
		'priority' => 5
	);

	foreach( $footer_widget_colors as $color ) {
		$wp_customize->add_setting(
			'generate_settings[' . $color['slug'] . ']', array(
				'default' => $color['default'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$color['slug'],
				array(
					'label' => $color['label'],
					'section' => 'footer_widget_color_section',
					'settings' => 'generate_settings[' . $color['slug'] . ']',
					'priority' => $color['priority']
				)
			)
		);
	}

	// Add Form colors
	$wp_customize->add_section(
		'form_color_section',
		array(
			'title' => __( 'Forms', 'generate-colors' ),
			'capability' => 'edit_theme_options',
			'priority' => 130,
			'panel' => 'generate_colors_panel'
		)
	);

	$wp_customize->add_setting(
		'generate_settings[form_background_color]',
		array(
			'default'     => $defaults['form_background_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[form_background_color]',
			array(
				'label'     => __( 'Form Background', 'generate-colors' ),
				'section'   => 'form_color_section',
				'settings'  => 'generate_settings[form_background_color]',
				'palette'   => $palettes,
				'priority' => 1
			)
		)
	);

	$wp_customize->add_setting(
		'generate_settings[form_background_color_focus]',
		array(
			'default'     => $defaults['form_background_color_focus'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[form_background_color_focus]',
			array(
				'label'     => __( 'Form Background Focus', 'generate-colors' ),
				'section'   => 'form_color_section',
				'settings'  => 'generate_settings[form_background_color_focus]',
				'palette'   => $palettes,
				'priority' => 3
			)
		)
	);

	$wp_customize->add_setting(
		'generate_settings[form_border_color]',
		array(
			'default'     => $defaults['form_border_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[form_border_color]',
			array(
				'label'     => __( 'Form Border', 'generate-colors' ),
				'section'   => 'form_color_section',
				'settings'  => 'generate_settings[form_border_color]',
				'palette'   => $palettes,
				'priority' => 5
			)
		)
	);

	$wp_customize->add_setting(
		'generate_settings[form_border_color_focus]',
		array(
			'default'     => $defaults['form_border_color_focus'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[form_border_color_focus]',
			array(
				'label'     => __( 'Form Border Focus', 'generate-colors' ),
				'section'   => 'form_color_section',
				'settings'  => 'generate_settings[form_border_color_focus]',
				'palette'   => $palettes,
				'priority' => 6
			)
		)
	);

	// Add color settings
	$form_colors = array();
	$form_colors[] = array(
		'slug' => 'form_text_color',
		'default' => $defaults['form_text_color'],
		'label' => __('Form Text', 'generate-colors'),
		'priority' => 2
	);
	$form_colors[] = array(
		'slug' => 'form_text_color_focus',
		'default' => $defaults['form_text_color_focus'],
		'label' => __('Form Text Focus', 'generate-colors'),
		'priority' => 4
	);

	foreach( $form_colors as $color ) {
		$wp_customize->add_setting(
			'generate_settings[' . $color['slug'] . ']', array(
				'default' => $color['default'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$color['slug'],
				array(
					'label' => $color['label'],
					'section' => 'form_color_section',
					'settings' => 'generate_settings[' . $color['slug'] . ']',
					'priority' => $color['priority']
				)
			)
		);
	}

	// Add Footer colors
	$wp_customize->add_section(
		'footer_color_section',
		array(
			'title' => __( 'Footer', 'generate-colors' ),
			'capability' => 'edit_theme_options',
			'priority' => 150,
			'panel' => 'generate_colors_panel'
		)
	);

	$wp_customize->add_setting(
		'generate_settings[footer_background_color]',
		array(
			'default'     => $defaults['footer_background_color'],
			'type'        => 'option',
			'capability'  => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[footer_background_color]',
			array(
				'label'     => __( 'Background', 'generate-colors' ),
				'section'   => 'footer_color_section',
				'settings'  => 'generate_settings[footer_background_color]',
				'palette'   => $palettes,
				'priority' => 1
			)
		)
	);

	// Add color settings
	$footer_colors = array();
	$footer_colors[] = array(
		'slug' => 'footer_text_color',
		'default' => $defaults['footer_text_color'],
		'label' => __('Text', 'generate-colors'),
		'priority' => 2
	);
	$footer_colors[] = array(
		'slug' => 'footer_link_color',
		'default' => $defaults['footer_link_color'],
		'label' => __('Link', 'generate-colors'),
		'priority' => 3
	);
	$footer_colors[] = array(
		'slug' => 'footer_link_hover_color',
		'default' => $defaults['footer_link_hover_color'],
		'label' => __('Link Hover', 'generate-colors'),
		'priority' => 4
	);

	foreach( $footer_colors as $color ) {
		$wp_customize->add_setting(
			'generate_settings[' . $color['slug'] . ']', array(
				'default' => $color['default'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$color['slug'],
				array(
					'label' => $color['label'],
					'section' => 'footer_color_section',
					'settings' => 'generate_settings[' . $color['slug'] . ']',
					'priority' => $color['priority']
				)
			)
		);
	}

	if ( isset( $defaults['back_to_top_background_color'] ) ) {
		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_back_to_top_title',
				array(
					'section' => 'footer_color_section',
					'type' => 'generatepress-customizer-title',
					'title' => __( 'Back to Top Button', 'generate-colors' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname'
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[back_to_top_background_color]', array(
				'default' => $defaults['back_to_top_background_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[back_to_top_background_color]',
				array(
					'label' => __( 'Background', 'generate-colors' ),
					'section' => 'footer_color_section',
					'settings' => 'generate_settings[back_to_top_background_color]',
					'palette' => $palettes
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[back_to_top_text_color]', array(
				'default' => $defaults['back_to_top_text_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[back_to_top_text_color]',
				array(
					'label' => __( 'Text', 'generate-colors' ),
					'section' => 'footer_color_section',
					'settings' => 'generate_settings[back_to_top_text_color]'
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[back_to_top_background_color_hover]', array(
				'default' => $defaults['back_to_top_background_color_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[back_to_top_background_color_hover]',
				array(
					'label'     => __( 'Background Hover', 'generate-colors' ),
					'section'   => 'footer_color_section',
					'settings'  => 'generate_settings[back_to_top_background_color_hover]',
					'palette'   => $palettes
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[back_to_top_text_color_hover]', array(
				'default' => $defaults['back_to_top_text_color_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[back_to_top_text_color_hover]',
				array(
					'label' => __( 'Text Hover', 'generate-colors' ),
					'section' => 'footer_color_section',
					'settings' => 'generate_settings[back_to_top_text_color_hover]'
				)
			)
		);
	}
}
endif;

if ( ! function_exists( 'generate_get_color_setting' ) ) :
/**
 * Wrapper function to get our settings
 * @since 1.3.42
 */
function generate_get_color_setting( $setting ) {

	// Bail if we don't have our color defaults
	if ( ! function_exists( 'generate_get_color_defaults' ) )
		return;

	if ( function_exists( 'generate_get_defaults' ) ) {
		$defaults = array_merge( generate_get_defaults(), generate_get_color_defaults()  );
	} else {
		$defaults = generate_get_color_defaults();
	}

	$generate_settings = wp_parse_args(
		get_option( 'generate_settings', array() ),
		$defaults
	);

	return $generate_settings[ $setting ];
}
endif;

if ( ! function_exists( 'generate_colors_rgba_to_hex' ) ) :
/**
 * Convert RGBA to hex if necessary
 * @since 1.3.42
 */
function generate_colors_rgba_to_hex( $rgba ) {
	// If it's not rgba, return it
	if ( false === strpos( $rgba, 'rgba' ) )
		return $rgba;

	return substr($rgba, 0, strrpos($rgba, ',')) . ')';
}
endif;

if ( ! function_exists( 'generate_get_default_color_palettes' ) ) :
/**
 * Set up our colors for the color picker palettes and filter them so you can change them
 * @since 1.3.42
 */
function generate_get_default_color_palettes() {
	$palettes = array(
		generate_colors_rgba_to_hex( generate_get_color_setting( 'link_color' ) ),
		generate_colors_rgba_to_hex( generate_get_color_setting( 'background_color' ) ),
		generate_colors_rgba_to_hex( generate_get_color_setting( 'navigation_background_color' ) ),
		generate_colors_rgba_to_hex( generate_get_color_setting( 'navigation_background_hover_color' ) ),
		'#F1C40F',
		'#1e72bd',
		'#1ABC9C',
		'#3498DB'
	);

	return apply_filters( 'generate_default_color_palettes', $palettes );
}
endif;

if ( ! function_exists( 'generate_enqueue_color_palettes' ) ):
/**
 * Add our custom color palettes to the color pickers in the Customizer
 * @since 1.3.42
 * Hooks into 1001 priority to show up after Secondary Nav
 */
add_action( 'customize_controls_enqueue_scripts','generate_enqueue_color_palettes', 1001 );
function generate_enqueue_color_palettes()
{
	// Old versions of WP don't get nice things
	if ( ! function_exists( 'wp_add_inline_script' ) )
		return;

	// Grab our palette array and turn it into JS
	$palettes = json_encode( generate_get_default_color_palettes() );

	// Add our custom palettes
	// json_encode takes care of escaping
	wp_add_inline_script( 'wp-color-picker', 'jQuery.wp.wpColorPicker.prototype.options.palettes = ' . $palettes . ';' );
}
endif;

if ( ! function_exists( 'generate_colors_customizer_live_preview' ) ) :
/**
 * Add our live preview javascript
 * @since 0.1
 */
add_action( 'customize_preview_init', 'generate_colors_customizer_live_preview' );
function generate_colors_customizer_live_preview()
{
	wp_enqueue_script(
		  'generate-colors-customizer',
		  trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/customizer.js',
		  array( 'jquery','customize-preview' ),
		  GENERATE_COLORS_VERSION,
		  true
	);

	wp_register_script(
		  'generate-wc-colors-customizer',
		  trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/wc-customizer.js',
		  array( 'jquery','customize-preview', 'generate-colors-customizer' ),
		  GENERATE_COLORS_VERSION,
		  true
	);
}
endif;
