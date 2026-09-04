<?php
/**
 * Frontend functionality.
 *
 * @package FloatConnect
 */

declare (strict_types = 1);
namespace FloriPlugins\FloatConnect\Frontend;

use FloriPlugins\FloatConnect\Settings\Manager;
defined( 'ABSPATH' ) || exit;

/**
 * Handles frontend output.
 */
final class Frontend {
	/**
	 * Settings manager.
	 *
	 * @var Manager
	 */
	private Manager $manager;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->manager = new Manager();
	}

	/**
	 * Register frontend hooks.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'wp_footer', array( $this, 'render' ) );
	}

	/**
	 * Render frontend output.
	 *
	 * The common settings are loaded here and the output is
	 * separated between Free and Premium.
	 *
	 * @return void
	 */
	public function render(): void {
		$settings = $this->manager->get_settings();
		if ( empty( $settings['enabled'] ) ) {
			return;
		}
		$contacts = ( is_array( $settings['contacts'] ?? null ) ? $settings['contacts'] : array() );
		if ( empty( $contacts ) ) {
			return;
		}

		/*
		 * Free rendering.
		 */
		$this->render_free( $settings, $contacts );
	}

	/**
	 * Render Free frontend.
	 *
	 * Free supports exactly one contact and one button.
	 *
	 * @param array<string, mixed> $settings Plugin settings.
	 * @param array<int, mixed>    $contacts Contact list.
	 *
	 * @return void
	 */
	private function render_free( array $settings, array $contacts ): void {
		$contact = $contacts[0] ?? array();
		if ( ! is_array( $contact ) ) {
			return;
		}
		$value = ( isset( $contact['value'] ) ? trim( (string) $contact['value'] ) : '' );
		if ( '' === $value ) {
			return;
		}
		$position      = (string) ( $settings['position'] ?? 'bottom-right' );
		$button_size   = absint( $settings['button_size'] ?? 60 );
		$icon_size     = absint( $settings['icon_size'] ?? 32 );
		$border_radius = absint( $settings['border_radius'] ?? 50 );
		$shadow        = (string) ( $settings['shadow'] ?? 'soft' );
		$animation     = (string) ( $settings['animation'] ?? 'none' );
		$hover_effect  = ! empty( $settings['hover_effect'] );
		$bottom_offset = absint( $settings['bottom_offset'] ?? 24 );
		$side_offset   = absint( $settings['side_offset'] ?? 24 );
		$z_index       = absint( $settings['z_index'] ?? 999999 );
		$classes       = array( 'fc', 'fc--single', 'fc--' . sanitize_html_class( $position ) );
		if ( 'none' !== $animation ) {
			$classes[] = 'fc--animation-' . sanitize_html_class( $animation );
		}
		if ( ! $hover_effect ) {
			$classes[] = 'fc--no-hover';
		}
		$classes[] = 'fc--shadow-' . sanitize_html_class( $shadow );
		$style     = sprintf(
			'--fc-size:%dpx;--fc-icon-size:%dpx;--fc-bottom:%dpx;--fc-side:%dpx;--fc-radius:%d%%;--fc-z:%d;',
			$button_size,
			$icon_size,
			$bottom_offset,
			$side_offset,
			$border_radius,
			$z_index
		);
		?>

		<div
			class="
			<?php
			echo esc_attr( implode( ' ', $classes ) );
			?>
		"
			style="
			<?php
			echo esc_attr( $style );
			?>
		"
		>

			<?php
			$this->render_free_contact( $contact );
			?>

		</div>

		<?php
	}

	/**
	 * Render the Free contact.
	 *
	 * Free has exactly one contact and does not use
	 * Premium contact-specific settings.
	 *
	 * @param array<string, mixed> $contact Contact data.
	 *
	 * @return void
	 */
	private function render_free_contact( array $contact ): void {
		$type  = ( isset( $contact['type'] ) ? (string) $contact['type'] : '' );
		$label = ( isset( $contact['label'] ) ? (string) $contact['label'] : '' );
		$value = ( isset( $contact['value'] ) ? trim( (string) $contact['value'] ) : '' );
		if ( '' === $value ) {
			return;
		}
		$href = $this->get_contact_url( $type, $value );
		if ( '' === $href ) {
			return;
		}
		$aria_label    = ( '' !== $label ? $label : ucfirst( $type ) );
		$icon_file     = '';
		$allowed_icons = array(
			'whatsapp',
			'phone',
			'email',
			'messenger',
			'telegram',
			'custom',
		);
		if ( in_array( $type, $allowed_icons, true ) ) {
			$icon_file = FLOATCONNECT_PATH . 'includes/Frontend/Icons/' . sanitize_file_name( $type ) . '.php';
		}
		?>

		<a
			class="fc__contact fc__contact--single"
			href="
			<?php
			echo esc_url( $href );
			?>
		"
			aria-label="
			<?php
			echo esc_attr( $aria_label );
			?>
		"
			<?php
			if ( 'email' !== $type && 'phone' !== $type ) {
				?>
				target="_blank"
				rel="noopener noreferrer"
				<?php
			}
			?>
		>

			<span
				class="fc__icon"
				aria-hidden="true"
			>
				<?php
				if ( '' !== $icon_file && file_exists( $icon_file ) ) {
					require $icon_file;
				}
				?>
			</span>

		</a>

		<?php
	}

	/**
	 * Build contact URL.
	 *
	 * @param string $type Contact type.
	 * @param string $value Contact value.
	 *
	 * @return string
	 */
	private function get_contact_url( string $type, string $value ): string {
		switch ( $type ) {
			case 'email':
				return 'mailto:' . sanitize_email( $value );
			case 'phone':
				return 'tel:' . preg_replace( '/[^0-9+]/', '', $value );
			case 'whatsapp':
				return 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $value );
			case 'messenger':
				if ( 0 === strpos( $value, 'http://' ) || 0 === strpos( $value, 'https://' ) ) {
					return esc_url( $value );
				}
				return 'https://m.me/' . rawurlencode( $value );
			case 'telegram':
				if ( 0 === strpos( $value, 'http://' ) || 0 === strpos( $value, 'https://' ) ) {
					return esc_url( $value );
				}
				$value = ltrim( $value, '@' );
				return 'https://t.me/' . rawurlencode( $value );
			case 'custom':
				$url = esc_url_raw( $value );
				if ( '' === $url || ! in_array( wp_parse_url( $url, PHP_URL_SCHEME ), array( 'http', 'https' ), true ) ) {
					return '';
				}
				return $url;
			default:
				return '';
		}
	}
}
