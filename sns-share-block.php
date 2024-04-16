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
	register_block_bindings_source( 'chiilog/share-button-link', array(
		'label'              => __( 'SNS URL', 'chiilog/share-button' ),
		'get_value_callback' => 'chiilog_sns_data_bindings',
		'uses_context'       => [ 'postId' ]
	) );

	register_block_bindings_source( 'chiilog/share-button-target', array(
		'label'              => __( 'Link Target', 'chiilog/share-button' ),
		'get_value_callback' => function () {
			return '_blank';
		}
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
