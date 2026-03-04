<?php
/**
 * Settings for AI Markdown.
 *
 * @package Markdown_Content_Negotiator_For_LLMs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Markdown_Content_Negotiator_For_LLMs_Settings
 *
 * Handles the administration settings page and option registration.
 */
class Markdown_Content_Negotiator_For_LLMs_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register the settings menu item.
	 */
	public function add_settings_menu() {
		add_options_page(
			'Markdown Content Negotiator Settings',
			'Markdown Content Negotiator',
			'manage_options',
			'markdown-content-negotiator-for-llms',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting( 'markdown_content_negotitator_for_llms_settings', 'markdown_content_negotitator_for_llms_post_types', array( 'sanitize_callback' => array( $this, 'sanitize_post_types' ) ) );
		register_setting( 'markdown_content_negotitator_for_llms_settings', 'markdown_content_negotitator_for_llms_content_signal', array( 'sanitize_callback' => 'sanitize_text_field' ) );

		if ( isset( $_GET['action'] ) && 'regenerate_markdown' === $_GET['action'] && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'markdown_content_negotitator_for_llms_regenerate' ) ) {
			$cron = new Markdown_Content_Negotiator_For_LLMs_Cron();
			$cron->process_all_posts();
			add_settings_error( 'markdown_content_negotitator_for_llms_messages', 'markdown_content_negotitator_for_llms_message', 'Markdown cache regeneration triggered!', 'updated' );
		}
	}

	/**
	 * Sanitize post types array.
	 *
	 * @param array $input The input array of post types.
	 */
	public function sanitize_post_types( $input ) {
		if ( ! is_array( $input ) ) {
			return array();
		}
		return array_map( 'sanitize_key', $input );
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$selected_types = get_option( 'markdown_content_negotitator_for_llms_post_types', array( 'post', 'page' ) );
		$content_signal = get_option( 'markdown_content_negotitator_for_llms_content_signal', 'ai-train=yes, search=yes, ai-input=yes' );
		$all_post_types = get_post_types( array( 'public' => true ), 'objects' );

		?>
		<div class="wrap">
			<h1>Markdown Content Negotiator Settings</h1>
			<form method="post" action="options.php">
		<?php settings_fields( 'markdown_content_negotitator_for_llms_settings' ); ?>
		<?php do_settings_sections( 'markdown_content_negotitator_for_llms_settings' ); ?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row">Enabled Post Types</th>
						<td>
				<?php foreach ( $all_post_types as $type ) : ?>
							<label>
								<input type="checkbox" name="markdown_content_negotitator_for_llms_post_types[]" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( in_array( $type->name, (array) $selected_types, true ) ); ?>>
							<?php echo esc_html( $type->label ); ?>
							</label><br>
				<?php endforeach; ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">X-Content-Signal Extra</th>
						<td>
							<input type="text" name="markdown_content_negotitator_for_llms_content_signal" value="<?php echo esc_attr( $content_signal ); ?>" class="regular-text">
							<p class="description">Additional signals to append to the X-Content-Signal header (e.g., <code>ai-train=yes, search=yes, ai-input=yes</code>).</p>
						</td>
					</tr>
				</table>

		<?php submit_button(); ?>
			</form>

			<hr>

			<h2>Manual Actions</h2>
			<p>Click below to manually trigger the Markdown cache regeneration for all selected post types.</p>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'options-general.php?page=markdown-content-negotiator-for-llms&action=regenerate_markdown' ), 'markdown_content_negotitator_for_llms_regenerate' ) ); ?>" class="button button-secondary">Regenerate Markdown Cache Now</a>
		</div>
		<?php
	}
}
