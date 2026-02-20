<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SA_AI_Markdown_Generator {

	public function __construct() {
		// Nothing to initialize here for now
	}

	/**
	 * Convert post content and metadata to Markdown with YAML frontmatter.
	 *
	 * @param WP_Post $post The post object.
	 * @return string The Markdown content.
	 */
	public function generate_markdown( $post ) {
		if ( ! $post instanceof WP_Post ) {
			return '';
		}

		$frontmatter = [
			'title'     => $post->post_title,
			'date'      => $post->post_date,
			'author'    => get_the_author_meta( 'display_name', $post->post_author ),
			'permalink' => get_permalink( $post->ID ),
			'categories'=> wp_get_post_categories( $post->ID, [ 'fields' => 'names' ] ),
			'tags'      => wp_get_post_tags( $post->ID, [ 'fields' => 'names' ] ),
		];

		$markdown = "---\n";
		foreach ( $frontmatter as $key => $value ) {
			if ( is_array( $value ) ) {
				$markdown .= "$key: [" . implode( ', ', array_map( [ $this, 'quote' ], $value ) ) . "]\n";
			} else {
				$markdown .= "$key: " . $this->quote( $value ) . "\n";
			}
		}
		$markdown .= "---\n\n";

		// Convert Gutenberg blocks or classic content
		$content = $post->post_content;
		if ( has_blocks( $content ) ) {
			$markdown .= $this->convert_blocks_to_markdown( $content );
		} else {
			$markdown .= $this->convert_html_to_markdown( $content );
		}

		return trim( $markdown );
	}

	/**
	 * Basic block-to-markdown conversion.
	 */
	private function convert_blocks_to_markdown( $content ) {
		$blocks = parse_blocks( $content );
		$output = '';

		foreach ( $blocks as $block ) {
			if ( ! empty( $block['blockName'] ) ) {
				switch ( $block['blockName'] ) {
					case 'core/paragraph':
						$output .= wp_strip_all_tags( $block['innerHTML'] ) . "\n\n";
						break;
					case 'core/heading':
						$level = isset( $block['attrs']['level'] ) ? $block['attrs']['level'] : 2;
						$output .= str_repeat( '#', $level ) . ' ' . wp_strip_all_tags( $block['innerHTML'] ) . "\n\n";
						break;
					case 'core/list':
						$output .= $this->convert_html_to_markdown( $block['innerHTML'] ) . "\n";
						break;
					case 'core/image':
						$url = isset( $block['attrs']['url'] ) ? $block['attrs']['url'] : '';
						$alt = isset( $block['attrs']['alt'] ) ? $block['attrs']['alt'] : '';
						$output .= "![$alt]($url)\n\n";
						break;
					case 'core/code':
						$output .= "```\n" . wp_strip_all_tags( $block['innerHTML'] ) . "\n```\n\n";
						break;
					default:
						// Fallback for other blocks
						$output .= wp_strip_all_tags( render_block( $block ) ) . "\n\n";
						break;
				}
			}
		}

		return $output;
	}

	/**
	 * Simple HTML to Markdown fallback.
	 */
	private function convert_html_to_markdown( $html ) {
		// Very basic regex-based conversion for common tags
		$markdown = $html;

		// Headings
		$markdown = preg_replace( '/<h([1-6])>(.*?)<\/h\1>/i', "\n" . str_repeat( '#', 1 ) . ' $2' . "\n", $markdown );
		
		// Links
		$markdown = preg_replace( '/<a href="(.*?)">(.*?)<\/a>/i', '[$2]($1)', $markdown );

		// Bold/Italic
		$markdown = preg_replace( '/<(strong|b)>(.*?)<\/\1>/i', '**$2**', $markdown );
		$markdown = preg_replace( '/<(em|i)>(.*?)<\/\1>/i', '*$2*', $markdown );

		// Lists
		$markdown = preg_replace( '/<li>(.*?)<\/li>/i', "- $1\n", $markdown );
		$markdown = preg_replace( '/<(ul|ol)>|<\/\1>/i', '', $markdown );

		// Strip remaining tags
		$markdown = wp_strip_all_tags( $markdown );

		return $markdown;
	}

	/**
	 * Estimate token count based on heuristic.
	 */
	public static function estimate_markdown_tokens( $content ) {
		// ~4 characters per token
		$char_count = mb_strlen( $content );
		return ceil( $char_count / 4 );
	}

	/**
	 * Helper to quote YAML strings.
	 */
	private function quote( $str ) {
		return '"' . str_replace( '"', '\"', $str ) . '"';
	}
}
