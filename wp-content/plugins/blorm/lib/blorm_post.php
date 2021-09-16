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

/*add_action("manage_posts_custom_column", "my_custom_columns");

function my_custom_columns($column)
{
    global $post;
    if ("ID" == $column) echo $post->ID; //displays title
    elseif ("description" == $column) echo $post->post_content; //displays the content excerpt
    elseif ("thumbnail" == $column) echo $post->post_thumbnail; //shows up our post thumbnail that we previously created.
}*/

/*
add_action('admin_menu','remove_blormpost_from_menu');

function remove_blormpost_from_menu() {
    remove_menu_page('edit.php?post_type=blormpost');
}

function blorm_post_add_meta_irl( $post_id ) {

    // If this is just a revision, don't send the email.
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }

    if ( get_post_type() == 'blormpost' ) {
        add_post_meta( $post_id, "blorm_irl", "the-unique-blorm-irl");
    }

    return $post_id;
}*/
//add_action( 'save_post', 'blorm_post_add_meta_irl' );

/*
// register custom post type 'my_custom_post_type' with 'supports' parameter
add_action( 'init', 'create_my_post_type' );
function create_my_post_type() {
    register_post_type( 'my_custom_post_type',
        array(
            'labels' => array( 'name' => __( 'Products' ) ),
            'public' => true,
            'supports' => array('title', 'editor', 'post-formats')
        )
    );
}*/

/*apply_filters('found_posts ','blorm_found_posts');
function blorm_found_posts($posts) {

    //var_dump($posts);

    return $posts;
}*/
/*
add_filter( 'post_class', 'new_class', 10,3);
function new_class (array $classes, $class, $id) {
    $newclass = 'blorm-reblogged';

    if ($id == 21) {
        $classes[] = esc_attr( $newclass );
    }

    return $classes;
}*/