<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SA_AI_Markdown_Settings {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_settings_menu' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	public function add_settings_menu() {
		add_options_page(
			'AI Markdown Settings',
			'AI Markdown',
			'manage_options',
			'sa-ai-markdown',
			[ $this, 'render_settings_page' ]
		);
	}

	public function register_settings() {
		register_setting( 'sa_ai_markdown_settings', 'sa_ai_markdown_post_types', [ 'sanitize_callback' => [ $this, 'sanitize_post_types' ] ] );
		register_setting( 'sa_ai_markdown_settings', 'sa_ai_markdown_content_signal', [ 'sanitize_callback' => 'sanitize_text_field' ] );

		if ( isset( $_GET['action'] ) && $_GET['action'] === 'regenerate_markdown' && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'sa_ai_markdown_regenerate' ) ) {
			$cron = new SA_AI_Markdown_Cron();
			$cron->process_all_posts();
			add_settings_error( 'sa_ai_markdown_messages', 'sa_ai_markdown_message', 'Markdown cache regeneration triggered!', 'updated' );
		}
	}

	/**
	 * Sanitize post types array.
	 */
	public function sanitize_post_types( $input ) {
		if ( ! is_array( $input ) ) {
			return [];
		}
		return array_map( 'sanitize_key', $input );
	}


	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$selected_types = get_option( 'sa_ai_markdown_post_types', [ 'post', 'page' ] );
		$content_signal = get_option( 'sa_ai_markdown_content_signal', 'ai-train=yes, search=yes, ai-input=yes' );
		$all_post_types = get_post_types( [ 'public' => true ], 'objects' );

		?>
		<div class="wrap">
			<h1>AI Markdown Settings</h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'sa_ai_markdown_settings' ); ?>
				<?php do_settings_sections( 'sa_ai_markdown_settings' ); ?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row">Enabled Post Types</th>
						<td>
							<?php foreach ( $all_post_types as $type ) : ?>
								<label>
									<input type="checkbox" name="sa_ai_markdown_post_types[]" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( in_array( $type->name, (array) $selected_types ) ); ?>>
									<?php echo esc_html( $type->label ); ?>
								</label><br>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">X-Content-Signal Extra</th>
						<td>
							<input type="text" name="sa_ai_markdown_content_signal" value="<?php echo esc_attr( $content_signal ); ?>" class="regular-text">
							<p class="description">Additional signals to append to the X-Content-Signal header (e.g., <code>ai-train=yes, search=yes, ai-input=yes</code>).</p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<hr>

			<h2>Manual Actions</h2>
			<p>Click below to manually trigger the Markdown cache regeneration for all selected post types.</p>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'options-general.php?page=sa-ai-markdown&action=regenerate_markdown' ), 'sa_ai_markdown_regenerate' ) ); ?>" class="button button-secondary">Regenerate Markdown Cache Now</a>
		</div>
		<?php
	}
}
