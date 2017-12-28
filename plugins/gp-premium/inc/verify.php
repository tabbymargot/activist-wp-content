<?php
defined( 'WPINC' ) or die;

/**
 * These functions are needed for product activation
 * The functions below are the same throughout all addons
 */
 
if ( ! function_exists( 'generate_verify_styles' ) ) :
add_action( 'admin_print_styles', 'generate_verify_styles' );
function generate_verify_styles() {
	$css = '';

	// If the old email activation isn't present, we can hide the old metabox
	if ( ! function_exists( 'generate_verify_email' ) ) {
		$css = '#gen-license-keys{display:none;}';
	}

	$css .= '.license-key-container {
				margin-bottom:15px;
			}
			.license-key-container:last-child {
				margin:0;
			}
			.update-help {
				float: right;
				text-decoration: none;
			}
			.license-key-container label {
				font-size: 11px;
				font-weight: normal;
				color: #777;
				display: inline-block;
				margin-bottom: 0;
			}
			.status {
				position: absolute;
				right:10px;
				top:-1px;
				background:rgba(255,255,255,0.9);
			}
			.license-key-input {
				width:100%;
				box-sizing:border-box;
				padding:10px;
			}
			.license-key-input::-webkit-credentials-auto-fill-button {
				visibility: hidden;
				pointer-events: none;
				position: absolute;
				right: 0;
			}
			.license-key-button {
				position:relative;
				top:1px;
				width:100%;
				box-sizing:border-box;
				padding: 10px !important;
				height:auto !important;
				line-height:normal !important;
			}';

	wp_add_inline_style( 'generate-options', $css );
}
endif;

if ( ! function_exists( 'generate_license_errors' ) ) :
/*
* Set up errors and messages
*/
add_action( 'admin_notices', 'generate_license_errors' );
function generate_license_errors() {
	if ( isset( $_GET['generate-message'] ) && 'deactivation_passed' == $_GET['generate-message'] ) {
		add_settings_error( 'generate-license-notices', 'deactivation_passed', __( 'License deactivated.', 'gp-premium' ), 'updated' );
	}

	if ( isset( $_GET['generate-message'] ) && 'license_activated' == $_GET['generate-message'] ) {
		add_settings_error( 'generate-license-notices', 'license_activated', __( 'License activated.', 'gp-premium' ), 'updated' );
	}

	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch ( $_GET['sl_activation'] ) {

		case 'false':
			$message = urldecode( $_GET['message'] );
			add_settings_error( 'generate-license-notices', 'license_failed', $message, 'error' );
			break;

		case 'true':
		default:
			break;

		}
	}

	settings_errors( 'generate-license-notices' );
}
endif;

if ( ! function_exists( 'generate_activation_area' ) ) :
add_action( 'generate_admin_right_panel', 'generate_activation_area' );
function generate_activation_area() {
?>

	<form method="post" action="options.php">
		<?php settings_fields( 'generate-license-group' ); ?>
		<?php do_settings_sections( 'generate-license-group' ); ?>
		<div class="postbox generate-metabox" id="generate-license-keys">
			<h3 class="hndle"><?php _e( 'Updates', 'gp-premium' );?> <a class="update-help" title="<?php _e( 'Help', 'gp-premium' ); ?>" href="https://docs.generatepress.com/article/updating-gp-premium/" target="_blank"><span class="dashicons dashicons-sos"></span></a></h3>
			<div class="inside" style="margin-bottom:0;">
				<?php do_action( 'generate_license_keys' ); ?>
			</div>
		</div>

	</form>
<?php
}
endif;

if ( ! function_exists( 'generate_add_license_key_field' ) ) :
function generate_add_license_key_field( $id, $download, $license_key_status, $license_key, $version ) {
	$license = get_option( $license_key );
	$key = get_option( $license_key_status, 'deactivated' );
	if ( 'valid' == $key ) {
		$message = sprintf( '<span style="color:green;">%s</span>', __( 'Activated', 'gp-premium' ) );
	} else {
		$message = sprintf( '<span style="color:red;">%s</span>', __ ( 'Not activated', 'gp-premium' ) );
	}
?>
	<div class="license-key-container" style="position:relative;">
		<div class="grid-70 grid-parent">
			<span style="position:relative;">
				<input spellcheck="false" class="license-key-input" id="generate_license_key_<?php echo $id;?>" name="generate_license_key_<?php echo $id;?>" type="<?php echo apply_filters( 'generate_premium_license_key_field', 'password' ); ?>" value="<?php echo $license; ?>" placeholder="<?php _e( 'License Key', 'gp-premium' ); ?>" title="<?php echo $download;?> <?php _e( 'License Key', 'gp-premium' ); ?>" />
			</span>
		</div>
		<div class="grid-30 grid-parent">
			<?php wp_nonce_field( 'generate_license_key_' . $id . '_nonce', 'generate_license_key_' . $id . '_nonce' ); ?>
			<input type="submit" id="submit" class="button button-primary license-key-button" name="<?php echo $id;?>_license_key" value="<?php _e( 'Update', 'gp-premium' );?>" />
		</div>
		<div class="clear" style="padding:0;margin:0;border:0;"></div>
		<label for="generate_license_key_<?php echo $id;?>"><?php echo $download; ?> <?php echo $version; ?> - <?php echo $message; ?></label>
		<div class="clear" style="padding:0;margin:0;border:0;"></div>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'generate_process_license_key' ) ) :
