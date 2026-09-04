<?php
/**
 * FloatConnect settings sanitizer.
 *
 * @package FloatConnect
 */

declare (strict_types = 1);
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
		$settings['position'] = ( isset( $data['position'] ) ? sanitize_key( (string) $data['position'] ) : 'bottom-right' );
		if ( ! in_array( $settings['position'], array( 'bottom-right', 'bottom-left' ), true ) ) {
			$settings['position'] = 'bottom-right';
		}

		/*
		 * Basic appearance.
		 */
		$settings['button_size']   = $this->sanitize_range(
			$data['button_size'] ?? 60,
			40,
			120,
			60
		);
		$settings['icon_size']     = $this->sanitize_range(
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
		$settings['side_offset']   = $this->sanitize_range(
			$data['side_offset'] ?? 24,
			0,
			200,
			24
		);
		$settings['z_index']       = $this->sanitize_range(
			$data['z_index'] ?? 999999,
			1,
			9999999,
			999999
		);

		/*
		 * Shadow.
		 */
		$settings['shadow'] = ( isset( $data['shadow'] ) ? sanitize_key( (string) $data['shadow'] ) : 'soft' );
		if ( ! in_array( $settings['shadow'], array( 'none', 'soft', 'strong' ), true ) ) {
			$settings['shadow'] = 'soft';
		}

		/*
		 * Single button animation.
		 */
		$settings['animation'] = ( isset( $data['animation'] ) ? sanitize_key( (string) $data['animation'] ) : 'none' );
		if ( ! in_array( $settings['animation'], array( 'none', 'pulse', 'bounce' ), true ) ) {
			$settings['animation'] = 'none';
		}
		$settings['hover_effect'] = ! empty( $data['hover_effect'] );

		/*
		 * Free version:
		 * One contact only.
		 */
		$settings['contacts'] = array( $this->sanitize_free_contact( $data['contacts'][0] ?? array() ) );

		/*
		 * Free version:
		 * The single contact is implicitly Primary.
		 */
		return $settings;
	}

	/**
	 * Sanitize the single Free contact.
	 *
	 * @param mixed $contact Raw contact.
	 *
	 * @return array<string, mixed>
	 */
	private function sanitize_free_contact( mixed $contact ): array {
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
		$type          = ( isset( $contact['type'] ) ? sanitize_key( (string) $contact['type'] ) : 'whatsapp' );
		if ( ! in_array( $type, $allowed_types, true ) ) {
			$type = 'whatsapp';
		}
		$label = ( isset( $contact['label'] ) ? sanitize_text_field( (string) $contact['label'] ) : '' );
		$value = ( isset( $contact['value'] ) ? trim( (string) $contact['value'] ) : '' );

		/*
		 * Contact value validation.
		 */
		if ( '' === $value ) {
			add_settings_error( 'floatconnect', 'contact_required', esc_html__( 'Contact value is required.', 'floatconnect' ) );
		} elseif ( 'email' === $type ) {
			$value = sanitize_email( $value );
			if ( '' === $value || ! is_email( $value ) ) {
				add_settings_error( 'floatconnect', 'invalid_email', esc_html__( 'Please enter a valid email address.', 'floatconnect' ) );
			}
		} elseif ( 'whatsapp' === $type ) {
			$value = sanitize_text_field( $value );
			if ( ! preg_match( '/^\\+[1-9][0-9]{6,14}$/', $value ) ) {
				add_settings_error( 'floatconnect', 'invalid_whatsapp', esc_html__( 'Please enter a valid international phone number for WhatsApp, for example +40722111222.', 'floatconnect' ) );
			}
		} elseif ( 'phone' === $type ) {
			$value = sanitize_text_field( $value );
			if ( ! preg_match( '/^\\+?[0-9]{7,15}$/', $value ) ) {
				add_settings_error( 'floatconnect', 'invalid_phone', esc_html__( 'Please enter a valid phone number.', 'floatconnect' ) );
			}
		} elseif ( 'custom' === $type ) {
			$value = esc_url_raw( $value );
			if ( '' === $value ) {
				add_settings_error( 'floatconnect', 'invalid_custom', esc_html__( 'Please enter a valid URL.', 'floatconnect' ) );
			}
		} else {
			$value = sanitize_text_field( $value );
		}
		return array(
			'type'    => $type,
			'label'   => $label,
			'value'   => $value,
			'enabled' => true,
		);
	}

	/**
	 * Sanitize color.
	 *
	 * @param mixed  $value    Raw color.
	 * @param string $fallback Fallback color.
	 *
	 * @return string
	 */
	private function sanitize_color( mixed $value, string $fallback ): string {
		$color = sanitize_hex_color( (string) $value );
		return ( null !== $color ? $color : $fallback );
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

	/**
	 * Sanitize Premium contacts.
	 *
	 * @param mixed  $contacts Raw contacts.
	 * @param int    $primary_contact Primary contact index.
	 * @param string $display_mode Current display mode.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function sanitize_contacts( mixed $contacts, int $primary_contact, string $display_mode ): array {
		if ( ! is_array( $contacts ) ) {
			return array();
		}
		$allowed_types = array(
			'whatsapp',
			'phone',
			'email',
			'messenger',
			'telegram',
			'custom',
		);
		$allowed_days  = array(
			'monday',
			'tuesday',
			'wednesday',
			'thursday',
			'friday',
			'saturday',
			'sunday',
		);
		$sanitized     = array();
		foreach ( $contacts as $index => $contact ) {
			if ( ! is_array( $contact ) ) {
				continue;
			}
			$type = ( isset( $contact['type'] ) ? sanitize_key( (string) $contact['type'] ) : '' );
			if ( ! in_array( $type, $allowed_types, true ) ) {
				continue;
			}
			$label   = ( isset( $contact['label'] ) ? sanitize_text_field( (string) $contact['label'] ) : '' );
			$value   = ( isset( $contact['value'] ) ? trim( (string) $contact['value'] ) : '' );
			$enabled = ! empty( $contact['enabled'] );

			/*
			 * Determine whether this contact must be validated.
			 */
			$must_validate = false;
			if ( 'single' === $display_mode ) {
				$must_validate = (int) $index === $primary_contact;
			} elseif ( 'bar' === $display_mode ) {
				$must_validate = $enabled;
			}

			/*
			 * Contact value validation.
			 */
			if ( $must_validate ) {
				if ( '' === $value ) {
					add_settings_error( 'floatconnect', 'contact_required_' . $index, esc_html__( 'Contact value is required for enabled contacts.', 'floatconnect' ) );
					continue;
				}
				if ( 'email' === $type ) {
					$value = sanitize_email( $value );
					if ( '' === $value || ! is_email( $value ) ) {
						add_settings_error( 'floatconnect', 'invalid_email_' . $index, esc_html__( 'Please enter a valid email address.', 'floatconnect' ) );
						continue;
					}
				} elseif ( 'whatsapp' === $type ) {
					$value = sanitize_text_field( $value );
					if ( ! preg_match( '/^\\+[1-9][0-9]{6,14}$/', $value ) ) {
						add_settings_error( 'floatconnect', 'invalid_whatsapp_' . $index, esc_html__( 'Please enter a valid international phone number for WhatsApp, for example +40722111222.', 'floatconnect' ) );
						continue;
					}
				} elseif ( 'phone' === $type ) {
					$value = sanitize_text_field( $value );
					if ( ! preg_match( '/^\\+?[0-9]{7,15}$/', $value ) ) {
						add_settings_error( 'floatconnect', 'invalid_phone_' . $index, esc_html__( 'Please enter a valid phone number.', 'floatconnect' ) );
						continue;
					}
				} elseif ( 'custom' === $type ) {
					$value = esc_url_raw( $value );
					if ( '' === $value ) {
						add_settings_error( 'floatconnect', 'invalid_custom_' . $index, esc_html__( 'Please enter a valid URL.', 'floatconnect' ) );
						continue;
					}
				} else {
					$value = sanitize_text_field( $value );
				}
			} elseif ( 'email' === $type ) {
				$value = sanitize_email( $value );
			} elseif ( 'custom' === $type ) {
				$value = esc_url_raw( $value );
			} else {
				$value = sanitize_text_field( $value );
			}

			/*
			 * Schedule.
			 */
			$schedule_enabled = ! empty( $contact['schedule_enabled'] );
			$schedule_days    = array();
			if ( isset( $contact['schedule_days'] ) && is_array( $contact['schedule_days'] ) ) {
				foreach ( $contact['schedule_days'] as $day ) {
					$day = sanitize_key( (string) $day );
					if ( in_array( $day, $allowed_days, true ) ) {
						$schedule_days[] = $day;
					}
				}
			}
			$schedule_days = array_values( array_unique( $schedule_days ) );
			$schedule_from = ( isset( $contact['schedule_from'] ) ? sanitize_text_field( (string) $contact['schedule_from'] ) : '00:00' );
			$schedule_to   = ( isset( $contact['schedule_to'] ) ? sanitize_text_field( (string) $contact['schedule_to'] ) : '23:59' );
			if ( ! preg_match( '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $schedule_from ) ) {
				$schedule_from = '00:00';
			}
			if ( ! preg_match( '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $schedule_to ) ) {
				$schedule_to = '23:59';
			}
			if ( $schedule_enabled && empty( $schedule_days ) ) {
				add_settings_error( 'floatconnect', 'schedule_days_required_' . $index, esc_html__( 'Please select at least one day when the schedule is enabled.', 'floatconnect' ) );
				continue;
			}

			/*
			 * Premium contact settings.
			 */
			$sanitized[ $index ] = array(
				'type'             => $type,
				'label'            => $label,
				'value'            => $value,
				'background_color' => $this->sanitize_color( $contact['background_color'] ?? '#25D366', '#25D366' ),
				'icon_color'       => $this->sanitize_color( $contact['icon_color'] ?? '#FFFFFF', '#FFFFFF' ),
				'hover_background' => $this->sanitize_color( $contact['hover_background'] ?? '#20BD5A', '#20BD5A' ),
				'hover_icon_color' => $this->sanitize_color( $contact['hover_icon_color'] ?? '#FFFFFF', '#FFFFFF' ),
				'enabled'          => $enabled,
				'mobile'           => ! empty( $contact['mobile'] ),
				'desktop'          => ! empty( $contact['desktop'] ),
				'schedule_enabled' => $schedule_enabled,
				'schedule_days'    => $schedule_days,
				'schedule_from'    => $schedule_from,
				'schedule_to'      => $schedule_to,
			);
		}
		return $sanitized;
	}
}
