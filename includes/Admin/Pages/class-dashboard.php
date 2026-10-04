<?php
/**
 * Dashboard page.
 *
 * @package Fcon
 */

declare( strict_types=1 );

namespace FloriPlugins\Fcon\Admin\Pages;

use FloriPlugins\Fcon\Settings\Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the Fcon settings page.
 */
final class Dashboard {

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
	 * Register page hooks.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'admin_init', array( $this, 'handle_save' ) );
	}

	/**
	 * Handle settings save/reset.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		/*
		 * Reset settings.
		 */
		if ( isset( $_POST['fcon_reset'] ) ) {
			check_admin_referer(
				'fcon_reset',
				'fcon_reset_nonce'
			);

			$reset = $this->manager->reset();

			if ( $reset ) {
				add_settings_error(
					'fcon',
					'fcon_reset',
					__( 'Settings reset to defaults.', 'floatconnect' ),
					'updated'
				);
			} else {
				add_settings_error(
					'fcon',
					'fcon_reset_failed',
					__( 'Settings could not be reset.', 'floatconnect' ),
					'error'
				);
			}

			return;
		}

		/*
		 * Save settings.
		 */
		if ( ! isset( $_POST['fcon_save'] ) ) {
			return;
		}

		check_admin_referer(
			'fcon_save',
			'fcon_nonce'
		);

		$data = array();

		if (
			isset( $_POST['fcon'] )
			&& is_array( $_POST['fcon'] )
		) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized by Settings\Sanitizer.
			$data = wp_unslash( $_POST['fcon'] );
		}

		$saved = $this->manager->save( $data );

		if ( $saved ) {
			add_settings_error(
				'fcon',
				'fcon_saved',
				__( 'Settings saved.', 'floatconnect' ),
				'updated'
			);
		}
	}

	/**
	 * Render dashboard page.
	 *
	 * @return void
	 */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = $this->manager->get_settings();

		$contact = (
			isset( $settings['contact'] )
			&& is_array( $settings['contact'] )
		)
			? $settings['contact']
			: array();

		?>

	<div class="wrap fcon-wrap">

		<h1>
			<?php esc_html_e( 'FloatConnect', 'floatconnect' ); ?>
		</h1>

		<?php settings_errors( 'fcon' ); ?>

		<form method="post">

			<?php wp_nonce_field( 'fcon_save', 'fcon_nonce' ); ?>

			<div class="fcon-card">

				<h2>
					<?php esc_html_e( 'General', 'floatconnect' ); ?>
				</h2>

				<div class="fcon-field">

					<label>
						<input
							type="checkbox"
							name="fcon[enabled]"
							value="1"
							<?php checked( ! empty( $settings['enabled'] ) ); ?>
						>

						<?php esc_html_e( 'Enable contact button', 'floatconnect' ); ?>
					</label>

				</div>

				<div class="fcon-field">

					<label
						class="fcon-label"
						for="fcon-position"
					>
						<?php esc_html_e( 'Position', 'floatconnect' ); ?>
					</label>

					<select
						id="fcon-position"
						class="fcon-input"
						name="fcon[position]"
					>

						<option
							value="bottom-right"
							<?php selected( $settings['position'] ?? 'bottom-right', 'bottom-right' ); ?>
						>
							<?php esc_html_e( 'Bottom right', 'floatconnect' ); ?>
						</option>

						<option
							value="bottom-left"
							<?php selected( $settings['position'] ?? 'bottom-right', 'bottom-left' ); ?>
						>
							<?php esc_html_e( 'Bottom left', 'floatconnect' ); ?>
						</option>

					</select>

				</div>

			</div>

			<div class="fcon-card">

				<h2>
					<?php esc_html_e( 'Appearance', 'floatconnect' ); ?>
				</h2>

				<h3>
					<?php esc_html_e( 'Button', 'floatconnect' ); ?>
				</h3>

				<div class="fcon-field">

					<label
						class="fcon-label"
						for="fcon-button-size"
					>
						<?php esc_html_e( 'Button size (px)', 'floatconnect' ); ?>
					</label>

					<input
						id="fcon-button-size"
						class="fcon-input"
						type="number"
						min="40"
						max="120"
						step="1"
						name="fcon[button_size]"
						value="<?php echo esc_attr( $settings['button_size'] ?? 60 ); ?>"
					>

				</div>

				<div class="fcon-field">

					<label
						class="fcon-label"
						for="fcon-icon-size"
					>
						<?php esc_html_e( 'Icon size (px)', 'floatconnect' ); ?>
					</label>

					<input
						id="fcon-icon-size"
						class="fcon-input"
						type="number"
						min="12"
						max="80"
						step="1"
						name="fcon[icon_size]"
						value="<?php echo esc_attr( $settings['icon_size'] ?? 32 ); ?>"
					>

				</div>

				<div class="fcon-field">

					<label
						class="fcon-label"
						for="fcon-border-radius"
					>
						<?php esc_html_e( 'Button radius (%)', 'floatconnect' ); ?>
					</label>

					<input
						id="fcon-border-radius"
						class="fcon-input"
						type="number"
						min="0"
						max="50"
						step="1"
						name="fcon[border_radius]"
						value="<?php echo esc_attr( $settings['border_radius'] ?? 50 ); ?>"
					>

					<p class="description">
						<?php esc_html_e( '50% creates a circle. Lower values create a more rounded square.', 'floatconnect' ); ?>
					</p>

				</div>

				<h3>
					<?php esc_html_e( 'Effects', 'floatconnect' ); ?>
				</h3>

				<div class="fcon-field">

					<label
						class="fcon-label"
						for="fcon-shadow"
					>
						<?php esc_html_e( 'Shadow', 'floatconnect' ); ?>
					</label>

					<select
						id="fcon-shadow"
						class="fcon-input"
						name="fcon[shadow]"
					>

						<option
							value="none"
							<?php selected( $settings['shadow'] ?? 'soft', 'none' ); ?>
						>
							<?php esc_html_e( 'None', 'floatconnect' ); ?>
						</option>

						<option
							value="soft"
							<?php selected( $settings['shadow'] ?? 'soft', 'soft' ); ?>
						>
							<?php esc_html_e( 'Soft', 'floatconnect' ); ?>
						</option>

						<option
							value="strong"
							<?php selected( $settings['shadow'] ?? 'soft', 'strong' ); ?>
						>
							<?php esc_html_e( 'Strong', 'floatconnect' ); ?>
						</option>

					</select>

				</div>

				<div class="fcon-field">

					<label
						class="fcon-label"
						for="fcon-animation"
					>
						<?php esc_html_e( 'Single button animation', 'floatconnect' ); ?>
					</label>

					<select
						id="fcon-animation"
						class="fcon-input"
						name="fcon[animation]"
					>

						<option
							value="none"
							<?php selected( $settings['animation'] ?? 'none', 'none' ); ?>
						>
							<?php esc_html_e( 'None', 'floatconnect' ); ?>
						</option>

						<option
							value="pulse"
							<?php selected( $settings['animation'] ?? 'none', 'pulse' ); ?>
						>
							<?php esc_html_e( 'Pulse', 'floatconnect' ); ?>
						</option>

						<option
							value="bounce"
							<?php selected( $settings['animation'] ?? 'none', 'bounce' ); ?>
						>
							<?php esc_html_e( 'Bounce', 'floatconnect' ); ?>
						</option>

					</select>

				</div>

				<div class="fcon-field">

					<label>
						<input
							type="checkbox"
							name="fcon[hover_effect]"
							value="1"
							<?php checked( ! empty( $settings['hover_effect'] ) ); ?>
						>

						<?php esc_html_e( 'Enable hover effect', 'floatconnect' ); ?>
					</label>

				</div>

				<h3>
					<?php esc_html_e( 'Position & spacing', 'floatconnect' ); ?>
				</h3>

				<?php
				$spacing_fields = array(
					'bottom_offset' => array(
						'label' => 'Bottom offset (px)',
						'min'   => 0,
						'max'   => 200,
					),
					'side_offset'   => array(
						'label' => 'Side offset (px)',
						'min'   => 0,
						'max'   => 200,
					),
					'z_index'       => array(
						'label' => 'Z-index',
						'min'   => 1,
						'max'   => 9999999,
					),
				);

				foreach ( $spacing_fields as $key => $field ) {
					?>
					<div class="fcon-field">

						<label
							class="fcon-label"
							for="fcon-<?php echo esc_attr( $key ); ?>"
						>
							<?php echo esc_html( $field['label'] ); ?>
						</label>

						<input
							id="fcon-<?php echo esc_attr( $key ); ?>"
							class="fcon-input"
							type="number"
							min="<?php echo esc_attr( (string) $field['min'] ); ?>"
							max="<?php echo esc_attr( (string) $field['max'] ); ?>"
							step="1"
							name="fcon[<?php echo esc_attr( $key ); ?>]"
							value="<?php echo esc_attr( $settings[ $key ] ?? '' ); ?>"
						>

					</div>
					<?php
				}
				?>

			</div>

			<div class="fcon-card">

				<h2>
					<?php esc_html_e( 'Contact', 'floatconnect' ); ?>
				</h2>

				<?php $this->render_contact( $contact ); ?>

			</div>

			<div class="fcon-actions">

				<button
					type="submit"
					class="button button-primary"
					name="fcon_save"
					value="1"
				>
					<?php esc_html_e( 'Save Settings', 'floatconnect' ); ?>
				</button>

				<button
					type="submit"
					class="button"
					name="fcon_reset"
					value="1"
					onclick="return window.confirm('<?php echo esc_js( __( 'Are you sure you want to reset all settings to their defaults?', 'floatconnect' ) ); ?>');"
				>
					<?php esc_html_e( 'Reset to Defaults', 'floatconnect' ); ?>
				</button>

			</div>

			<?php wp_nonce_field( 'fcon_reset', 'fcon_reset_nonce' ); ?>

		</form>

	</div>
		<?php
	}

	/**
	 * Render the single Free contact configuration.
	 *
	 * @param array<string, mixed> $contact Contact.
	 *
	 * @return void
	 */
	private function render_contact( array $contact ): void {
		$type = isset( $contact['type'] )
			? (string) $contact['type']
			: 'whatsapp';

		$label = isset( $contact['label'] )
			? (string) $contact['label']
			: '';

		$value = isset( $contact['value'] )
			? (string) $contact['value']
			: '';

		$types = array(
			'whatsapp'  => 'WhatsApp',
			'phone'     => 'Phone',
			'email'     => 'Email',
			'messenger' => 'Messenger',
			'telegram'  => 'Telegram',
			'custom'    => 'Custom URL',
		);

		?>
	<p class="description">
		<?php esc_html_e( 'Configure the contact that will be used by the Single button.', 'floatconnect' ); ?>
	</p>

	<div class="fcon-contact-row">

		<div class="fcon-contact-field">

			<label
				class="fcon-label"
				for="fcon-contact-type"
			>
				<?php esc_html_e( 'Contact type', 'floatconnect' ); ?>
			</label>

			<select
				id="fcon-contact-type"
				class="fcon-input"
				name="fcon[contact][type]"
			>

				<?php foreach ( $types as $type_key => $type_label ) : ?>
					<option
						value="<?php echo esc_attr( $type_key ); ?>"
						<?php selected( $type, $type_key ); ?>
					>
						<?php echo esc_html( $type_label ); ?>
					</option>
				<?php endforeach; ?>

			</select>

		</div>

		<div class="fcon-contact-field">

			<label
				class="fcon-label"
				for="fcon-contact-label"
			>
				<?php esc_html_e( 'Label', 'floatconnect' ); ?>
			</label>

			<input
				id="fcon-contact-label"
				class="fcon-input"
				type="text"
				name="fcon[contact][label]"
				value="<?php echo esc_attr( $label ); ?>"
			>

		</div>

		<div class="fcon-contact-field">

			<label
				class="fcon-label"
				for="fcon-contact-value"
			>
				<?php esc_html_e( 'Value', 'floatconnect' ); ?>
			</label>

			<input
				id="fcon-contact-value"
				class="fcon-input"
				type="text"
				name="fcon[contact][value]"
				value="<?php echo esc_attr( $value ); ?>"
			>

		</div>

	</div>
		<?php
	}
}
