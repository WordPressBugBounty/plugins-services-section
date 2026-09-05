<?php

namespace SSB;

class Init {
    /**
     * Blocks that are always registered, regardless of licence.
     *
     * `services` + `service` are the original free blocks (kept verbatim for
     * backward compatibility) and `parent` is the selector/chooser UI, which every
     * user needs so the shortcode CPT can render its template.
     */
    const FREE_BLOCKS = [ 'services', 'service', 'parent' ];

    function __construct() {
        add_action( 'init', [ $this, 'onInit' ] );
        add_filter( 'block_categories_all', [ $this, 'registerBlockCategory' ], 10, 2 );
    }

    function onInit() {
        $this->ssb_register_blocks();

        wp_set_script_translations( 'services-section-services-editor-script', 'services-section', SSB_DIR_PATH . 'languages' );
    }

    /**
     * Loop through build/blocks/ and register each block.
     * Free blocks are always registered.
     * Pro blocks are only registered when premium is active.
     * Disabled blocks (toggled OFF in the dashboard) are skipped entirely, so they
     * never reach the inserter.
     */
    function ssb_register_blocks() {
        $blocks_path = SSB_DIR_PATH . 'build/blocks/';

        // Use scandir instead of glob to prevent issues on restricted servers
        if ( ! is_dir( $blocks_path ) ) {
            return;
        }

        $files      = scandir( $blocks_path );
        $all_blocks = [];

        foreach ( $files as $file ) {
            if ( $file !== '.' && $file !== '..' && is_dir( $blocks_path . $file ) ) {
                $all_blocks[] = $blocks_path . $file;
            }
        }

        if ( empty( $all_blocks ) ) {
            return;
        }

        // Blocks toggled OFF by the admin dashboard.
        $disabled_blocks = get_option( 'ssbBlocks', [] );
        if ( ! is_array( $disabled_blocks ) ) {
            $disabled_blocks = [];
        }

        $is_premium = ssbIsPremium();

        // Register the shared editor bundle used by every pro block, so the editor
        // downloads it once instead of once per block.
        if ( $is_premium ) {
            $asset_path = SSB_DIR_PATH . 'build/blocks/index.asset.php';
            $asset_file = file_exists( $asset_path )
                ? include $asset_path
                : [
                    'dependencies' => [ 'wp-blocks', 'wp-element', 'wp-i18n' ],
                    'version'      => SSB_VERSION,
                ];

            wp_register_script(
                'ssb-pro-blocks',
                SSB_DIR_URL . 'build/blocks/index.js',
                $asset_file['dependencies'],
                $asset_file['version'],
                true
            );

            wp_register_style(
                'ssb-pro-blocks',
                SSB_DIR_URL . 'build/blocks/index.css',
                [],
                $asset_file['version']
            );
        }

        foreach ( $all_blocks as $block_path ) {
            $block_name = basename( $block_path );

            // Skip if admin toggled this block OFF.
            if ( in_array( $block_name, $disabled_blocks, true ) ) {
                continue;
            }

            // `service` is the child of `services`; registering it while its parent is
            // off would leave an orphan block, so it follows the parent's state.
            if ( 'service' === $block_name && in_array( 'services', $disabled_blocks, true ) ) {
                continue;
            }

            // Free blocks — always register.
            if ( in_array( $block_name, self::FREE_BLOCKS, true ) ) {
                register_block_type( $block_path );
                continue;
            }

            // All other blocks are Pro — only registered when premium is active.
            if ( $is_premium ) {
                register_block_type( $block_path, [
                    'editor_script' => 'ssb-pro-blocks',
                    'editor_style'  => 'ssb-pro-blocks',
                ] );
            }
        }

        // `service` declares the `services` editor bundle as its editorScript so the
        // pair ships in one file; that makes WordPress mint a duplicate handle for the
        // same asset, which we drop here.
        wp_deregister_script( 'services-section-service-editor-script' );
    }

    /**
     * Register block category at the top of the list.
     */
    function registerBlockCategory( $categories, $context ) {
        if ( ! is_array( $categories ) ) {
            $categories = [];
        }

        // Prevent duplicate category
        foreach ( $categories as $category ) {
            if ( isset( $category['slug'] ) && 'services-section' === $category['slug'] ) {
                return $categories;
            }
        }

        // Add category at top
        array_unshift( $categories, [
            'slug'  => 'services-section',
            'title' => __( 'Services Section', 'services-section' ),
            'icon'  => null,
        ] );

        return $categories;
    }
}
