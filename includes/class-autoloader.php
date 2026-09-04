<?php
/**
 * Plugin class autoloader.
 *
 * @package FloatConnect
 */

declare( strict_types=1 );

namespace FloriPlugins\FloatConnect;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the FloatConnect class autoloader.
 */
final class Autoloader {

	/**
	 * Register the autoloader.
	 *
	 * @return void
	 */
	public static function register(): void {
		spl_autoload_register(
			static function ( string $class_name ): void {
				$prefix = 'FloriPlugins\\FloatConnect\\';

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
					preg_replace(
						'/(?<!^)[A-Z]/',
						'-$0',
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
