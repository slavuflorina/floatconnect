<?php
/**
 * Dashboard page.
 *
 * @package FloatConnect
 */

declare( strict_types=1 );

namespace FloriPlugins\FloatConnect\Admin\Pages;

use FloriPlugins\FloatConnect\Settings\Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the FloatConnect settings page.
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
		if ( isset( $_POST['floatconnect_reset'] ) ) {
			check_admin_referer(
				'floatconnect_reset',
				'floatconnect_reset_nonce'
			);

			$reset = $this->manager->reset();

			if ( $reset ) {
				add_settings_error(
					'floatconnect',
					'floatconnect_reset',
					__( 'Settings reset to defaults.', 'floatconnect' ),
					'updated'
				);
			} else {
				add_settings_error(
					'floatconnect',
					'floatconnect_reset_failed',
					__( 'Settings could not be reset.', 'floatconnect' ),
					'error'
				);
			}

			return;
		}

		/*
		 * Save settings.
		 */
		if ( ! isset( $_POST['floatconnect_save'] ) ) {
			return;
		}

		check_admin_referer(
			'floatconnect_save',
			'floatconnect_nonce'
		);

		$data = array();

		if (
			isset( $_POST['floatconnect'] )
			&& is_array( $_POST['floatconnect'] )
		) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized by Settings\Sanitizer.
			$data = wp_unslash( $_POST['floatconnect'] );
		}

		$saved = $this->manager->save( $data );

		if ( $saved ) {
			add_settings_error(
				'floatconnect',
				'floatconnect_saved',
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

		$contacts = (
			isset( $settings['contacts'] )
			&& is_array( $settings['contacts'] )
		)
			? $settings['contacts']
			: array();

		?>
		<div class="wrap fc-wrap">

			<h1>
				<?php esc_html_e( 'FloatConnect', 'floatconnect' ); ?>
			</h1>

			<?php settings_errors( 'floatconnect' ); ?>

			<form method="post">

				<?php wp_nonce_field( 'floatconnect_save', 'floatconnect_nonce' ); ?>

				<div class="fc-card">

					<h2>
						<?php esc_html_e( 'General', 'floatconnect' ); ?>
					</h2>

					<div class="fc-field">

						<label>
							<input
								type="checkbox"
								name="floatconnect[enabled]"
								value="1"
								<?php checked( ! empty( $settings['enabled'] ) ); ?>
							>

							<?php esc_html_e( 'Enable contact button', 'floatconnect' ); ?>
						</label>

					</div>

					<div class="fc-field">

						<label
							class="fc-label"
							for="fc-position"
						>
							<?php esc_html_e( 'Position', 'floatconnect' ); ?>
						</label>

						<select
							id="fc-position"
							class="fc-input"
							name="floatconnect[position]"
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

				<div class="fc-card">

					<h2>
						<?php esc_html_e( 'Appearance', 'floatconnect' ); ?>
					</h2>

					<h3>
						<?php esc_html_e( 'Button', 'floatconnect' ); ?>
					</h3>

					<div class="fc-field">

						<label
							class="fc-label"
							for="fc-button-size"
						>
							<?php esc_html_e( 'Button size (px)', 'floatconnect' ); ?>
						</label>

						<input
							id="fc-button-size"
							class="fc-input"
							type="number"
							min="40"
							max="120"
							step="1"
							name="floatconnect[button_size]"
							value="<?php echo esc_attr( $settings['button_size'] ?? 60 ); ?>"
						>

					</div>

					<div class="fc-field">

						<label
							class="fc-label"
							for="fc-icon-size"
						>
							<?php esc_html_e( 'Icon size (px)', 'floatconnect' ); ?>
						</label>

						<input
							id="fc-icon-size"
							class="fc-input"
							type="number"
							min="12"
							max="80"
							step="1"
							name="floatconnect[icon_size]"
							value="<?php echo esc_attr( $settings['icon_size'] ?? 32 ); ?>"
						>

					</div>

					<div class="fc-field">

						<label
							class="fc-label"
							for="fc-border-radius"
						>
							<?php esc_html_e( 'Button radius (%)', 'floatconnect' ); ?>
						</label>

						<input
							id="fc-border-radius"
							class="fc-input"
							type="number"
							min="0"
							max="50"
							step="1"
							name="floatconnect[border_radius]"
							value="<?php echo esc_attr( $settings['border_radius'] ?? 50 ); ?>"
						>

						<p class="description">
							<?php esc_html_e( '50% creates a circle. Lower values create a more rounded square.', 'floatconnect' ); ?>
						</p>

					</div>

					<h3>
						<?php esc_html_e( 'Effects', 'floatconnect' ); ?>
					</h3>

					<div class="fc-field">

						<label
							class="fc-label"
							for="fc-shadow"
						>
							<?php esc_html_e( 'Shadow', 'floatconnect' ); ?>
						</label>

						<select
							id="fc-shadow"
							class="fc-input"
							name="floatconnect[shadow]"
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

					<div class="fc-field">

						<label
							class="fc-label"
							for="fc-animation"
						>
							<?php esc_html_e( 'Single button animation', 'floatconnect' ); ?>
						</label>

						<select
							id="fc-animation"
							class="fc-input"
							name="floatconnect[animation]"
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

					<div class="fc-field">

						<label>
							<input
								type="checkbox"
								name="floatconnect[hover_effect]"
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
						<div class="fc-field">

							<label
								class="fc-label"
								for="fc-<?php echo esc_attr( $key ); ?>"
							>
								<?php echo esc_html( $field['label'] ); ?>
							</label>

							<input
								id="fc-<?php echo esc_attr( $key ); ?>"
								class="fc-input"
								type="number"
								min="<?php echo esc_attr( (string) $field['min'] ); ?>"
								max="<?php echo esc_attr( (string) $field['max'] ); ?>"
								step="1"
								name="floatconnect[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( $settings[ $key ] ?? '' ); ?>"
							>

						</div>
						<?php
					}
					?>

				</div>

				<div class="fc-card">

					<h2>
						<?php esc_html_e( 'Contacts', 'floatconnect' ); ?>
					</h2>

					<?php
					/*
					 * Free: one basic contact only.
					 */
					$this->render_contacts( $contacts );
					?>

				</div>

				<div class="fc-actions">

					<button
						type="submit"
						class="button button-primary"
						name="floatconnect_save"
						value="1"
					>
						<?php esc_html_e( 'Save Settings', 'floatconnect' ); ?>
					</button>

					<button
						type="submit"
						class="button"
						name="floatconnect_reset"
						value="1"
						onclick="return window.confirm('<?php echo esc_js( __( 'Are you sure you want to reset all settings to their defaults?', 'floatconnect' ) ); ?>');"
					>
						<?php esc_html_e( 'Reset to Defaults', 'floatconnect' ); ?>
					</button>

				</div>

				<?php wp_nonce_field( 'floatconnect_reset', 'floatconnect_reset_nonce' ); ?>

			</form>

		</div>
		<?php
	}

	/**
	 * Render the Free contact configuration.
	 *
	 * @param array<int|string, mixed> $contacts Contacts.
	 *
	 * @return void
	 */
	private function render_contacts( array $contacts ): void {
		$this->render_free_contact( $contacts );
	}

	/**
	 * Render the Free contact configuration.
	 *
	 * @param array<int|string, mixed> $contacts Contacts.
	 *
	 * @return void
	 */
	private function render_free_contact( array $contacts ): void {
		$contact = (
			isset( $contacts[0] )
			&& is_array( $contacts[0] )
		)
			? $contacts[0]
			: array();

		$type = (
			isset( $contact['type'] )
			? (string) $contact['type']
			: 'whatsapp'
		);

		$label = (
			isset( $contact['label'] )
			? (string) $contact['label']
			: ''
		);

		$value = (
			isset( $contact['value'] )
			? (string) $contact['value']
			: ''
		);

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

		<div class="fc-contact-row">

			<div class="fc-contact-field">

				<label
					class="fc-label"
					for="fc-free-contact-type"
				>
					<?php esc_html_e( 'Contact type', 'floatconnect' ); ?>
				</label>

				<select
					id="fc-free-contact-type"
					class="fc-input"
					name="floatconnect[contacts][0][type]"
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

			<div class="fc-contact-field">

				<label
					class="fc-label"
					for="fc-free-contact-label"
				>
					<?php esc_html_e( 'Label', 'floatconnect' ); ?>
				</label>

				<input
					id="fc-free-contact-label"
					class="fc-input"
					type="text"
					name="floatconnect[contacts][0][label]"
					value="<?php echo esc_attr( $label ); ?>"
				>

			</div>

			<div class="fc-contact-field">

				<label
					class="fc-label"
					for="fc-free-contact-value"
				>
					<?php esc_html_e( 'Value', 'floatconnect' ); ?>
				</label>

				<input
					id="fc-free-contact-value"
					class="fc-input"
					type="text"
					name="floatconnect[contacts][0][value]"
					value="<?php echo esc_attr( $value ); ?>"
				>

			</div>

		</div>
		<?php
	}
}
