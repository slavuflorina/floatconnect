<?php
/**
 * Plugin Name: FloatConnect
 * Plugin URI: https://github.com/slavuflorina/floatconnect
 * Description: Floating contact button for WordPress with additional contact options and advanced features.
 * Version: 1.0.0
 * Author: Flori
 * Author URI: https://github.com/slavuflorina
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: floatconnect
 * Domain Path: /languages
 *
 * @package Fcon
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Freemius integration.
 */
if ( function_exists( 'fcon_fs' ) ) {
	fcon_fs()->set_basename( false, __FILE__ );
} else {

	if ( ! function_exists( 'fcon_fs' ) ) {

		/**
		 * Create a helper function for easy SDK access.
		 *
		 * @return object
		 */
		function fcon_fs() {
			global $fcon_fs;

			if ( ! isset( $fcon_fs ) ) {
				/**
				 * Include Freemius SDK.
				 */
				require_once __DIR__ . '/vendor/freemius/start.php';

				$fcon_fs = fs_dynamic_init(
					array(
						'id'               => '38182',
						'slug'             => 'floatconnect',
						'premium_slug'     => 'floatconnect-premium',
						'type'             => 'plugin',
						'public_key'       => 'pk_d2ba9ba3c5feb85a0e0ce21514752',
						'is_premium'       => false,
						'premium_suffix'   => 'Premium',
						'has_addons'       => false,
						'has_paid_plans'   => true,
						'is_org_compliant' => true,
						'menu'             => array(
							'slug'    => 'floatconnect',
							'support' => false,
						),
						'is_live'          => true,
					)
				);
			}

			return $fcon_fs;
		}

		/**
		 * Initialize Freemius.
		 */
		fcon_fs();

		/**
		 * Clean up plugin data after uninstall.
		 *
		 * @return void
		 */
		function fcon_fs_uninstall_cleanup(): void {
			delete_option( 'fcon_settings' );
		}

		fcon_fs()->add_action(
			'after_uninstall',
			'fcon_fs_uninstall_cleanup'
		);

		/**
		 * Signal that SDK was initiated.
		 */
		do_action( 'fcon_fs_loaded' );
	}

	/**
	 * Plugin version.
	 */
	define( 'FCON_VERSION', '1.0.0' );

	/**
	 * Plugin path.
	 */
	define( 'FCON_PATH', plugin_dir_path( __FILE__ ) );

	/**
	 * Plugin URL.
	 */
	define( 'FCON_URL', plugin_dir_url( __FILE__ ) );

	/**
	 * Load the autoloader.
	 */
	require_once FCON_PATH . 'includes/class-autoloader.php';

	FloriPlugins\Fcon\Autoloader::register();

	/**
	 * Boot the plugin.
	 */
	add_action(
		'plugins_loaded',
		static function (): void {
			$application = new FloriPlugins\Fcon\Application();
			$application->boot();
		}
	);
}
