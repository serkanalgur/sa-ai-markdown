<?php
/**
 * Plugin Name:       Markdown Content Negotiator for LLMs
 * Plugin URI:        https://github.com/serkanalgur/markdown-content-negotiator-for-llms
 * Description:       Detects Accept: text/markdown and serves pre-generated Markdown versions of posts/pages.
 * Version:           1.0.7
 * Requires at least: 6.0
 * Tested up to:      6.9
 * Requires PHP:      7.3
 * Author:            Serkan Algur
 * Author URI:        https://github.com/serkanalgur
 * License:           GPL-2.0+
 * Text Domain:       markdown-content-negotiator-for-llms
 *
 * @package           Markdown_Content_Negotiator_For_LLMs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define constants
 */
define( 'MARKDOWN_CONTENT_NEGOTIATOR_VERSION', '1.0.6' );
define( 'MARKDOWN_CONTENT_NEGOTIATOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'MARKDOWN_CONTENT_NEGOTIATOR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Include classes
 */
require_once MARKDOWN_CONTENT_NEGOTIATOR_PATH . 'includes/class-markdown-content-negotiator-for-llms-generator.php';
require_once MARKDOWN_CONTENT_NEGOTIATOR_PATH . 'includes/class-markdown-content-negotiator-for-llms-cron.php';
require_once MARKDOWN_CONTENT_NEGOTIATOR_PATH . 'includes/class-markdown-content-negotiator-for-llms-negotiator.php';
require_once MARKDOWN_CONTENT_NEGOTIATOR_PATH . 'includes/class-markdown-content-negotiator-for-llms-settings.php';

/**
 * Initialize the plugin
 */
function Markdown_Content_Negotiator_For_LLMs_init() {
	new Markdown_Content_Negotiator_For_LLMs_Generator();
	new Markdown_Content_Negotiator_For_LLMs_Cron();
	new Markdown_Content_Negotiator_For_LLMs_Negotiator();
	new Markdown_Content_Negotiator_For_LLMs_Settings();
}
add_action( 'plugins_loaded', 'Markdown_Content_Negotiator_For_LLMs_init' );

/**
 * Activation hook
 */
register_activation_hook( __FILE__, array( 'Markdown_Content_Negotiator_For_LLMs_Cron', 'activate' ) );

/**
 * Deactivation hook
 */
register_deactivation_hook( __FILE__, array( 'Markdown_Content_Negotiator_For_LLMs_Cron', 'deactivate' ) );
