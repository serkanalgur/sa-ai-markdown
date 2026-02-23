<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SA_AI_Markdown_Cron {

	const CRON_EVENT = 'sa_ai_markdown_regenerate_cache';
	const META_KEY_MARKDOWN = '_sa_markdown_cache';
	const META_KEY_TOKENS = '_sa_markdown_tokens';
	const META_KEY_MODIFIED = '_sa_markdown_last_modified';

	public function __construct() {
		add_action( self::CRON_EVENT, [ $this, 'process_all_posts' ] );
		// regenerate markdown whenever a post is saved/updated
		add_action( 'save_post', [ $this, 'handle_save_post' ], 10, 3 );
	}

	/**
	 * Register the scheduled event on activation.
	 */
	public static function activate() {
		if ( ! wp_next_scheduled( self::CRON_EVENT ) ) {
			wp_schedule_event( time(), 'hourly', self::CRON_EVENT );
		}
	}

	/**
	 * Unregister the event on deactivation.
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( self::CRON_EVENT );
	}

	/**
	 * Process all published posts/pages and update their markdown cache.
	 */
	public function process_all_posts() {
		$post_types = (array) get_option( 'sa_ai_markdown_post_types', [ 'post', 'page' ] );
		
		$args = [
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => 50, // Process in chunks to avoid timeouts
			'fields'         => 'ids',
		];

		$query = new WP_Query( $args );
		$post_ids = $query->posts;

		if ( empty( $post_ids ) ) {
			return;
		}

		$generator = new SA_AI_Markdown_Generator();

		foreach ( $post_ids as $post_id ) {
			$post = get_post( $post_id );
			
			// Check if modification date has changed
			$last_modified = get_post_meta( $post_id, self::META_KEY_MODIFIED, true );
			if ( $last_modified === $post->post_modified ) {
				continue;
			}

			$markdown = $generator->generate_markdown( $post );
			$tokens = SA_AI_Markdown_Generator::estimate_markdown_tokens( $markdown );

			update_post_meta( $post_id, self::META_KEY_MARKDOWN, $markdown );
			update_post_meta( $post_id, self::META_KEY_TOKENS, $tokens );
			update_post_meta( $post_id, self::META_KEY_MODIFIED, $post->post_modified );
		}
	}

	/**
	 * Manually trigger regeneration for a specific post (useful for testing or one-off updates).
	 */
	public function regenerate_post( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return;
		}

		$generator = new SA_AI_Markdown_Generator();
		$markdown = $generator->generate_markdown( $post );
		$tokens = SA_AI_Markdown_Generator::estimate_markdown_tokens( $markdown );

		update_post_meta( $post_id, self::META_KEY_MARKDOWN, $markdown );
		update_post_meta( $post_id, self::META_KEY_TOKENS, $tokens );
		update_post_meta( $post_id, self::META_KEY_MODIFIED, $post->post_modified );
	}

	/**
	 * Callback for save_post action. Regenerates cached markdown for the post
	 * when content is updated.
	 *
	 * @param int     $post_id Post ID being saved.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated.
	 */
	public function handle_save_post( $post_id, $post, $update ) {
		// bail for revisions or autosaves
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// only regenerate for configured post types
		$post_types = (array) get_option( 'sa_ai_markdown_post_types', [ 'post', 'page' ] );
		if ( ! in_array( $post->post_type, $post_types, true ) ) {
			return;
		}

		// only regenerate when there is content or status changes; skip for trash
		if ( 'trash' === $post->post_status ) {
			return;
		}

		$this->regenerate_post( $post_id );
	}
}
