<?php
namespace SSB;

class Shortcode {
    private $post_type = 'services_section';

    function __construct() {
        add_action( 'init', [ $this, 'onInit' ] );
        add_shortcode( 'services_section', [ $this, 'ssb_shortcode' ] );

        add_filter( 'use_block_editor_for_post', [ $this, 'useBlockEditorForPost' ], 999, 2 );
    }

    /**
     * The shortcode post type.
     *
     * Every argument below is preserved from the original registration in SSB\Init so
     * existing posts, menu position and the Freemius menu slug
     * (`edit.php?post_type=services_section`) keep working untouched. Only `template`
     * is licence-dependent, and a template applies to NEW posts only:
     *   premium → the selector UI, so any Services block can be chosen
     *   free    → straight to the Services Section block, exactly as before
     */
    function onInit() {
        if ( ssbIsPremium() ) {
            $template = [ [ 'services-section/services-selector' ] ];
        } else {
            $template = [ [ 'services-section/services' ] ];
        }

        register_post_type( $this->post_type, [
            'label'  => __( 'Services Section', 'services-section' ),
            'labels' => [
                'add_new'      => __( 'Add New', 'services-section' ),
                'add_new_item' => __( 'Add New Service', 'services-section' ),
                'edit_item'    => __( 'Edit Service', 'services-section' ),
                'not_found'    => __( 'There was no Service please add one', 'services-section' ),
            ],
            'show_in_rest'       => true,
            'public'             => true,
            'publicly_queryable' => false,
            'menu_icon'          => 'dashicons-portfolio',
            'item_published'     => __( 'Services Section Published', 'services-section' ),
            'item_updated'       => __( 'Services Section Updated', 'services-section' ),
            'template'           => $template,
            // Only $blocks[0] is ever rendered, so the root list is pinned to one block.
            // Inner service items stay editable — the `services` block passes
            // templateLock={false} to its own InnerBlocks.
            'template_lock'      => 'all',
        ] );
    }

    /**
     * [services_section id="123"]
     */
    function ssb_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'id' => 0,
        ], $atts, 'services_section' );

        $post_id = intval( $atts['id'] );
        if ( ! $post_id ) {
            return '';
        }

        $post = get_post( $post_id );

        if ( ! $post ) {
            return '';
        }

        if ( post_password_required( $post ) ) {
            return get_the_password_form( $post );
        }

        switch ( $post->post_status ) {
            case 'publish':
                return $this->displayContent( $post );

            case 'private':
                if ( current_user_can( 'read_private_posts' ) ) {
                    return $this->displayContent( $post );
                }
                return '';

            case 'draft':
            case 'pending':
            case 'future':
                if ( current_user_can( 'edit_post', $post_id ) ) {
                    return $this->displayContent( $post );
                }
                return '';

            default:
                return '';
        }
    }

    function displayContent( $post ) {
        $blocks = parse_blocks( $post->post_content );

        if ( empty( $blocks ) || ! isset( $blocks[0] ) ) {
            return '';
        }

        return render_block( $blocks[0] );
    }

    function useBlockEditorForPost( $use, $post ) {
        if ( is_object( $post ) && isset( $post->post_type ) && $this->post_type === $post->post_type ) {
            return true;
        }

        return $use;
    }
}
