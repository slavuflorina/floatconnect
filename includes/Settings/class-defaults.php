<?php
/**
 * Default FloatConnect plugin settings.
 *
 * @package FloatConnect
 */

declare( strict_types=1 );

namespace FloriPlugins\FloatConnect\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Provides default FloatConnect plugin settings.
 */
final class Defaults {

	/**
	 * Get default settings.
	 *
	 * @return array<string, mixed>
	 */
	public static function all(): array {
		return array(
			'enabled'       => true,
			'position'      => 'bottom-right',
			'button_size'   => 60,
			'icon_size'     => 32,
			'border_radius' => 50,
			'shadow'        => 'soft',
			'animation'     => 'none',
			'hover_effect'  => true,
			'bottom_offset' => 24,
			'side_offset'   => 24,
			'z_index'       => 999999,
			'contact'       => array(
				'type'  => 'whatsapp',
				'label' => 'WhatsApp',
				'value' => '',
			),
		);
	}
}
