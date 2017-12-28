<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Include our CSS class
 */
require plugin_dir_path( __FILE__ ) . 'css.php';

add_action( 'after_setup_theme', 'generate_secondary_nav_setup' );

if ( ! function_exists( 'generate_secondary_nav_setup' ) ) :
/**
 * Register our secondary navigation
 * @since 0.1
 */
function generate_secondary_nav_setup() 
{
	register_nav_menus( array(
		'secondary' => __( 'Secondary Menu','secondary-nav' ),
	) );
}
endif;

if ( ! function_exists( 'generate_secondary_nav_enqueue_scripts' ) ) :
/**
 * Add our necessary scripts
 * @since 0.1
 */
add_action( 'wp_enqueue_scripts','generate_secondary_nav_enqueue_scripts', 100 );
function generate_secondary_nav_enqueue_scripts() 
{
	// Bail if no Secondary menu is set
	if ( ! has_nav_menu( 'secondary' ) ) {
		return;
	}
	
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_style( 'generate-secondary-nav', plugin_dir_url( __FILE__ ) . "css/style{$suffix}.css", array(), GENERATE_SECONDARY_NAV_VERSION );
	if ( ! defined( 'GENERATE_DISABLE_MOBILE' ) ) :
		wp_add_inline_script( 'generate-navigation', 
			"jQuery( document ).ready( function($) {
				$( '.secondary-navigation .menu-toggle' ).on( 'click', function( e ) {
					e.preventDefault();
					$( this ).closest( '.secondary-navigation' ).toggleClass( 'toggled' );
					$( this ).closest( '.secondary-navigation' ).attr( 'aria-expanded', $( this ).closest( '.secondary-navigation' ).attr( 'aria-expanded' ) === 'true' ? 'false' : 'true' );
					$( this ).toggleClass( 'toggled' );
					$( this ).children( 'i' ).toggleClass( 'fa-bars' ).toggleClass( 'fa-close' );
					$( this ).attr( 'aria-expanded', $( this ).attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
				});
			});" 
		);
		wp_enqueue_style( 'generate-secondary-nav-mobile', plugin_dir_url( __FILE__ ) . "css/mobile{$suffix}.css", array(), GENERATE_SECONDARY_NAV_VERSION, 'all' );
	endif;
}	
endif;

if ( ! function_exists( 'generate_secondary_nav_enqueue_customizer_scripts' ) ) :
/**
 * Add our Customizer preview JS
 * @since 0.1
 */
add_action( 'customize_preview_init', 'generate_secondary_nav_enqueue_customizer_scripts' );
function generate_secondary_nav_enqueue_customizer_scripts()
{
    wp_enqueue_script( 'generate-secondary-nav-customizer', plugin_dir_url( __FILE__ ) . 'js/customizer.js', array( 'jquery', 'customize-preview' ), GENERATE_SECONDARY_NAV_VERSION, true );
}
endif;

if ( ! function_exists( 'generate_secondary_nav_get_defaults' ) ) :
/**
 * Set default options
 * @since 0.1
 */
function generate_secondary_nav_get_defaults( $filter = true )
{
	$generate_defaults = array(
		'secondary_nav_mobile_label' => 'Menu',
		'secondary_nav_layout_setting' => 'secondary-fluid-nav',
		'secondary_nav_inner_width' => 'contained',
		'secondary_nav_position_setting' => 'secondary-nav-above-header',
		'secondary_nav_alignment' => 'right',
		'navigation_background_color' => '#636363',
		'navigation_text_color' => '#ffffff',
		'navigation_background_hover_color' => '#303030',
		'navigation_text_hover_color' => '#ffffff',
		'navigation_background_current_color' => '#ffffff',
		'navigation_text_current_color' => '#222222',
		'subnavigation_background_color' => '#303030',
		'subnavigation_text_color' => '#ffffff',
		'subnavigation_background_hover_color' => '#474747',
		'subnavigation_text_hover_color' => '#ffffff',
		'subnavigation_background_current_color' => '#474747',
		'subnavigation_text_current_color' => '#ffffff',
		'secondary_menu_item' => '20',
		'secondary_menu_item_height' => '40',
		'secondary_sub_menu_item_height' => '10',
		'font_secondary_navigation' => 'inherit',
		'secondary_navigation_font_weight' => 'normal',
		'secondary_navigation_font_transform' => 'none',
		'secondary_navigation_font_size' => '13',
		'nav_image' => '',
		'nav_repeat' => '',
		'nav_item_image' => '',
		'nav_item_repeat' => '',
		'nav_item_hover_image' => '',
		'nav_item_hover_repeat' => '',
		'nav_item_current_image' => '',
		'nav_item_current_repeat' => '',
		'sub_nav_image' => '',
		'sub_nav_repeat' => '',
		'sub_nav_item_image' => '',
		'sub_nav_item_repeat' => '',
		'sub_nav_item_hover_image' => '',
		'sub_nav_item_hover_repeat' => '',
		'sub_nav_item_current_image' => '',
		'sub_nav_item_current_repeat' => '',
		'merge_top_bar' => false
	);
	
	if ( $filter ) 
		return apply_filters( 'generate_secondary_nav_option_defaults', $generate_defaults );
	
	return $generate_defaults;
}
endif;

