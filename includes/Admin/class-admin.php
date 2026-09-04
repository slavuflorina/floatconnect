<?php
/**
 * FloatConnect admin functionality.
 *
 * @package FloatConnect
 */

declare( strict_types=1 );

namespace FloriPlugins\FloatConnect\Admin;

use FloriPlugins\FloatConnect\Admin\Pages\Dashboard;

defined( 'ABSPATH' ) || exit;

/**
 * Handles FloatConnect admin functionality.
 */
final class Admin {

	/**
	 * Dashboard page.
	 *
	 * @var Dashboard
	 */
	private Dashboard $dashboard;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->dashboard = new Dashboard();
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
	 * Register FloatConnect admin menu.
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
