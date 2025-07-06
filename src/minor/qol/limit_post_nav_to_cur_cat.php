<?php
/**
 * Limit post navigation to the current category php
 */
add_filter( 'generate_post_navigation_args', function( $args ) {
    $args['in_same_term'] = true;
    return $args;
} );