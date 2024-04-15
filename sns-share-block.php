<?php
/**
 * Plugin Name:       Chiilog Share Button
 * Description:       block binding api test.
 * Requires at least: 6.5
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            mel_cha
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       chiilog-share-button
 *
 * @package ChiilogShareButton
 */

add_action( 'init', 'chiilog_register_block_bindings' );

function chiilog_register_block_bindings() {
	register_block_bindings_source( 'chiilog/share-button', array(
		'label'              => __( 'SNS', 'chiilog-share-button' ),
		'get_value_callback' => 'chiilog_sns_data_bindings',
		'uses_context'       => [ 'postId' ]
	) );
}

/**
 * SNSのシェアリンクを返す
 *
 * @param array $source_args
 * @param WP_Block $block_instance
 *
 * @return string|null
 */
function chiilog_sns_data_bindings( $source_args, WP_Block $block_instance ) {
	$current_post_id = $block_instance->context['postId'];

	// キーがない場合はなにもしない
	if ( ! isset( $source_args['key'] ) ) {
		return null;
	}

	switch ( $source_args['key'] ) {
		case 'x':
			return 'https://twitter.com/intent/tweet?url=' . esc_url( get_permalink( $current_post_id ) ) . '&text=' . esc_attr( get_the_title( $current_post_id ) );
		case 'facebook':
			return 'https://www.facebook.com/sharer/sharer.php?u=' . esc_url( get_permalink( $current_post_id ) );
		case 'line':
			return 'https://social-plugins.line.me/lineit/share?url=' . esc_url( get_permalink( $current_post_id ) ) . '&text=' . esc_attr( get_the_title( $current_post_id ) );
		default:
			return null;
	}
}

/**
 * シェアボタンの場合、ボタンのlinkTargetを_blankに変換する
 *
 * @param string $block_content
 * @param WP_Block $block
 *
 * @return string
 */
add_filter( 'render_block_core/button', 'add_link_target_for_binding_button', 10, 2 );

function add_link_target_for_binding_button( $block_content, $block ) {
	$processor = new WP_HTML_Tag_Processor( $block_content );

	if ( isset( $block['attrs']['metadata']['bindings']['url']['source'] ) ) {
		$binding_source = $block['attrs']['metadata']['bindings']['url']['source'];
		if ( $binding_source === 'chiilog/share-button' ) {
			$processor->next_tag( array( 'class_name' => 'wp-block-button__link' ) );
			$processor->set_attribute( 'target', '_blank' );
		}
	}

	return $processor->get_updated_html();
}
