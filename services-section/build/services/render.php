<?php
$prefix = 'ssbServices';
$id = wp_unique_id( "$prefix-" );

extract( $attributes );
?>
<div <?php echo get_block_wrapper_attributes(); ?> id='<?php echo esc_attr( $id ); ?>' data-attributes='<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>'>
	<div class='<?php echo esc_attr( $prefix ); ?>Style'></div>

	<div class='<?php echo esc_attr( $prefix ); ?> <?php echo esc_attr( $layout ); ?> columns-<?php echo esc_attr( $columns['desktop'] ); ?> columns-tablet-<?php echo esc_attr( $columns['tablet'] ); ?> columns-mobile-<?php echo esc_attr( $columns['mobile'] ); ?>'>
		<?php echo wp_kses_post( $content ); ?>
	</div>
</div>