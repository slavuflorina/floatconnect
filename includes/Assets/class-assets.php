<?php
/**
 * FloatConnect assets.
 *
 * @package FloatConnect
 */

declare (strict_types = 1);
namespace FloriPlugins\FloatConnect\Assets;

defined( 'ABSPATH' ) || exit;
/**
 * Handles FloatConnect plugin assets.
 */
final class Assets {
	/**
	 * Register asset hooks.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @return void
	 */
	public function enqueue_admin(): void {
		wp_enqueue_style(
			'floatconnect-admin',
			FLOATCONNECT_URL . 'assets/admin/admin.css',
			array(),
			FLOATCONNECT_VERSION
		);
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_frontend(): void {
		if ( ! is_admin() ) {
			wp_enqueue_style(
				'floatconnect-frontend',
				FLOATCONNECT_URL . 'assets/css/frontend.css',
				array(),
				FLOATCONNECT_VERSION
			);
			wp_enqueue_script(
				'floatconnect-frontend',
				FLOATCONNECT_URL . 'assets/js/frontend.js',
				array(),
				FLOATCONNECT_VERSION,
				true
			);
		}
	}
}
