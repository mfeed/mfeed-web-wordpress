<?php
/**
 * XO Security Two Factor Authentication.
 *
 * @package xo-security
 * @since 3.9.0
 */

/**
 * XO Security Two Factor Authentication class.
 *
 * @since 3.9.0
 */
class XO_Security_Two_Factor {
	/**
	 * Parent object.
	 *
	 * @since 3.9.0
	 * @var XO_Security
	 */
	private $parent;

	/**
	 * Whether the current login has already passed two-factor authentication.
	 *
	 * @since 3.11.0
	 * @var bool
	 */
	private $two_factor_verified = false;

	/**
	 * Construction.
	 *
	 * @since 3.9.0
	 *
	 * @param XO_Security $parent_object XO_Security object.
	 */
	public function __construct( $parent_object ) {
		require_once 'class-xo-security-google-authenticator.php';

		$this->parent = $parent_object;

		add_action( 'plugins_loaded', array( $this, 'setup' ) );
	}

	/**
	 * Plugin setup.
	 *
	 * @since 3.9.0
	 */
	public function setup() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'show_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'user_profile_update_errors', array( $this, 'user_profile_update_errors' ), 10, 3 );
		add_action( 'login_init', array( $this, 'login_init' ), 0 );
		add_action( 'wp_login', array( $this, 'wp_login' ), 0, 2 );
		add_filter( 'login_message', array( $this, 'login_message' ) );
	}

	/**
	 * Determine if the user has a role.
	 *
	 * @since 3.9.0
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 * @return bool
	 */
	private function has_user_role( $user ) {
		if ( is_multisite() && is_super_admin( $user->ID ) ) {
			return true;
		}

		if ( isset( $this->parent->options['two_factor_roles'] ) ) {
			$role = null;
			if ( isset( $user->role ) ) {
				$role = $user->role;
			} elseif ( isset( $user->roles ) && 0 < count( $user->roles ) ) {
				$role = $user->roles[0];
			}
			if ( $role && in_array( $role, (array) $this->parent->options['two_factor_roles'], true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 3.9.0
	 */
	public function enqueue_assets() {
		wp_register_script( 'xo-security-qrcode', XO_SECURITY_URL . '/js/qrcode.min.js', array(), XO_SECURITY_VERSION, false );
	}

	/**
	 * Display Two-Factor on the 'Edit User' screen.
	 *
	 * @since 3.9.0
	 *
	 * @param object $user A WP_User object.
	 */
	public function edit_user_profile( $user ) {
		if ( ! $this->has_user_role( $user ) ) {
			return;
		}

		if ( isset( $_REQUEST['xo_security_two_factor_secret_key'] ) ) {
			check_admin_referer( 'update-user_' . $user->ID );
			$secret_key = sanitize_text_field( wp_unslash( $_REQUEST['xo_security_two_factor_secret_key'] ) );
		} else {
			$ga         = new XO_Security_Google_Authenticator();
			$secret_key = $ga->create_secret();
		}

		$issuer     = get_bloginfo( 'name', 'display' );
		$totp_title = $issuer . ': ' . $user->user_login;
		$totp_url   = add_query_arg(
			array(
				'secret' => rawurlencode( $secret_key ),
				'issuer' => rawurlencode( $issuer ),
			),
			'otpauth://totp/' . rawurlencode( $totp_title )
		);

		$enable = get_user_meta( $user->ID, 'xo_security_two_factor_enable', true );
		$enable = ! empty( $enable );

		echo '<h2>' . esc_html__( 'Two-factor authentication settings', 'xo-security' ) . '</h2>';
		echo '<table class="form-table" role="presentation"><tbody>';

		if ( $enable ) :
			?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Two-factor authentication', 'xo-security' ); ?></th>
				<td>
					<label for="xo_security_two_factor_enable">
						<input name="xo_security_two_factor_enable" type="checkbox" id="xo_security_two_factor_enable" value="true" <?php checked( true, $enable ); ?> />
						<?php esc_html_e( 'Enable', 'xo-security' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Turn it off if you want to reset it.', 'xo-security' ); ?></p>
				</td>
			</tr>
		<?php else : ?>
			<tr>
				<th><label for="xo_security_two_factor_secret_key"><?php esc_html_e( 'Secret key', 'xo-security' ); ?></label></th>
				<td>
				<input type="text" name="xo_security_two_factor_secret_key" id="xo_security_two_factor_secret_key" readonly="readonly" value="<?php echo esc_attr( $secret_key ); ?>" class="regular-text" />
				<div id="xo_security_qrcode" style="background-color: #fff; margin: 16px 0; padding: 20px; width: 200px;"></div>
				<p class="description"><?php esc_html_e( 'Scan the QR code with the Google Authenticator app or manually enter the secret key.', 'xo-security' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="xo_security_two_factor_authenticator_code"><?php esc_html_e( 'Authentication Code', 'xo-security' ); ?></label></th>
				<td>
				<input type="text" name="xo_security_two_factor_authenticator_code" id="xo_security_two_factor_authenticator_code" value="" class="regular-text" maxlength="6" autocomplete="off" spellcheck="false" />
				<p class="description"><?php esc_html_e( 'Entering the correct verification code will enable two-factor authentication.', 'xo-security' ); ?></p>
				</td>
			</tr>
			<?php
			$js =
				'var xo_security_qrcode = new QRCode(document.getElementById("xo_security_qrcode"), {
					text: "' . esc_url( $totp_url, array( 'otpauth' ) ) . '",
					width: 200,
					height: 200,
					colorDark : "#000000",
					colorLight : "#ffffff",
					correctLevel : QRCode.CorrectLevel.M
				});';

			wp_enqueue_script( 'xo-security-qrcode' );
			wp_add_inline_script( 'xo-security-qrcode', $js );
		endif;
		echo '</tbody></table>';
	}

	/**
	 * Fires before user profile update errors are returned.
	 *
	 * @since 3.9.0
	 *
	 * @param WP_Error $errors WP_Error object (passed by reference).
	 * @param bool     $update Whether this is a user update.
	 * @param stdClass $user   User object (passed by reference).
	 */
	public function user_profile_update_errors( $errors, $update, $user ) {
		if ( ! $update ) {
			return;
		}

		if ( empty( $user->ID ) ) {
			return;
		}

		check_admin_referer( 'update-user_' . $user->ID );

		if ( ! isset( $_POST['xo_security_two_factor_enable'] ) ) {
			update_user_meta( $user->ID, 'xo_security_two_factor_enable', false );
		}

		if ( empty( $_POST['xo_security_two_factor_secret_key'] ) || empty( $_POST['xo_security_two_factor_authenticator_code'] ) ) {
			return;
		}

		$key  = sanitize_text_field( wp_unslash( $_POST['xo_security_two_factor_secret_key'] ) );
		$code = sanitize_text_field( wp_unslash( $_POST['xo_security_two_factor_authenticator_code'] ) );

		$ga = new XO_Security_Google_Authenticator();
		if ( $ga->verify_code( $key, $code, 2 ) ) {
			update_user_meta( $user->ID, 'xo_security_two_factor_enable', true );
			update_user_meta( $user->ID, 'xo_security_two_factor_secret_key', $key );
		} else {
			update_user_meta( $user->ID, 'xo_security_two_factor_enable', false );
			$errors->add( 'authenticator_code_error', __( '<strong>Error:</strong> Authentication code is incorrect.', 'xo-security' ) );
		}
	}

	/**
	 * Get the transient key for a two-factor challenge token.
	 *
	 * @since 3.11.0
	 *
	 * @param string $token Challenge token.
	 * @return string Transient key.	 
	 */
	private function get_challenge_key( $token ) {
		return 'xo_security_2fa_' . hash( 'sha256', $token );
	}

	/**
	 * Create a server-side two-factor challenge.
	 *
	 * @since 3.11.0
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 * @return string Challenge token.
	 */
	private function create_challenge( $user ) {
		$token       = wp_generate_password( 32, false, false );
		$rememberme  = ! empty( $_REQUEST['rememberme'] );
		$redirect_to = isset( $_REQUEST['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_REQUEST['redirect_to'] ), admin_url() ) : admin_url(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		set_transient(
			$this->get_challenge_key( $token ),
			array(
				'user_id'     => $user->ID,
				'rememberme'  => $rememberme,
				'redirect_to' => $redirect_to,
				'expires'     => time() + 5 * MINUTE_IN_SECONDS,
				'attempts'    => 0,
			),
			5 * MINUTE_IN_SECONDS
		);
		return $token;
	}

	/**
	 * Display the two-factor authentication form.
	 *
	 * @since 3.11.0
	 *
	 * @param string $token       Challenge token.
	 * @param string $redirect_to URL to redirect to after login.
	 * @param string $error       Error message.
	 */
	private function display_authenticator_form( $token, $redirect_to, $error = '' ) {
		login_header( 'Authenticator Code Form' );

		if ( '' !== $error ) {
			echo '<div id="login_error" class="notice notice-error"><p>' . wp_kses( $error, array( 'strong' => array() ) ) . '</p></div>';
			add_action( 'login_footer', 'wp_shake_js', 12 );
		}

		?>
		<form name="loginform" id="loginform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
			<?php wp_nonce_field( 'xo_security_authenticator_code_form', 'xo_security_authenticator_nonce' ); ?>
			<input type="hidden" name="action" value="xo_security_two_factor" />
			<input type="hidden" name="xo_security_two_factor_token" value="<?php echo esc_attr( $token ); ?>" />
			<p>
				<label for="google_authenticator_code"><?php esc_html_e( 'Authentication Code', 'xo-security' ); ?></label>
				<input type="text" name="google_authenticator_code" id="google_authenticator_code" class="input" value="" maxlength="6" size="6" required="required" autocomplete="off" spellcheck="false" autofocus />
			</p>
			<p class="submit">
				<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php echo esc_attr( 'Log In' ); ?>" />
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
				<input type="hidden" name="testcookie" value="1" />
			</p>
		</form>
		<?php

		$script =
			'function xo_security_attempt_focus() {
				setTimeout( function() {
					try {
						d = document.getElementById( "google_authenticator_code" );
						d.focus();
					} catch( er ) {}
				}, 200);
			}
			xo_security_attempt_focus();';

		echo '<script type="text/javascript">' . $script . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		login_footer();

		exit;
	}

	/**
	 * Redirect to the login page with a two-factor error message.
	 *
	 * @since 3.11.0
	 *
	 * @param string $error Error code.
	 */
	private function redirect_to_login( $error ) {
		$login_url = add_query_arg(
			'xo_security_two_factor_error',
			$error,
			site_url( 'wp-login.php', 'login' )
		);

		wp_safe_redirect( $login_url );
		exit;
	}

	/**
	 * Filter the login message for two-factor errors.
	 *
	 * @since 3.11.0
	 *
	 * @param string $message Login message.
	 * @return string Login message.
	 */
	public function login_message( $message ) {
		if ( empty( $_GET['xo_security_two_factor_error'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $message;
		}

		$error = sanitize_key( wp_unslash( $_GET['xo_security_two_factor_error'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'too_many_attempts' === $error ) {
			return '<div id="login_error" class="notice notice-error"><p><strong>' . esc_html__( 'Error:', 'xo-security' ) . '</strong> ' . esc_html__( 'Too many authentication attempts. Please log in again.', 'xo-security' ) . '</p></div>';
		}

		if ( 'expired' === $error ) {
			return '<div id="login_error" class="notice notice-error"><p><strong>' . esc_html__( 'Error:', 'xo-security' ) . '</strong> ' . esc_html__( 'The two-factor login session has expired. Please log in again.', 'xo-security' ) . '</p></div>';
		}

		return $message;
	}

	/**
	 * Handle the two-factor authentication challenge submission.
	 *
	 * @since 3.11.0
	 */
	public function login_init() {
		if ( ! isset( $_REQUEST['action'] ) || 'xo_security_two_factor' !== $_REQUEST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$token       = isset( $_POST['xo_security_two_factor_token'] ) ? sanitize_text_field( wp_unslash( $_POST['xo_security_two_factor_token'] ) ) : '';
		$redirect_to = isset( $_POST['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_POST['redirect_to'] ), admin_url() ) : admin_url();

		if (
			empty( $_POST['xo_security_authenticator_nonce'] )
			|| ! wp_verify_nonce( wp_unslash( $_POST['xo_security_authenticator_nonce'] ), 'xo_security_authenticator_code_form' )
		) {
			$this->display_authenticator_form( $token, $redirect_to, __( '<strong>Error:</strong> The login session is invalid. Please try again.', 'xo-security' ) );
		}

		if ( '' === $token ) {
			$this->redirect_to_login( 'expired' );
		}

		$challenge_key = $this->get_challenge_key( $token );
		$data          = get_transient( $challenge_key );
		if ( empty( $data['user_id'] ) ) {
			$this->redirect_to_login( 'expired' );
		}

		$user = get_user_by( 'id', $data['user_id'] );
		if ( ! $user ) {
			delete_transient( $challenge_key );
			$this->redirect_to_login( 'expired' );
		}

		$key  = get_user_option( 'xo_security_two_factor_secret_key', $user->ID );
		$code = isset( $_POST['google_authenticator_code'] ) ? sanitize_text_field( wp_unslash( $_POST['google_authenticator_code'] ) ) : '';

		if ( '' === $code ) {
			$this->display_authenticator_form( $token, $redirect_to, __( '<strong>Error:</strong> No authentication code entered.', 'xo-security' ) );
		}

		$expires = isset( $data['expires'] ) ? (int) $data['expires'] : time() + 5 * MINUTE_IN_SECONDS;
		if ( time() >= $expires ) {
			delete_transient( $challenge_key );
			$this->redirect_to_login( 'expired' );
		}

		$ga = new XO_Security_Google_Authenticator();
		if ( ! $ga->verify_code( $key, $code, 2 ) ) {
			$data['attempts'] = isset( $data['attempts'] ) ? (int) $data['attempts'] + 1 : 1;
			$this->parent->failed_login( $user->user_login );

			$remaining = $expires - time();
			if ( 0 >= $remaining ) {
				delete_transient( $challenge_key );
				$this->redirect_to_login( 'expired' );
			}

			if ( 3 <= $data['attempts'] ) {
				delete_transient( $challenge_key );
				$this->redirect_to_login( 'too_many_attempts' );
			}

			set_transient( $challenge_key, $data, $remaining );
			$this->display_authenticator_form( $token, $redirect_to, __( '<strong>Error:</strong> Authentication code is incorrect.', 'xo-security' ) );
		}

		delete_transient( $challenge_key );

		$this->two_factor_verified = true;

		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, ! empty( $data['rememberme'] ) );

		do_action( 'wp_login', $user->user_login, $user );

		wp_safe_redirect( isset( $data['redirect_to'] ) ? $data['redirect_to'] : $redirect_to );
		exit;
	}

	/**
	 * Handle the browser-based login.
	 *
	 * @since 3.11.0
	 *
	 * @param string  $user_login Username.
	 * @param WP_User $user WP_User object of the logged-in user.
	 */
	public function wp_login( $user_login, $user ) {
		if ( $this->two_factor_verified ) {
			return;
		}

		$enable = get_user_option( 'xo_security_two_factor_enable', $user->ID );

		if ( ! $enable ) {
			return;
		}

		$key = get_user_option( 'xo_security_two_factor_secret_key', $user->ID );

		if ( empty( $key ) ) {
			return;
		}

		if ( ! $this->has_user_role( $user ) ) {
			return;
		}

		$token       = $this->create_challenge( $user );
		$redirect_to = isset( $_REQUEST['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_REQUEST['redirect_to'] ), admin_url() ) : admin_url(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		wp_logout();

		if ( ! empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
			unset( $_COOKIE[ LOGGED_IN_COOKIE ] );
		}

		$this->display_authenticator_form( $token, $redirect_to );
	}
}
