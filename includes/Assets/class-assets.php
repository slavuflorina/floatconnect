<?php
/**
 * Fcon assets.
 *
 * @package Fcon
 */

declare( strict_types=1 );

namespace FloriPlugins\Fcon\Assets;

defined( 'ABSPATH' ) || exit;

/**
 * Handles Fcon plugin assets.
 */
final class Assets {

	/**
	 * Register asset hooks.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action(
			'admin_enqueue_scripts',
			array( $this, 'enqueue_admin' )
		);

		add_action(
			'wp_enqueue_scripts',
			array( $this, 'enqueue_frontend' )
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @return void
	 */
	public function enqueue_admin(): void {
		wp_enqueue_style(
			'fcon-admin',
			FCON_URL . 'assets/admin/admin.css',
			array(),
			FCON_VERSION
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
				'fcon-frontend',
				FCON_URL . 'assets/css/frontend.css',
				array(),
				FCON_VERSION
			);

			wp_enqueue_script(
				'fcon-frontend',
				FCON_URL . 'assets/js/frontend.js',
				array(),
				FCON_VERSION,
				true
			);
		}
	}
}
