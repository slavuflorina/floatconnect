<?php
/**
 * Main plugin application.
 *
 * @package Fcon
 */

declare( strict_types=1 );

namespace FloriPlugins\Fcon;

use FloriPlugins\Fcon\Admin\Admin;
use FloriPlugins\Fcon\Assets\Assets;
use FloriPlugins\Fcon\Frontend\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Boots the Fcon plugin components.
 */
final class Application {

	/**
	 * Admin component.
	 *
	 * @var Admin
	 */
	private Admin $admin;

	/**
	 * Assets component.
	 *
	 * @var Assets
	 */
	private Assets $assets;

	/**
	 * Frontend component.
	 *
	 * @var Frontend
	 */
	private Frontend $frontend;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->admin    = new Admin();
		$this->assets   = new Assets();
		$this->frontend = new Frontend();
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
