<?php
class SSBBlock{
    function __construct(){
		add_action( 'enqueue_block_assets', [$this, 'enqueueBlockAssets'] );
		add_action( 'init', [$this, 'onInit'] );
	}

	function enqueueBlockAssets(){
		wp_register_style( 'fontAwesome', SSB_DIR_URL . 'assets/css/font-awesome.min.css', [], '6.4.2' ); // Icon
	}

	function onInit(){
		wp_register_style( 'services-section-services-style', SSB_DIR_URL . 'dist/style.css', [ 'fontAwesome' ], SSB_VERSION ); // Style
		wp_register_style( 'services-section-services-editor-style', SSB_DIR_URL . 'dist/editor.css', [ 'services-section-services-style' ], SSB_VERSION ); // Backend Style

		register_block_type( __DIR__, [
			'editor_style'		=> 'services-section-services-editor-style',
			'render_callback'	=> [$this, 'render_services']
		] ); // Register Block

		wp_set_script_translations( 'services-section-services-editor-script', 'services-section', SSB_DIR_PATH . 'languages' );
	} // Register

	function render_services( $attributes, $content ){
		extract( $attributes );

		wp_enqueue_style( 'services-section-services-style' );
		wp_enqueue_script( 'services-section-services-script', SSB_DIR_URL . 'dist/script.js', [ 'react', 'react-dom' ], SSB_VERSION, true );
		wp_set_script_translations( 'services-section-services-script', 'services-section', SSB_DIR_PATH . 'languages' );

		$className = $className ?? '';
		$blockClassName = "wp-block-services-section-services $className align$align";

		ob_start(); ?>
		<div class='<?php echo esc_attr( $blockClassName ); ?>' id='ssbServices-<?php echo esc_attr( $cId ); ?>' data-attributes='<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>'>
			<div class='ssbServicesStyle'></div>

			<div class='ssbServices <?php echo esc_attr( $layout ); ?> columns-<?php echo esc_attr( $columns['desktop'] ); ?> columns-tablet-<?php echo esc_attr( $columns['tablet'] ); ?> columns-mobile-<?php echo esc_attr( $columns['mobile'] ); ?>'>
				<?php echo wp_kses_post( $content ); ?>
			</div>
		</div>

		<?php return ob_get_clean();
	} // Render Services
}
new SSBBlock();