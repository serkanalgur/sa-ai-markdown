=== AI Markdown Content Negotiator ===
Contributors: serkanalgur
Donate link: https://serkanalgur.com.tr
Tags: markdown, ai, content negotiation, gutenberg, caching
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.3
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Detects Accept: text/markdown and serves pre-generated Markdown versions of posts and pages for AI agents and LLMs.

== Description ==

AI Markdown Content Negotiator is a performance-optimized WordPress plugin designed to serve your website's content in a format that AI agents and LLMs (Large Language Models) love: Clean Markdown.

Using standard HTTP Content Negotiation, the plugin detects when a request is made with the `Accept: text/markdown` header. Instead of serving the standard HTML theme, it returns a Markdown version of the post or page, complete with YAML Frontmatter, token estimation headers, and customizable content signals.

To ensure maximum performance and minimal server load, Markdown versions are pre-generated and cached using WP-Cron.

= Key Features =

*   **Content Negotiation**: Automatically switches to Markdown output when requested via the `Accept: text/markdown` header.
*   **YAML Frontmatter**: Includes metadata like Title, Date, Author, and Categories in a structured format.
*   **Performance Tracking**: Provides an `X-Markdown-Tokens` header using standard LLM token heuristics.
*   **AI Metadata**: Includes `X-Content-Signal` headers to help agents understand the nature of the document.
*   **Background Caching**: Uses WP-Cron to pre-calculate Markdown strings, ensuring zero latency during requests.
*   **Admin Settings**: Choose which post types to enable and configure global AI content signals.

== Installation ==

1. Upload the `sa-ai-markdown` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure your preferences under 'Settings > AI Markdown'.

== Frequently Asked Questions ==

= How do I test the Markdown output? =

You can test it using a tool like cURL:

`curl -H "Accept: text/markdown" https://your-site.com/post-slug/`

= How are tokens calculated? =

We use a standard heuristic of ~4 characters per token to provide an estimate in the `X-Markdown-Tokens` header.

== Screenshots ==

1. The AI Markdown settings page.

== Changelog ==

= 1.0.0 =
* Initial release.
