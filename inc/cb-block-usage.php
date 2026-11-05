<?php
/**
 * Block usage shortcode for debugging/QA — [block_usage_table].
 *
 * Lists every block file in /blocks against the published content (across
 * all public post types, not just pages/posts) that actually uses it, so
 * you can tell at a glance whether a block is safe to remove with
 * rm_block.sh.
 *
 * Instances counts block comments, not entries: a block used three times on
 * one page counts three. Where those differ the column reads "3 in 1", and
 * the entry is tagged with its own multiple in the Used In list.
 *
 * @package cb-global42026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders a table of all blocks and the content that uses them.
 *
 * @return string HTML table of block usage.
 */
function cb_global42026_block_usage_table_shortcode() {
	$blocks_dir  = get_stylesheet_directory() . '/blocks/';
	$block_files = glob( $blocks_dir . '*.php' );

	if ( ! $block_files ) {
		return '<p>No blocks found.</p>';
	}

	// Block names are kebab-case, matching both the filename and the
	// registered block type: acf_register_block_type() runs 'name' through
	// acf_slugify() regardless of how add_block.sh wrote it, so the block
	// comment stored in post content is always the hyphenated form.
	$block_names = array();
	foreach ( $block_files as $file ) {
		$block_names[] = basename( $file, '.php' );
	}

	$posts = get_posts(
		array(
			'post_type'      => get_post_types( array( 'public' => true ), 'names' ),
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		)
	);

	$usage_map = array_fill_keys( $block_names, array() );
	$totals    = array_fill_keys( $block_names, 0 );

	foreach ( $posts as $post ) {
		// Opening and self-closing block comments both start this way; the
		// closing '<!-- /wp:acf/...' does not, because of the slash before
		// 'wp'. So one match is one instance.
		preg_match_all( '/<!-- wp:acf\/([a-z0-9-]+)\s/', $post->post_content, $matches );

		if ( empty( $matches[1] ) ) {
			continue;
		}

		// array_count_values() rather than array_unique(): the same block can
		// appear several times in one entry, and that is the number wanted.
		foreach ( array_count_values( $matches[1] ) as $found_block => $count ) {
			if ( isset( $usage_map[ $found_block ] ) ) {
				$usage_map[ $found_block ][] = array(
					'post'  => $post,
					'count' => $count,
				);

				$totals[ $found_block ] += $count;
			}
		}
	}

	// Inline-styled on purpose — this is a standalone QA utility that should
	// look reasonable on any project regardless of whether it has opted
	// into src/css/tables.css.
	ob_start();
	?>
	<div style="padding: 2rem;">
	<table style="width: 100%; border-collapse: collapse;">
		<thead>
			<tr style="border-bottom: 2px solid #ccc;">
				<th style="text-align: left; padding: 8px; font-weight: bold;">Block Name</th>
				<th style="text-align: right; padding: 8px; font-weight: bold;">Instances</th>
				<th style="text-align: left; padding: 8px; font-weight: bold;">Used In</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $usage_map as $block_name => $posts_using_block ) : ?>
				<tr style="border-bottom: 1px solid #eee;">
					<td style="padding: 8px; vertical-align: top;"><?php echo esc_html( $block_name ); ?></td>
					<td style="padding: 8px; vertical-align: top; text-align: right; white-space: nowrap;">
						<?php if ( $totals[ $block_name ] ) : ?>
							<?php echo esc_html( number_format_i18n( $totals[ $block_name ] ) ); ?>
							<?php if ( count( $posts_using_block ) !== $totals[ $block_name ] ) : ?>
								<span style="color: #999; font-size: 0.9em;">
									in <?php echo esc_html( number_format_i18n( count( $posts_using_block ) ) ); ?>
								</span>
							<?php endif; ?>
						<?php else : ?>
							<span style="color: #999;">0</span>
						<?php endif; ?>
					</td>
					<td style="padding: 8px;">
						<?php if ( empty( $posts_using_block ) ) : ?>
							<em style="color: #999;">Not used</em>
						<?php else : ?>
							<ul style="margin: 0; padding-left: 20px;">
								<?php foreach ( $posts_using_block as $use ) : ?>
									<?php $post = $use['post']; ?>
									<li>
										<a href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>" target="_blank">
											<?php echo esc_html( $post->post_title ); ?>
										</a>
										<span style="color: #999; font-size: 0.9em;">(<?php echo esc_html( ucfirst( $post->post_type ) ); ?>)</span>
										<?php if ( $use['count'] > 1 ) : ?>
											<strong>&times;<?php echo esc_html( number_format_i18n( $use['count'] ) ); ?></strong>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'block_usage_table', 'cb_global42026_block_usage_table_shortcode' );
