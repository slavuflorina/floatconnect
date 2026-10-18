<?php
/**
 * Main plugin application.
 *
 * @package Floatconnect
 */

declare( strict_types=1 );

namespace FloriPlugins\Floatconnect;

use FloriPlugins\Floatconnect\Admin\FLOATCONNECT_Admin;
use FloriPlugins\Floatconnect\Assets\FLOATCONNECT_Assets;
use FloriPlugins\Floatconnect\Frontend\FLOATCONNECT_Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Boots the Floatconnect plugin components.
 */
final class FLOATCONNECT_Application {

	/**
	 * Admin component.
	 *
	 * @var FLOATCONNECT_Admin
	 */
	private FLOATCONNECT_Admin $admin;

	/**
	 * Assets component.
	 *
	 * @var FLOATCONNECT_Assets
	 */
	private FLOATCONNECT_Assets $assets;

	/**
	 * Frontend component.
	 *
	 * @var FLOATCONNECT_Frontend
	 */
	private FLOATCONNECT_Frontend $frontend;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->admin    = new FLOATCONNECT_Admin();
		$this->assets   = new FLOATCONNECT_Assets();
		$this->frontend = new FLOATCONNECT_Frontend();
	}

	/**
	 * Boot plugin components.
	 *
	 * @return void
	 */
	public function boot(): void {
		$this->admin->boot();
		$this->assets->boot();
		$this->frontend->boot();
	}
}
