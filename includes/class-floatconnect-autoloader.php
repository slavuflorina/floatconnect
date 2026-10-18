<?php
/**
 * Plugin class autoloader.
 *
 * @package Floatconnect
 */

declare( strict_types=1 );

namespace FloriPlugins\Floatconnect;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Floatconnect class autoloader.
 */
final class FLOATCONNECT_Autoloader {

	/**
	 * Register the autoloader.
	 *
	 * @return void
	 */
	public static function register(): void {
		spl_autoload_register(
			static function ( string $class_name ): void {
				$prefix = 'FloriPlugins\\Floatconnect\\';

				if (
					0 !== strncmp(
						$class_name,
						$prefix,
						strlen( $prefix )
					)
				) {
					return;
				}

				$relative = substr(
					$class_name,
					strlen( $prefix )
				);

				$parts = explode( '\\', $relative );

				$class_name = array_pop( $parts );

				$file_name = 'class-' . strtolower(
					str_replace(
						'_',
						'-',
						$class_name
					)
				) . '.php';

				$path = FLOATCONNECT_PATH . 'includes/';

				if ( ! empty( $parts ) ) {
					$path .= implode(
						DIRECTORY_SEPARATOR,
						$parts
					) . DIRECTORY_SEPARATOR;
				}

				$file = $path . $file_name;

				if ( is_file( $file ) ) {
					require_once $file;
				}
			}
		);
	}
}
