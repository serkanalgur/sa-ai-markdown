<?php

/*
* Plugin Name:       AI Markdown Content Negotiator
* Plugin URI:        https://github.com/serkanalgur/sa-ai-markdown
* Description:       Detects Accept: text/markdown and serves pre-generated Markdown versions of posts/pages.
* Version:           1.0.1
* Requires at least: 6.0
* Tested up to: 6.9
* Requires PHP:      7.3
* Author:            Serkan Algur
* Author URI:        https://serkanalgur.com.tr
* License:           GPL-2.0+
* Text Domain:       sa-ai-markdown
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
define( 'SA_AI_MARKDOWN_VERSION', '1.0.1' );
define( 'SA_AI_MARKDOWN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SA_AI_MARKDOWN_URL', plugin_dir_url( __FILE__ ) );

// Include classes
require_once SA_AI_MARKDOWN_PATH . 'includes/class-sa-ai-markdown-generator.php';
require_once SA_AI_MARKDOWN_PATH . 'includes/class-sa-ai-markdown-cron.php';
require_once SA_AI_MARKDOWN_PATH . 'includes/class-sa-ai-markdown-negotiator.php';
require_once SA_AI_MARKDOWN_PATH . 'includes/class-sa-ai-markdown-settings.php';

/**
 * Initialize the plugin
 */
function sa_ai_markdown_init() {
	new SA_AI_Markdown_Generator();
	new SA_AI_Markdown_Cron();
	new SA_AI_Markdown_Negotiator();
	new SA_AI_Markdown_Settings();
}
add_action( 'plugins_loaded', 'sa_ai_markdown_init' );

/**
 * Activation hook
 */
register_activation_hook( __FILE__, [ 'SA_AI_Markdown_Cron', 'activate' ] );

/**
 * Deactivation hook
 */
register_deactivation_hook( __FILE__, [ 'SA_AI_Markdown_Cron', 'deactivate' ] );
