<?php
$id = wp_unique_id('ssbService-');

// Sanitize the link attribute to prevent XSS via dangerous protocols
if ( isset( $attributes['link'] ) ) {
	$attributes['link'] = esc_url( $attributes['link'], wp_allowed_protocols() );
}
?>
<div <?php echo get_block_wrapper_attributes(); ?> id='<?php echo esc_attr($id); ?>' data-attributes='<?php echo esc_attr(wp_json_encode($attributes)); ?>'></div>
