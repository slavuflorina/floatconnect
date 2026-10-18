<?php
/**
 * Floatconnect admin functionality.
 *
 * @package Floatconnect
 */

declare( strict_types=1 );

namespace FloriPlugins\Floatconnect\Admin;

use FloriPlugins\Floatconnect\Admin\Pages\FLOATCONNECT_Dashboard;

defined( 'ABSPATH' ) || exit;

/**
 * Handles Floatconnect admin functionality.
 */
final class FLOATCONNECT_Admin {

	/**
	 * Dashboard page.
	 *
	 * @var FLOATCONNECT_Dashboard
	 */
	private FLOATCONNECT_Dashboard $dashboard;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->dashboard = new FLOATCONNECT_Dashboard();
	}

	/**
	 * Register admin hooks.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action(
			'admin_menu',
			array( $this, 'register_menu' )
		);

		$this->dashboard->boot();
	}

	/**
	 * Register Floatconnect admin menu.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		add_menu_page(
			__( 'FloatConnect', 'floatconnect' ),
			__( 'FloatConnect', 'floatconnect' ),
			'manage_options',
			'floatconnect',
			array( $this->dashboard, 'render' ),
			'dashicons-format-chat',
			80
		);
	}
}