/**
 * Activates and deactivates license keys
 */
function generate_process_license_key( $id, $download, $license_key_status, $license_key ) {
	// Has our button been clicked?
	if ( isset( $_POST[ $id . '_license_key' ] ) ) {

		// Get out if we didn't click the button
		if ( ! check_admin_referer( 'generate_license_key_' . $id . '_nonce', 'generate_license_key_' . $id . '_nonce' ) ) {
			return;
		}

		// If we're not an administrator, bail.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Grab the value being saved
		$new = sanitize_key( $_POST['generate_license_key_' . $id] );

		// Get the previously saved value
		$old = get_option( $license_key );

		// Still here? Update our option with the new license key
		update_option( $license_key, $new );

		// If we have a value, run activation.
		if ( '' !== $new ) {
			$api_params = array(
				'edd_action' => 'activate_license',
				'license' => $new,
				'item_name' => urlencode( $download ),
				'url' => home_url()
			);
		}

		// If we don't have a value (it's been cleared), run deactivation.
		if ( '' == $new && 'valid' == get_option( $license_key_status ) ) {
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license' => $old,
				'item_name' => urlencode( $download ),
				'url' => home_url()
			);
		}

		// Nothing? Get out of here.
		if ( ! $api_params ) {
			wp_safe_redirect( admin_url( 'themes.php?page=generate-options' ) );
			exit;
		}

		// Phone home.
		$license_response = wp_remote_post( 'https://generatepress.com', array(
			'timeout'   => 60,
			'sslverify' => false,
			'body'      => $api_params
		) );

		// make sure the response came back okay
		if ( is_wp_error( $license_response ) || 200 !== wp_remote_retrieve_response_code( $license_response ) ) {
			$message = $license_response->get_error_message();
		} else {

			// Still here? Decode our response.
			$license_data = json_decode( wp_remote_retrieve_body( $license_response ) );

			if ( false === $license_data->success ) {

				switch ( $license_data->error ) {

				case 'expired' :

					$message = sprintf(
						__( 'Your license key expired on %s.', 'gp-premium' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'revoked' :

					$message = __( 'Your license key has been disabled.', 'gp-premium' );
					break;

				case 'missing' :

					$message = __( 'Invalid license.', 'gp-premium' );
					break;

				case 'invalid' :
				case 'site_inactive' :

					$message = __( 'Your license is not active for this URL.', 'gp-premium' );
					break;

				case 'item_name_mismatch' :

					$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'gp-premium' ), $download );
					break;

				case 'no_activations_left':

					$message = __( 'Your license key has reached its activation limit.', 'gp-premium' );
					break;

				default :

					$message = __( 'An error occurred, please try again.', 'gp-premium' );
					break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			delete_option( $license_key_status );
			$base_url = admin_url( 'themes.php?page=generate-options' );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), esc_url( $base_url ) );
			wp_redirect( $redirect );
			exit();
		}

		// Update our license key status
		update_option( $license_key_status, $license_data->license );

		if ( 'valid' == $license_data->license ) {
			// Validated, go tell them
			wp_safe_redirect( admin_url( 'themes.php?page=generate-options&generate-message=license_activated' ) );
			exit;
		} elseif ( 'deactivated' == $license_data->license ) {
			// Deactivated, go tell them
			wp_safe_redirect( admin_url( 'themes.php?page=generate-options&generate-message=deactivation_passed' ) );
			exit;
		} else {
			// Failed, go tell them
			wp_safe_redirect( admin_url( 'themes.php?page=generate-options&generate-message=license_failed' ) );
			exit;
		}
	}
}
endif;