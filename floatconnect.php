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
 * @package FloatConnect
 */

declare (strict_types = 1);
defined( 'ABSPATH' ) || exit;
/**
 * Freemius integration.
 */
if ( function_exists( 'flo_fs' ) ) {
	flo_fs()->set_basename( false, __FILE__ );
} else {
	if ( ! function_exists( 'flo_fs' ) ) {
		/**
		 * Create a helper function for easy SDK access.
		 *
		 * @return object
		 */
		function flo_fs() {
			global $flo_fs;
			if ( ! isset( $flo_fs ) ) {
				/**
				 * Include Freemius SDK.
				 */
				require_once __DIR__ . '/vendor/freemius/start.php';
				$flo_fs = fs_dynamic_init(
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
			return $flo_fs;
		}

		/**
		 * Initialize Freemius.
		 */
		flo_fs();
		/**
		 * Clean up plugin data after uninstall.
		 *
		 * @return void
		 */
		function flo_fs_uninstall_cleanup(): void {
			delete_option( 'floatconnect_settings' );
		}

		flo_fs()->add_action( 'after_uninstall', 'flo_fs_uninstall_cleanup' );
		/**
		 * Signal that SDK was initiated.
		 */
		do_action( 'flo_fs_loaded' );
	}
	/**
	 * Plugin version.
	 */
	define( 'FLOATCONNECT_VERSION', '1.0.0' );
	/**
	 * Plugin path.
	 */
	define( 'FLOATCONNECT_PATH', plugin_dir_path( __FILE__ ) );
	/**
	 * Plugin URL.
	 */
	define( 'FLOATCONNECT_URL', plugin_dir_url( __FILE__ ) );
	/**
	 * Load the autoloader.
	 */
	require_once FLOATCONNECT_PATH . 'includes/class-autoloader.php';
	FloriPlugins\FloatConnect\Autoloader::register();
	/**
	 * Boot the plugin.
	 */
	add_action(
		'plugins_loaded',
		static function (): void {
			$application = new FloriPlugins\FloatConnect\Application();
			$application->boot();
		}
	);
}
