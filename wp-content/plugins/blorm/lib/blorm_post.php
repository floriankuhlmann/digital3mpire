<?php

// init add the blorm post type
// https://codex.wordpress.org/Function_Reference/register_post_type
add_action( 'init',  'create_post_type_blormpost');
function create_post_type_blormpost() {
    $type = 'blormpost';
    $labels = array('Blormpost', 'Blormposts');
    $arguments =  array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => false,
        'show_in_admin_bar' => false,
        'show_in_nav_menus' => false,
        'show_in_menu' => false,
        'has_archive' => false,
    );
    register_post_type( $type, $arguments);
}

// change the links of the page to redirect direct to external posts on the origin source
add_filter('post_type_link', 'blormpost_type_link', 1, 2);
function blormpost_type_link( $link, $post = 0 ){
    return get_post_meta($post->ID, 'blorm_reblog_teaser_url')[0];
}
