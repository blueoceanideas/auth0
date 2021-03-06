<?php
// phpcs:ignoreFile
/**
 * Class WP_Auth0_Configure_JWTAUTH
 *
 * @deprecated - 3.10.0, plugin is deprecated and removed from the WP plugin repo.
 *
 * @codeCoverageIgnore - Deprecated.
 */
class WP_Auth0_Configure_JWTAUTH {

	protected $a0_options;

	/**
	 * @deprecated - 3.10.0, plugin is deprecated and removed from the WP plugin repo.
	 */
	public function __construct( WP_Auth0_Options $a0_options ) {
		$this->a0_options = $a0_options;
	}

	public function init() {
		add_action( 'admin_action_wpauth0_configure_jwt', array( $this, 'setupjwt' ) );
		add_action( 'plugins_loaded', array( $this, 'check_jwt_auth' ) );
	}

	public function check_jwt_auth() {
		if ( isset( $_REQUEST['page'] ) && 'wpa0-jwt-auth' === $_REQUEST['page'] ) {
			return;
		}

		if ( self::is_jwt_auth_enabled() && ! self::is_jwt_configured() ) {
			add_action( 'admin_notices', array( $this, 'notify_jwt' ) );
		}
	}

	public function notify_jwt() {
		?>
		<div class="update-nag">
			<?php _e( 'JWT Auth installed. ', 'wp-auth0' ); ?>
			<a href="<?php echo admin_url( 'admin.php?page=wpa0-jwt-auth' ); ?>">
				<?php _e( 'To configure it to work with the Auth0 plugin, click here.', 'wp-auth0' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * @deprecated - 3.6.0, not needed, handled in WP_Auth0_Admin::admin_enqueue()
	 *
	 * @codeCoverageIgnore - Deprecated
	 */
	public function admin_enqueue() {
		// phpcs:ignore
		@trigger_error( sprintf( __( 'Method %s is deprecated.', 'wp-auth0' ), __METHOD__ ), E_USER_DEPRECATED );
	}

	public function render_settings_page() {
		$ready = self::is_jwt_configured();
		include WPA0_PLUGIN_DIR . 'templates/configure-jwt-auth.php';
	}

	public function setupjwt() {

		if ( self::is_jwt_auth_enabled() ) {
			JWT_AUTH_Options::set( 'aud', $this->a0_options->get( 'client_id' ) );
			JWT_AUTH_Options::set( 'secret', $this->a0_options->get( 'client_secret' ) );
			JWT_AUTH_Options::set( 'secret_base64_encoded', $this->a0_options->get( 'client_secret_b64_encoded' ) );
			JWT_AUTH_Options::set( 'signing_algorithm', $this->a0_options->get( 'client_signing_algorithm' ) );
			JWT_AUTH_Options::set( 'domain', $this->a0_options->get( 'domain' ) );
			JWT_AUTH_Options::set( 'cache_expiration', $this->a0_options->get( 'cache_expiration' ) );
			JWT_AUTH_Options::set( 'override_user_repo', 'WP_Auth0_UsersRepo' );
			$this->a0_options->set( 'jwt_auth_integration', true );
		}

		wp_redirect( admin_url( 'admin.php?page=wpa0-jwt-auth' ) );

	}

	/**
	 * @deprecated - 3.10.0, plugin is deprecated and removed from the WP plugin repo.
	 *
	 * @return bool
	 */
	public static function is_jwt_auth_enabled() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'wp-jwt-auth/JWT_AUTH.php' );
	}

	/**
	 * @deprecated - 3.10.0, plugin is deprecated and removed from the WP plugin repo.
	 *
	 * @return bool
	 */
	public static function is_jwt_configured() {
		$options = WP_Auth0_Options::Instance();
		return (
			JWT_AUTH_Options::get( 'aud' ) === $options->get( 'client_id' ) &&
			JWT_AUTH_Options::get( 'secret' ) === $options->get( 'client_secret' ) &&
			JWT_AUTH_Options::get( 'secret_base64_encoded' ) === $options->get( 'client_secret_b64_encoded' ) &&
			JWT_AUTH_Options::get( 'signing_algorithm' ) === $options->get( 'client_signing_algorithm' ) &&
			JWT_AUTH_Options::get( 'domain' ) === $options->get( 'domain' ) &&
			JWT_AUTH_Options::get( 'cache_expiration' ) === $options->get( 'cache_expiration' ) &&
			$options->get( 'jwt_auth_integration' ) &&
			JWT_AUTH_Options::get( 'jwt_attribute' ) === 'sub'
		);
	}

}
