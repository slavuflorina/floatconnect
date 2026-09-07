<?php
/**
 * FloatConnect settings sanitizer.
 *
 * @package FloatConnect
 */

declare( strict_types=1 );

namespace FloriPlugins\FloatConnect\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Sanitizes FloatConnect plugin settings.
 */
final class Sanitizer {

	/**
	 * Sanitize settings.
	 *
	 * @param array<string, mixed> $data Raw settings data.
	 *
	 * @return array<string, mixed>
	 */
	public function sanitize( array $data ): array {
		$settings = Defaults::all();

		/*
		 * General.
		 */
		$settings['enabled']  = ! empty( $data['enabled'] );
		$settings['position'] = isset( $data['position'] )
			? sanitize_key( (string) $data['position'] )
			: 'bottom-right';

		if ( ! in_array( $settings['position'], array( 'bottom-right', 'bottom-left' ), true ) ) {
			$settings['position'] = 'bottom-right';
		}

		/*
		 * Basic appearance.
		 */
		$settings['button_size'] = $this->sanitize_range(
			$data['button_size'] ?? 60,
			40,
			120,
			60
		);

		$settings['icon_size'] = $this->sanitize_range(
			$data['icon_size'] ?? 32,
			12,
			80,
			32
		);

		$settings['border_radius'] = $this->sanitize_range(
			$data['border_radius'] ?? 50,
			0,
			50,
			50
		);

		$settings['bottom_offset'] = $this->sanitize_range(
			$data['bottom_offset'] ?? 24,
			0,
			200,
			24
		);

		$settings['side_offset'] = $this->sanitize_range(
			$data['side_offset'] ?? 24,
			0,
			200,
			24
		);

		$settings['z_index'] = $this->sanitize_range(
			$data['z_index'] ?? 999999,
			1,
			9999999,
			999999
		);

		/*
		 * Shadow.
		 */
		$settings['shadow'] = isset( $data['shadow'] )
			? sanitize_key( (string) $data['shadow'] )
			: 'soft';

		if ( ! in_array( $settings['shadow'], array( 'none', 'soft', 'strong' ), true ) ) {
			$settings['shadow'] = 'soft';
		}

		/*
		 * Single button animation.
		 */
		$settings['animation'] = isset( $data['animation'] )
			? sanitize_key( (string) $data['animation'] )
			: 'none';

		if ( ! in_array( $settings['animation'], array( 'none', 'pulse', 'bounce' ), true ) ) {
			$settings['animation'] = 'none';
		}

		$settings['hover_effect'] = ! empty( $data['hover_effect'] );

		/*
		 * Single Free contact.
		 */
		$settings['contact'] = $this->sanitize_contact(
			$data['contact'] ?? array()
		);

		return $settings;
	}

	/**
	 * Sanitize the single Free contact.
	 *
	 * @param mixed $contact Raw contact.
	 *
	 * @return array<string, mixed>
	 */
	private function sanitize_contact( mixed $contact ): array {
		if ( ! is_array( $contact ) ) {
			$contact = array();
		}

		$allowed_types = array(
			'whatsapp',
			'phone',
			'email',
			'messenger',
			'telegram',
			'custom',
		);

		$type = isset( $contact['type'] )
			? sanitize_key( (string) $contact['type'] )
			: 'whatsapp';

		if ( ! in_array( $type, $allowed_types, true ) ) {
			$type = 'whatsapp';
		}

		$label = isset( $contact['label'] )
			? sanitize_text_field( (string) $contact['label'] )
			: '';

		$value = isset( $contact['value'] )
			? trim( (string) $contact['value'] )
			: '';

		/*
		 * Contact value validation.
		 */
		if ( '' === $value ) {
			add_settings_error(
				'floatconnect',
				'contact_required',
				esc_html__(
					'Contact value is required.',
					'floatconnect'
				)
			);
		} elseif ( 'email' === $type ) {
			$value = sanitize_email( $value );

			if ( '' === $value || ! is_email( $value ) ) {
				add_settings_error(
					'floatconnect',
					'invalid_email',
					esc_html__(
						'Please enter a valid email address.',
						'floatconnect'
					)
				);
			}
		} elseif ( 'whatsapp' === $type ) {
			$value = sanitize_text_field( $value );

			if ( ! preg_match( '/^\+[1-9][0-9]{6,14}$/', $value ) ) {
				add_settings_error(
					'floatconnect',
					'invalid_whatsapp',
					esc_html__(
						'Please enter a valid international phone number for WhatsApp, for example +40722111222.',
						'floatconnect'
					)
				);
			}
		} elseif ( 'phone' === $type ) {
			$value = sanitize_text_field( $value );

			if ( ! preg_match( '/^\+?[0-9]{7,15}$/', $value ) ) {
				add_settings_error(
					'floatconnect',
					'invalid_phone',
					esc_html__(
						'Please enter a valid phone number.',
						'floatconnect'
					)
				);
			}
		} elseif ( 'custom' === $type ) {
			$value = esc_url_raw( $value );

			if ( '' === $value ) {
				add_settings_error(
					'floatconnect',
					'invalid_custom',
					esc_html__(
						'Please enter a valid URL.',
						'floatconnect'
					)
				);
			}
		} else {
			$value = sanitize_text_field( $value );
		}

		return array(
			'type'  => $type,
			'label' => $label,
			'value' => $value,
		);
	}

	/**
	 * Sanitize numeric range.
	 *
	 * @param mixed $value    Raw value.
	 * @param int   $minimum  Minimum value.
	 * @param int   $maximum  Maximum value.
	 * @param int   $fallback Fallback value.
	 *
	 * @return int
	 */
	private function sanitize_range(
		mixed $value,
		int $minimum,
		int $maximum,
		int $fallback
	): int {
		$value = absint( $value );

		if ( $value < $minimum || $value > $maximum ) {
			return $fallback;
		}

		return $value;
	}
}
