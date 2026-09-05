<?php
/**
 * Server render for the Ribbon Cards block.
 *
 * Emits an empty, uniquely-id'd wrapper carrying the block attributes as JSON.
 * view.js hydrates it with the same React components the editor renders, so there
 * is a single markup source instead of a parallel PHP template.
 *
 * @var array $attributes Block attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id = wp_unique_id( 'bBlocksRibbonCards-' );
?>
<div
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped by core.
	echo get_block_wrapper_attributes();
	?>
	id="<?php echo esc_attr( $id ); ?>"
	data-attributes="<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>"
></div>