if ( ! function_exists( 'generate_secondary_nav_customize_register' ) ) :
/**
 * Register our options
 * @since 0.1
 */
add_action( 'customize_register', 'generate_secondary_nav_customize_register', 100 );
function generate_secondary_nav_customize_register( $wp_customize ) 
{
	// Get our defaults
	$defaults = generate_secondary_nav_get_defaults();
	
	// Controls
	require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';
	
	// Use the Layout panel in the free theme if it exists
	if ( $wp_customize->get_panel( 'generate_layout_panel' ) ) {
		$layout_panel = 'generate_layout_panel';
	} else {
		$layout_panel = 'secondary_navigation_panel';
	}
	
	// Add our secondary navigation panel
	// This shouldn't be used anymore if the theme is up to date
	if ( class_exists( 'WP_Customize_Panel' ) ) {
		$wp_customize->add_panel( 'secondary_navigation_panel', array(
			'priority'       => 100,
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => __( 'Secondary Navigation','secondary-nav' ),
			'description'    => '',
		) );
	}
	
	// Add secondary navigation section
	$wp_customize->add_section(
		'secondary_nav_section',
		array(
			'title' => __( 'Secondary Navigation','secondary-nav' ),
			'capability' => 'edit_theme_options',
			'priority' => 31,
			'panel' => $layout_panel
		)
	);
	
	// Mobile menu label
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[secondary_nav_mobile_label]', 
		array(
			'default' => $defaults['secondary_nav_mobile_label'],
			'type' => 'option',
			'sanitize_callback' => 'wp_kses_post'
		)
	);
		 
	$wp_customize->add_control(
		'secondary_nav_mobile_label_control', array(
			'label' => __('Mobile Menu Label','secondary-nav'),
			'section' => 'secondary_nav_section',
			'settings' => 'generate_secondary_nav_settings[secondary_nav_mobile_label]',
			'priority' => 10
		)
	);
	
	// Navigation width
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[secondary_nav_layout_setting]',
		array(
			'default' => $defaults['secondary_nav_layout_setting'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[secondary_nav_layout_setting]',
		array(
			'type' => 'select',
			'label' => __( 'Navigation Width','secondary-nav' ),
			'section' => 'secondary_nav_section',
			'choices' => array(
				'secondary-fluid-nav' => __( 'Full','secondary-nav' ),
				'secondary-contained-nav' => __( 'Contained','secondary-nav' )
			),
			'settings' => 'generate_secondary_nav_settings[secondary_nav_layout_setting]',
			'priority' => 15
		)
	);
	
	// Inner navigation width
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[secondary_nav_inner_width]',
		array(
			'default' => $defaults['secondary_nav_inner_width'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[secondary_nav_inner_width]',
		array(
			'type' => 'select',
			'label' => __( 'Inner Navigation Width','secondary-nav' ),
			'section' => 'secondary_nav_section',
			'choices' => array(
				'full-width' => __( 'Full','secondary-nav' ),
				'contained' => __( 'Contained','secondary-nav' )
			),
			'settings' => 'generate_secondary_nav_settings[secondary_nav_inner_width]',
			'priority' => 15
		)
	);

	// Navigation alignment
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[secondary_nav_alignment]',
		array(
			'default' => $defaults['secondary_nav_alignment'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[secondary_nav_alignment]',
		array(
			'type' => 'select',
			'label' => __( 'Navigation Alignment','secondary-nav' ),
			'section' => 'secondary_nav_section',
			'choices' => array(
				'left' => __( 'Left','secondary-nav' ),
				'center' => __( 'Center','secondary-nav' ),
				'right' => __( 'Right','secondary-nav' )
			),
			'settings' => 'generate_secondary_nav_settings[secondary_nav_alignment]',
			'priority' => 20
		)
	);
	
	// Navigation location
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[secondary_nav_position_setting]',
		array(
			'default' => $defaults['secondary_nav_position_setting'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[secondary_nav_position_setting]',
		array(
			'type' => 'select',
			'label' => __( 'Navigation Location','secondary-nav' ),
			'section' => 'secondary_nav_section',
			'choices' => array(
				'secondary-nav-below-header' => __( 'Below Header','secondary-nav' ),
				'secondary-nav-above-header' => __( 'Above Header','secondary-nav' ),
				'secondary-nav-float-right' => __( 'Float Right','secondary-nav' ),
				'secondary-nav-float-left' => __( 'Float Left','secondary-nav' ),
				'secondary-nav-left-sidebar' => __( 'Left Sidebar','secondary-nav' ),
				'secondary-nav-right-sidebar' => __( 'Right Sidebar','secondary-nav' ),
				'' => __( 'No Navigation','secondary-nav' )
			),
			'settings' => 'generate_secondary_nav_settings[secondary_nav_position_setting]',
			'priority' => 30
		)
	);
	
	// Merge top bar
	$wp_customize->add_setting(
		'generate_secondary_nav_settings[merge_top_bar]',
		array(
			'default' => $defaults['merge_top_bar'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);
	
	$wp_customize->add_control(
		'generate_secondary_nav_settings[merge_top_bar]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Merge with Secondary Navigation','secondary-nav' ),
			'section' => 'generate_top_bar',
			'settings' => 'generate_secondary_nav_settings[merge_top_bar]',
			'priority' => 100,
			'active_callback' => 'generate_secondary_nav_show_merge_top_bar'
		)
	);
}
endif;

if ( ! function_exists( 'generate_display_secondary_google_fonts' ) ) :
/** 
 * Add Google Fonts to wp_head if needed
 * @since 0.1
 */
add_filter('generate_typography_google_fonts','generate_display_secondary_google_fonts', 50);
function generate_display_secondary_google_fonts($google_fonts) {
	
	// Bail if no Secondary menu is set
	if ( ! has_nav_menu( 'secondary' ) ) {
		return $google_fonts;
	}
		
	$generate_secondary_nav_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	
	// List our non-Google fonts
	if ( function_exists( 'generate_typography_default_fonts' ) ) {
		$not_google = str_replace( ' ', '+', generate_typography_default_fonts() );
	} else {
		$not_google = array(
			'inherit',
			'Arial,+Helvetica,+sans-serif',
			'Century+Gothic',
			'Comic+Sans+MS',
			'Courier+New',
			'Georgia,+Times+New+Roman,+Times,+serif',
			'Helvetica',
			'Impact',
			'Lucida+Console',
			'Lucida+Sans+Unicode',
			'Palatino+Linotype',
			'Tahoma,+Geneva,+sans-serif',
			'Trebuchet+MS,+Helvetica,+sans-serif',
			'Verdana,+Geneva,+sans-serif'
		);
	}
	
	// Create our Google Fonts array
	$secondary_google_fonts = array();
	
	if ( function_exists( 'generate_get_google_font_variants' ) ) :
	
		// If our value is still using the old format, fix it
		if ( strpos( $generate_secondary_nav_settings[ 'font_secondary_navigation' ], ':' ) !== false )
			$generate_secondary_nav_settings[ 'font_secondary_navigation' ] = current( explode( ':', $generate_secondary_nav_settings[ 'font_secondary_navigation' ] ) );
		
		// Grab the variants using the plain name
		$variants = generate_get_google_font_variants( $generate_secondary_nav_settings[ 'font_secondary_navigation' ], 'font_secondary_navigation', generate_secondary_nav_get_defaults() );
	
	else :
		$variants = '';
	endif;
	
	// Replace the spaces in the names with a plus
	$value = str_replace( ' ', '+', $generate_secondary_nav_settings[ 'font_secondary_navigation' ] );
			
	// If we have variants, add them to our value
	$value = ! empty( $variants ) ? $value . ':' . $variants : $value;
			
	// Add our value to the array
	$secondary_google_fonts[] = $value;
	
	// Ignore any non-Google fonts
	$secondary_google_fonts = array_diff($secondary_google_fonts, $not_google);
	
	// Separate each different font with a bar
	$secondary_google_fonts = implode('|', $secondary_google_fonts);
	
	if ( !empty( $secondary_google_fonts ) ) :
		$print_secondary_fonts = '|' . $secondary_google_fonts;
	else : 
		$print_secondary_fonts = '';
	endif;
	
	// Remove any duplicates
	$return = $google_fonts . $print_secondary_fonts;
	$return = implode('|',array_unique(explode('|', $return)));
	return $return;
	
}
endif;

if ( ! function_exists( 'generate_add_secondary_navigation_after_header' ) ) :
/**
 * Add the navigation after the header
 * @since 0.1
 */
add_action( 'generate_after_header', 'generate_add_secondary_navigation_after_header', 7 );
function generate_add_secondary_navigation_after_header()
{
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	
	if ( 'secondary-nav-below-header' == $generate_settings['secondary_nav_position_setting'] ) :
		generate_secondary_navigation_position();
	endif;
	
}
endif;

if ( ! function_exists( 'generate_add_secondary_navigation_before_header' ) ) :
/**
 * Add the navigation before the header
 * @since 0.1
 */
add_action( 'generate_before_header', 'generate_add_secondary_navigation_before_header', 7 );
function generate_add_secondary_navigation_before_header()
{
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	
	if ( 'secondary-nav-above-header' == $generate_settings['secondary_nav_position_setting'] ) :
		generate_secondary_navigation_position();
	endif;
	
}
endif;

if ( ! function_exists( 'generate_add_secondary_navigation_float_right' ) ) :
/**
 * Add the navigation inside the header so it can float right
 * @since 0.1
 */
add_action( 'generate_before_header_content', 'generate_add_secondary_navigation_float_right', 7 );
function generate_add_secondary_navigation_float_right()
{
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	
	if ( 'secondary-nav-float-right' == $generate_settings['secondary_nav_position_setting'] || 'secondary-nav-float-left' == $generate_settings['secondary_nav_position_setting'] ) {
		generate_secondary_navigation_position();
	}
	
}
endif;

if ( ! function_exists( 'generate_add_secondary_navigation_before_right_sidebar' ) ) :
/**
 * Add the navigation into the right sidebar
 * @since 0.1
 */
add_action( 'generate_before_right_sidebar_content', 'generate_add_secondary_navigation_before_right_sidebar', 7 );
function generate_add_secondary_navigation_before_right_sidebar()
{
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	
	if ( 'secondary-nav-right-sidebar' == $generate_settings['secondary_nav_position_setting'] ) :
		echo '<div class="gen-sidebar-secondary-nav">';
			generate_secondary_navigation_position();
		echo '</div><!-- .gen-sidebar-secondary-nav -->';
	endif;
	
}
endif;

if ( ! function_exists( 'generate_add_secondary_navigation_before_left_sidebar' ) ) :
/**
 * Add the navigation into the left sidebar
 * @since 0.1
 */
add_action( 'generate_before_left_sidebar_content', 'generate_add_secondary_navigation_before_left_sidebar', 7 );
function generate_add_secondary_navigation_before_left_sidebar()
{
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	
	if ( 'secondary-nav-left-sidebar' == $generate_settings['secondary_nav_position_setting'] ) :
		echo '<div class="gen-sidebar-secondary-nav">';
			generate_secondary_navigation_position();
		echo '</div><!-- .gen-sidebar-secondary-nav -->';
	endif;
	
}
endif;

if ( ! function_exists( 'generate_secondary_navigation_position' ) ) :
/**
 * Build our secondary navigation.
 * Would like to change this function name.
 * @since 0.1
 */
function generate_secondary_navigation_position()
{
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	if ( has_nav_menu( 'secondary' ) ) :
		?>
		<nav itemtype="http://schema.org/SiteNavigationElement" itemscope="itemscope" id="secondary-navigation" <?php generate_secondary_navigation_class(); ?>>
			<div <?php generate_inside_secondary_navigation_class(); ?>>
				<?php do_action('generate_inside_secondary_navigation'); ?>
				<button class="menu-toggle secondary-menu-toggle">
					<?php do_action( 'generate_inside_secondary_mobile_menu' ); ?>
					<span class="mobile-menu"><?php echo $generate_settings['secondary_nav_mobile_label']; ?></span>
				</button>
				<?php 
				
					wp_nav_menu( 
						array( 
							'theme_location' => 'secondary',
							'container' => 'div',
							'container_class' => 'main-nav',
							'menu_class' => '',
							'fallback_cb' => 'generate_secondary_menu_fallback',
							'items_wrap' => '<ul id="%1$s" class="%2$s ' . join( ' ', generate_get_secondary_menu_class() ) . '">%3$s</ul>'
						) 
					);
				
				?>
			</div><!-- .inside-navigation -->
		</nav><!-- #secondary-navigation -->
		<?php
	endif;
}
endif;

if ( ! function_exists( 'generate_secondary_menu_fallback' ) ) :
/**
 * Menu fallback. 
 *
 * @param  array $args
 * @return string
 * @since 1.1.4
 */
function generate_secondary_menu_fallback( $args )
{ 
?>
	<div class="main-nav">
		<ul <?php generate_secondary_menu_class(); ?>>
			<?php wp_list_pages('sort_column=menu_order&title_li='); ?>
		</ul>
	</div><!-- .main-nav -->
<?php 
}
endif;

if ( ! function_exists( 'generate_secondary_nav_body_classes' ) ) :
/**
 * Adds custom classes to the array of body classes.
 * @since 0.1
 */
add_filter( 'body_class', 'generate_secondary_nav_body_classes' );
function generate_secondary_nav_body_classes( $classes ) {
	
	// Bail if no Secondary menu is set
	if ( ! has_nav_menu( 'secondary' ) ) {
		return $classes;
	}
	
	// Get theme options
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);

	$classes[] = ( $generate_settings['secondary_nav_position_setting'] ) ? $generate_settings['secondary_nav_position_setting'] : 'secondary-nav-below-header';
	
	// Navigation alignment class
	if ( $generate_settings['secondary_nav_alignment'] == 'left' ) :
		$classes[] = 'secondary-nav-aligned-left';
	elseif ( $generate_settings['secondary_nav_alignment'] == 'center' ) :
		$classes[] = 'secondary-nav-aligned-center';
	elseif ( $generate_settings['secondary_nav_alignment'] == 'right' ) :
		$classes[] = 'secondary-nav-aligned-right';
	else :
		$classes[] = 'secondary-nav-aligned-left';
	endif;

	return $classes;
}
endif;

if ( ! function_exists( 'generate_secondary_menu_classes' ) ) :
/**
 * Adds custom classes to the menu
 * @since 0.1
 */
add_filter( 'generate_secondary_menu_class', 'generate_secondary_menu_classes');
function generate_secondary_menu_classes( $classes )
{
	
	$classes[] = 'secondary-menu';
	$classes[] = 'sf-menu';

	// Get theme options
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);

	return $classes;
	
}
endif;

if ( ! function_exists( 'generate_secondary_navigation_classes' ) ) :
/**
 * Adds custom classes to the navigation
 * @since 0.1
 */
add_filter( 'generate_secondary_navigation_class', 'generate_secondary_navigation_classes');
function generate_secondary_navigation_classes( $classes )
{
	
	$classes[] = 'secondary-navigation';

	// Get theme options
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	$nav_layout = $generate_settings['secondary_nav_layout_setting'];
	
	if ( $nav_layout == 'secondary-contained-nav' ) :
		$classes[] = 'grid-container';
		$classes[] = 'grid-parent';
	endif;

	return $classes;
	
}
endif;

if ( ! function_exists( 'generate_inside_secondary_navigation_classes' ) ) :
/**
 * Adds custom classes to the inner navigation
 * @since 1.3.41
 */
add_filter( 'generate_inside_secondary_navigation_class', 'generate_inside_secondary_navigation_classes');
function generate_inside_secondary_navigation_classes( $classes )
{
	
	$classes[] = 'inside-navigation';
	// Get theme options
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	$inner_nav_width = $generate_settings['secondary_nav_inner_width'];
	
	if ( $inner_nav_width !== 'full-width' ) :
		$classes[] = 'grid-container';
		$classes[] = 'grid-parent';
	endif;

	return $classes;
	
}
endif;

if ( !function_exists( 'generate_secondary_nav_css' ) ) :
/**
 * Generate the CSS in the <head> section using the Theme Customizer
 * @since 0.1
 */
function generate_secondary_nav_css()
{
	
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	
	if ( function_exists( 'generate_spacing_get_defaults' ) ) {
		$spacing_settings = wp_parse_args( 
			get_option( 'generate_spacing_settings', array() ), 
			generate_spacing_get_defaults() 
		);
		$separator = $spacing_settings[ 'separator' ];
	} else {
		$separator = 20;
	}

	if ( function_exists( 'generate_get_font_family_css' ) ) :
		$secondary_nav_family = generate_get_font_family_css( 'font_secondary_navigation', 'generate_secondary_nav_settings', generate_secondary_nav_get_defaults() );
	else : 
		$secondary_nav_family = current(explode(':', $generate_settings['font_secondary_navigation']));
	endif;
	
	if ( '""' == $secondary_nav_family ) {
		$secondary_nav_family = 'inherit';
	}
	
	// Get our untouched defaults
	$og_defaults = generate_secondary_nav_get_defaults( false );
	
	// Initiate our CSS class
	$css = new GeneratePress_Secondary_Nav_CSS;
	
	// Navigation background
	$css->set_selector( '.secondary-navigation' );
	$css->add_property( 'background-color', esc_attr( $generate_settings[ 'navigation_background_color' ] ) );
	$css->add_property( 'background-image', !empty( $generate_settings['nav_image'] ) ? 'url(' . esc_url( $generate_settings['nav_image'] )	. ')' : '' );
	$css->add_property( 'background-repeat', esc_attr( $generate_settings['nav_repeat'] ) );
	
	// Top bar
	if ( 'secondary-nav-above-header' == $generate_settings[ 'secondary_nav_position_setting' ] && has_nav_menu( 'secondary' ) && is_active_sidebar( 'top-bar' ) ) {
		$css->set_selector( '.secondary-navigation .top-bar' );
		$css->add_property( 'color', esc_attr( $generate_settings[ 'navigation_text_color' ] ) );
		$css->add_property( 'line-height', absint( $generate_settings[ 'secondary_menu_item_height' ] ), false, 'px' );
		$css->add_property( 'font-family', $secondary_nav_family );
		$css->add_property( 'font-weight', esc_attr( $generate_settings[ 'secondary_navigation_font_weight' ] ) );
		$css->add_property( 'text-transform', esc_attr( $generate_settings[ 'secondary_navigation_font_transform' ] ) );
		$css->add_property( 'font-size', absint( $generate_settings[ 'secondary_navigation_font_size' ] ), false, 'px' );

		$css->set_selector( '.secondary-navigation .top-bar a' );
		$css->add_property( 'color', esc_attr( $generate_settings[ 'navigation_text_color' ] ) );
		
		$css->set_selector( '.secondary-navigation .top-bar a:hover, .secondary-navigation .top-bar a:focus' );
		$css->add_property( 'color', esc_attr( $generate_settings[ 'navigation_background_hover_color' ] ) );
	}
	
	// Navigation text
	$css->set_selector( '.secondary-navigation .main-nav ul li a,.secondary-navigation .menu-toggle' );
	$css->add_property( 'color', esc_attr( $generate_settings[ 'navigation_text_color' ] ) );
	$css->add_property( 'font-family', ( 'inherit' !== $secondary_nav_family ) ? $secondary_nav_family : null );
	$css->add_property( 'font-weight', esc_attr( $generate_settings[ 'secondary_navigation_font_weight' ] ), $og_defaults[ 'secondary_navigation_font_weight' ] );
	$css->add_property( 'text-transform', esc_attr( $generate_settings[ 'secondary_navigation_font_transform' ] ), $og_defaults[ 'secondary_navigation_font_transform' ] );
	$css->add_property( 'font-size', absint( $generate_settings[ 'secondary_navigation_font_size' ] ), $og_defaults[ 'secondary_navigation_font_size' ], 'px' );
	$css->add_property( 'padding-left', absint( $generate_settings[ 'secondary_menu_item' ] ), $og_defaults[ 'secondary_menu_item' ], 'px' );
	$css->add_property( 'padding-right', absint( $generate_settings[ 'secondary_menu_item' ] ), $og_defaults[ 'secondary_menu_item' ], 'px' );
	$css->add_property( 'line-height', absint( $generate_settings[ 'secondary_menu_item_height' ] ), $og_defaults[ 'secondary_menu_item_height' ], 'px' );
	$css->add_property( 'background-image', !empty( $generate_settings['nav_item_image'] ) ? 'url(' . esc_url( $generate_settings['nav_item_image'] ) . ')' : '' );
	$css->add_property( 'background-repeat', esc_attr( $generate_settings['nav_item_repeat'] ) );
	
	// Mobile menu text on hover
	$css->set_selector( 'button.secondary-menu-toggle:hover,button.secondary-menu-toggle:focus' );
	$css->add_property( 'color', esc_attr( $generate_settings[ 'navigation_text_color' ] ) );
	
	// Widget area navigation
	$css->set_selector( '.widget-area .secondary-navigation' );
	$css->add_property( 'margin-bottom', absint( $separator ), false, 'px' );
	
	// Sub-navigation background
	$css->set_selector( '.secondary-navigation ul ul' );
	$css->add_property( 'background-color', esc_attr( $generate_settings[ 'subnavigation_background_color' ] ) );
	$css->add_property( 'top', 'auto' ); // Added for compatibility purposes on 22/12/2016
	
	// Sub-navigation text
	$css->set_selector( '.secondary-navigation .main-nav ul ul li a' );
	$css->add_property( 'color', esc_attr( $generate_settings[ 'subnavigation_text_color' ] ) );
	$css->add_property( 'font-size', absint( $generate_settings[ 'secondary_navigation_font_size' ] - 1 ), absint( $og_defaults[ 'secondary_navigation_font_size' ] - 1 ), 'px' );
	$css->add_property( 'padding-left', absint( $generate_settings[ 'secondary_menu_item' ] ), $og_defaults[ 'secondary_menu_item' ], 'px' );
	$css->add_property( 'padding-right', absint( $generate_settings[ 'secondary_menu_item' ] ), $og_defaults[ 'secondary_menu_item' ], 'px' );
	$css->add_property( 'padding-top', absint( $generate_settings[ 'secondary_sub_menu_item_height' ] ), $og_defaults[ 'secondary_sub_menu_item_height' ], 'px' );
	$css->add_property( 'padding-bottom', absint( $generate_settings[ 'secondary_sub_menu_item_height' ] ), $og_defaults[ 'secondary_sub_menu_item_height' ], 'px' );
	$css->add_property( 'background-image', !empty( $generate_settings['sub_nav_item_image'] ) ? 'url(' . esc_url( $generate_settings['sub_nav_item_image'] ) . ')' : '' );
	$css->add_property( 'background-repeat', esc_attr( $generate_settings['sub_nav_item_repeat'] ) );
	
	// Menu item padding on RTL
	if ( is_rtl() ) {
		$css->set_selector( 'nav.secondary-navigation .main-nav ul li.menu-item-has-children > a' );
		$css->add_property( 'padding-right', absint( $generate_settings[ 'secondary_menu_item' ] ), $og_defaults[ 'secondary_menu_item' ], 'px' );
	}
	
	// Dropdown arrow
	$css->set_selector( '.secondary-navigation .menu-item-has-children ul .dropdown-menu-toggle' );
	$css->add_property( 'padding-top', absint( $generate_settings[ 'secondary_sub_menu_item_height' ] ), $og_defaults[ 'secondary_sub_menu_item_height' ], 'px' );
	$css->add_property( 'padding-bottom', absint( $generate_settings[ 'secondary_sub_menu_item_height' ] ), $og_defaults[ 'secondary_sub_menu_item_height' ], 'px' );
	$css->add_property( 'margin-top', '-' . absint( $generate_settings[ 'secondary_sub_menu_item_height' ] ), '-' . absint( $og_defaults[ 'secondary_sub_menu_item_height' ] ), 'px' );
	
	// Dropdown arrow
	$css->set_selector( '.secondary-navigation .menu-item-has-children .dropdown-menu-toggle' );
	$css->add_property( 'padding-right', absint( $generate_settings[ 'secondary_menu_item' ] ), $og_defaults[ 'secondary_menu_item' ], 'px' );
	
	// Sub-navigation dropdown arrow
	$css->set_selector( '.secondary-navigation .menu-item-has-children ul .dropdown-menu-toggle' );
	$css->add_property( 'padding-top', absint( $generate_settings[ 'secondary_sub_menu_item_height' ] ), $og_defaults[ 'secondary_sub_menu_item_height' ], 'px' );
	$css->add_property( 'padding-bottom', absint( $generate_settings[ 'secondary_sub_menu_item_height' ] ), $og_defaults[ 'secondary_sub_menu_item_height' ], 'px' );
	$css->add_property( 'margin-top', '-' . absint( $generate_settings[ 'secondary_sub_menu_item_height' ] ), '-' . absint( $og_defaults[ 'secondary_sub_menu_item_height' ] ), 'px' );
	
	// Navigation background/text on hover
	$css->set_selector( '.secondary-navigation .main-nav ul li > a:hover,.secondary-navigation .main-nav ul li > a:focus,.secondary-navigation .main-nav ul li.sfHover > a' );
	$css->add_property( 'color', esc_attr( $generate_settings[ 'navigation_text_hover_color' ] ) );
	$css->add_property( 'background-color', esc_attr( $generate_settings[ 'navigation_background_hover_color' ] ) );
	$css->add_property( 'background-image', !empty( $generate_settings[ 'nav_item_hover_image' ] ) ? 'url(' . esc_url( $generate_settings[ 'nav_item_hover_image' ] )	. ')' : '' );
	$css->add_property( 'background-repeat', esc_attr( $generate_settings['nav_item_hover_repeat'] ) );
	
	// Sub-Navigation background/text on hover
	$css->set_selector( '.secondary-navigation .main-nav ul ul li > a:hover,.secondary-navigation .main-nav ul ul li > a:focus,.secondary-navigation .main-nav ul ul li.sfHover > a' );
	$css->add_property( 'color', esc_attr( $generate_settings[ 'subnavigation_text_hover_color' ] ) );
	$css->add_property( 'background-color', esc_attr( $generate_settings[ 'subnavigation_background_hover_color' ] ) );
	$css->add_property( 'background-image', !empty( $generate_settings[ 'sub_nav_item_hover_image' ] ) ? 'url(' . esc_url( $generate_settings[ 'sub_nav_item_hover_image' ] )	. ')' : '' );
	$css->add_property( 'background-repeat', esc_attr( $generate_settings['sub_nav_item_hover_repeat'] ) );
	
	// Navigation background / text current + hover
	$css->set_selector( '.secondary-navigation .main-nav ul li[class*="current-menu-"] > a, .secondary-navigation .main-nav ul li[class*="current-menu-"] > a:hover,.secondary-navigation .main-nav ul li[class*="current-menu-"].sfHover > a' );
	$css->add_property( 'color', esc_attr( $generate_settings[ 'navigation_text_current_color' ] ) );
	$css->add_property( 'background-color', esc_attr( $generate_settings[ 'navigation_background_current_color' ] ) );
	$css->add_property( 'background-image', !empty( $generate_settings[ 'nav_item_current_image' ] ) ? 'url(' . esc_url( $generate_settings[ 'nav_item_current_image' ] )	. ')' : '' );
	$css->add_property( 'background-repeat', esc_attr( $generate_settings['nav_item_current_repeat'] ) );
	
	// Sub-Navigation background / text current + hover
	$css->set_selector( '.secondary-navigation .main-nav ul ul li[class*="current-menu-"] > a,.secondary-navigation .main-nav ul ul li[class*="current-menu-"] > a:hover,.secondary-navigation .main-nav ul ul li[class*="current-menu-"].sfHover > a' );
	$css->add_property( 'color', esc_attr( $generate_settings[ 'subnavigation_text_current_color' ] ) );
	$css->add_property( 'background-color', esc_attr( $generate_settings[ 'subnavigation_background_current_color' ] ) );
	$css->add_property( 'background-image', !empty( $generate_settings[ 'sub_nav_item_current_image' ] ) ? 'url(' . esc_url( $generate_settings[ 'sub_nav_item_current_image' ] )	. ')' : '' );
	$css->add_property( 'background-repeat', esc_attr( $generate_settings['sub_nav_item_current_repeat'] ) );
	
	// RTL menu item padding
	if ( is_rtl() ) {
		$css->set_selector( '.secondary-navigation .main-nav ul li.menu-item-has-children > a' );
		$css->add_property( 'padding-right', absint( $generate_settings[ 'secondary_menu_item' ] ), false, 'px' );
	}
	
	// Return our dynamic CSS
	return $css->css_output();
}
endif;

if ( ! function_exists( 'generate_secondary_color_scripts' ) ) :
/**
 * Enqueue scripts and styles
 */
add_action( 'wp_enqueue_scripts', 'generate_secondary_color_scripts', 110 );
function generate_secondary_color_scripts() {
	// Bail if no Secondary menu is set
	if ( ! has_nav_menu( 'secondary' ) ) {
		return;
	}
	
	wp_add_inline_style( 'generate-secondary-nav', generate_secondary_nav_css() );
}
endif;

if ( ! function_exists( 'generate_secondary_navigation_class' ) ) :
/**
 * Display the classes for the secondary navigation.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_secondary_navigation_class( $class = '' ) {
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', generate_get_secondary_navigation_class( $class ) ) . '"';
}
endif;

if ( ! function_exists( 'generate_get_secondary_navigation_class' ) ) :
/**
 * Retrieve the classes for the secondary navigation.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_secondary_navigation_class( $class = '' ) {

	$classes = array();

	if ( !empty($class) ) {
		if ( !is_array( $class ) )
			$class = preg_split('#\s+#', $class);
		$classes = array_merge($classes, $class);
	}

	$classes = array_map('esc_attr', $classes);

	return apply_filters('generate_secondary_navigation_class', $classes, $class);
}
endif;

if ( ! function_exists( 'generate_secondary_menu_class' ) ) :
/**
 * Display the classes for the secondary navigation.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_secondary_menu_class( $class = '' ) {
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', generate_get_secondary_menu_class( $class ) ) . '"';
}
endif;

if ( ! function_exists( 'generate_get_secondary_menu_class' ) ) :
/**
 * Retrieve the classes for the secondary navigation.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function generate_get_secondary_menu_class( $class = '' ) {

	$classes = array();

	if ( !empty($class) ) {
		if ( !is_array( $class ) )
			$class = preg_split('#\s+#', $class);
		$classes = array_merge($classes, $class);
	}

	$classes = array_map('esc_attr', $classes);

	return apply_filters('generate_secondary_menu_class', $classes, $class);
}
endif;

if ( ! function_exists( 'generate_inside_secondary_navigation_class' ) ) :
/**
 * Display the classes for the inner navigation.
 *
 * @since 0.1
 * @param string|array $class One or more classes to add to the class list.
 */
function generate_inside_secondary_navigation_class( $class = '' ) {
	$classes = array();

	if ( !empty($class) ) {
		if ( !is_array( $class ) )
			$class = preg_split('#\s+#', $class);
		$classes = array_merge($classes, $class);
	}

	$classes = array_map('esc_attr', $classes);

	$return = apply_filters('generate_inside_secondary_navigation_class', $classes, $class);
	
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', $return ) . '"';
}
endif;

if ( ! function_exists( 'generate_hidden_secondary_navigation' ) && function_exists( 'is_customize_preview' ) ) :
/**
 * Adds a hidden navigation if no navigation is set
 * This allows us to use postMessage to position the navigation when it doesn't exist
 */
add_action( 'wp_footer','generate_hidden_secondary_navigation' );
function generate_hidden_secondary_navigation()
{
	if ( is_customize_preview() && function_exists( 'generate_secondary_navigation_position' ) ) {
		?>
		<div style="display:none;">
			<?php generate_secondary_navigation_position(); ?>
		</div>
		<?php
	}
}
endif;

if ( ! function_exists( 'generate_secondary_nav_remove_top_bar' ) ) :
/**
 * Remove the top bar and add it to the Secondary Navigation if it's set
 */
add_action( 'wp','generate_secondary_nav_remove_top_bar' );
function generate_secondary_nav_remove_top_bar()
{
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	
	if ( $generate_settings[ 'merge_top_bar' ] && 'secondary-nav-above-header' == $generate_settings[ 'secondary_nav_position_setting' ] && has_nav_menu( 'secondary' ) && is_active_sidebar( 'top-bar' ) ) {
		remove_action( 'generate_before_header','generate_top_bar', 5 );
		add_action( 'generate_inside_secondary_navigation','generate_secondary_nav_top_bar_widget', 5 );
		add_filter( 'generate_is_top_bar_active', '__return_false' );
	}
}
endif;

if ( ! function_exists( 'generate_secondary_nav_top_bar_widget' ) ) :
/**
 * Build the top bar widget area
 * This is placed into the secondary navigation if set
 */
function generate_secondary_nav_top_bar_widget()
{
	if ( ! is_active_sidebar( 'top-bar' ) )
		return;
	?>
	<div class="top-bar">
		<div class="inside-top-bar">
			<?php dynamic_sidebar( 'top-bar' ); ?>
		</div>
	</div>
	<?php
}
endif;