<?php
/**
 * FloatConnect settings manager.
 *
 * @package FloatConnect
 */

declare( strict_types=1 );

namespace FloriPlugins\FloatConnect\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Handles FloatConnect settings operations.
 */
final class Manager {

	/**
	 * Settings repository.
	 *
	 * @var Repository
	 */
	private Repository $repository;

	/**
	 * Settings sanitizer.
	 *
	 * @var Sanitizer
	 */
	private Sanitizer $sanitizer;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->repository = new Repository();
		$this->sanitizer  = new Sanitizer();
	}

	/**
	 * Get all settings.
	 *
	 * @return array<string, mixed>
	 */
	public function get_settings(): array {
		return $this->repository->get_all();
	}

	/**
	 * Get one setting.
	 *
	 * @param string $key Setting key.
	 * @param mixed  $default_value Default value.
	 *
	 * @return mixed
	 */
	public function get(
		string $key,
		mixed $default_value = null
	): mixed {
		$settings = $this->get_settings();

		return $settings[ $key ] ?? $default_value;
	}

	/**
	 * Save settings.
	 *
	 * @param array<string, mixed> $settings Settings to save.
	 *
	 * @return bool
	 */
	public function save( array $settings ): bool {
		$sanitized = $this->sanitizer->sanitize( $settings );

		$errors = get_settings_errors(
			'floatconnect'
		);

		if ( ! empty( $errors ) ) {
			return false;
		}

		return $this->repository->save( $sanitized );
	}

	/**
	 * Reset settings to defaults.
	 *
	 * @return bool
	 */
	public function reset(): bool {
		$defaults = Defaults::all();

		/*
		 * Keep the default contact structurally valid.
		 * The value is intentionally empty and therefore the frontend
		 * will simply not display the contact until a value is entered.
		 */
		return $this->repository->save( $defaults );
	}
}
