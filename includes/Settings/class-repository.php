<?php
/**
 * FloatConnect settings repository.
 *
 * @package FloatConnect
 */

declare( strict_types=1 );

namespace FloriPlugins\FloatConnect\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Handles persistent FloatConnect plugin settings.
 */
final class Repository {

	/**
	 * WordPress option name.
	 *
	 * @var string
	 */
	private const OPTION_NAME = 'floatconnect_settings';

	/**
	 * Get all settings.
	 *
	 * @return array<string, mixed>
	 */
	public function get_all(): array {
		$saved = get_option(
			self::OPTION_NAME,
			array()
		);

		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		$defaults = Defaults::all();

		/*
		 * Merge saved settings with defaults.
		 */
		$settings = array_replace_recursive(
			$defaults,
			$saved
		);

		/*
		 * Contacts are user-managed.
		 *
		 * If contacts have been explicitly saved, use them exactly as
		 * stored. Do not merge them with default contacts because removed
		 * contacts must stay removed.
		 */
		if ( array_key_exists( 'contacts', $saved ) ) {
			$settings['contacts'] = is_array( $saved['contacts'] )
				? $saved['contacts']
				: array();
		}

		return $settings;
	}

	/**
	 * Save settings.
	 *
	 * @param array<string, mixed> $settings Settings to save.
	 *
	 * @return bool
	 */
	public function save( array $settings ): bool {
		$result = update_option(
			self::OPTION_NAME,
			$settings,
			false
		);

		/*
		 * update_option() returns false when the value is already
		 * identical to the stored value. That is not a save failure.
		 */
		if ( false === $result ) {
			$current = get_option(
				self::OPTION_NAME,
				array()
			);

			return is_array( $current ) && $current === $settings;
		}

		return true;
	}

	/**
	 * Delete all FloatConnect settings.
	 *
	 * @return bool
	 */
	public function delete(): bool {
		return delete_option( self::OPTION_NAME );
	}
}
