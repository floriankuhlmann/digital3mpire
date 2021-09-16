<?php

// modify the css-classes of the posts
add_filter( 'post_class', 'blorm_created_class',10,3);
function blorm_created_class (array $classes, $class, $post_id) {

    $options = get_option("blorm_plugin_options_frontend");

    if (!isset($options['position_widget_menue']))
        return $classes;

    $a = get_post_meta($post_id);
    if (isset($a["blorm_create"])) {
        array_push($classes, 'blorm-shared');

        if ( $options['position_widget_menue'] === 'add_blorm_info_on_image' )
            array_push($classes, 'blormwidget-on-image-post');
    }

    if (isset($a["blorm_reblog_activity_id"])) {
        array_push($classes, 'blorm-rebloged');

        if ( $options['position_widget_menue'] === 'add_blorm_info_on_image' )
            array_push($classes, 'blormwidget-on-image-post');
    }

    // add the standard 'post' class for layout-consistence
    if (isset($a["blorm_create"]) || isset($a["blorm_reblog_activity_id"]))
        array_push($classes, 'post');

    return $classes;
}

//https://developer.wordpress.org/plugins/post-types/working-with-custom-post-types/
add_action( 'pre_get_posts', 'blorm_add_posttype_blorm_to_loop' );
function blorm_add_posttype_blorm_to_loop( $query ) {

    $options_config = get_option( 'blorm_plugin_options_frontend' );
    $options_cat = get_option( 'blorm_plugin_options_category' );

    if (isset($options_config['display_config'])) {

        switch ($options_config['display_config']) {

            case "display_config_widget":
                return $query;
                break;

            case "display_config_category":
                // category
                if (isset($options_cat['blorm_category_show_reblogged'])) {
                    if( $query->is_category($options_cat['blorm_category_show_reblogged'])) {
                        $query->set( 'post_type', array( 'blormpost' ));
                        return $query;
                    }
                }
                break;

            case "display_config_loop":
                if ($query->is_main_query()) {
                    $query->set('post_type', array('post', 'blormpost'));
                    return $query;
                }
                break;

            case "display_config_loop_and_widget":
                if ($query->is_main_query()) {
                    $query->set('post_type', array('post', 'blormpost'));
                    return $query;
                }
                break;

            case "display_config_loop_and_category":
                // category
                if (isset($options_cat['blorm_category_show_reblogged'])) {
                    if( $query->is_category($options_cat['blorm_category_show_reblogged'])) {
                        $query->set( 'post_type', array( 'blormpost' ));
                        return $query;
                    }
                }
                // main query
                $query->set( 'post_type', array( 'post', 'blormpost' ));
                return $query;
                break;

            case "display_config_category_and_widget":
                // category
                if (isset($options_cat['blorm_category_show_reblogged'])) {
                    if( $query->is_category($options_cat['blorm_category_show_reblogged'])) {
                        $query->set( 'post_type', array( 'blormpost' ));
                        return $query;
                    }
                }
                if ($query->is_main_query()) {
                    return $query;
                }
                break;

            case "display_config_loop_and_category_and_widget":
                if (isset($options_cat['blorm_category_show_reblogged'])) {
                    if( $query->is_category($options_cat['blorm_category_show_reblogged'])) {
                        $query->set( 'post_type', array( 'blormpost' ));
                        return $query;
                    }
                }
                // main query
                if ($query->is_main_query()) {
                    $query->set('post_type', array('post', 'blormpost'));
                    return $query;
                }
                break;
        }
    }

    /*if (isset($options_cat['blorm_category_show_reblogged'])) {

        if( $query->is_category($options_cat['blorm_category_show_reblogged'])) {
            $query->set( 'post_type', array( 'blormpost' ));
            return $query;
        }

        /*if( $query->is_main_query() && $query->is_home() && $options_cat['blorm_category_show_reblogged'] !== 'no-category-selected') {
            $query->set( 'cat', '-'.$options_cat['blorm_category_show_reblogged'] );
            return $query;
        }
    }

    if ($query->is_main_query()) {

        return $query;
    }*/

    return $query;
}

/*
 *
 * if there is a category for showing the blorm posts lets remove the rebloged post from the mainloop
 */
//add_action( 'pre_get_posts', 'blorm_remove_posts_from_home_page' );
/*function blorm_remove_posts_from_home_page( $query ) {

    $options = get_option( 'blorm_plugin_options_category' );

    if (isset( $options['blorm_category_show_reblogged'] )) {
        if( $query->is_main_query() && $query->is_home() )
            $query->set( 'cat', '-'.$options['blorm_category_show_reblogged'] );
    }

    return $query;
}*/

/*
 *
 * if there is a category for showing the blorm posts lets remove the rebloged post from the mainloop
 */
//add_action( 'pre_get_posts', 'blorm_add_posts_to_loop_by_category' );
/*function blorm_add_posts_to_loop_by_category( $query ) {

    $options = get_option( 'blorm_plugin_options_category' );

    if (isset( $options['blorm_category_show_reblogged'] )) {
        if( $query->is_category($options['blorm_category_show_reblogged'])) {
            $query->set( 'post_type', array( 'blormpost' ));
        }
    }

    return $query;
}*/


add_action( 'the_posts', 'blorm_mod_the_posts' );
function blorm_mod_the_posts($posts) {

    $options = get_option("blorm_plugin_options_frontend");

    foreach ($posts as $post) {

        $a = get_post_meta($post->ID);

        $acivityId = "";
        $post_class = "blorm-post-data";
        if (isset($a["blorm_reblog_activity_id"])) {
            $post_class= "blorm-reblog-post-data";
            $acivityId = $a['blorm_reblog_activity_id'][0];
        }

        if (isset($a["blorm_create_activity_id"])) {
            $post_class= "blorm-create-post-data";
            $acivityId = $a['blorm_create_activity_id'][0];
        }

        if (isset($a["blorm_reblog_activity_id"]) || isset($a["blorm_create_activity_id"])) {

            // modify title
            if ( isset( $options['position_widget_menue']) ) {
                if ( $options['position_widget_menue'] === 'add_blorm_info_before_title' ) {

                    $post->post_title = '<div class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></div>'.$post->post_title;
                }
            }

            if ( isset( $options['position_widget_menue']) ) {
                if ( $options['position_widget_menue'] === 'add_blorm_info_after_title' ) {

                    $post->post_title = $post->post_title . '<div class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></div>';
                    if (isset($a["blorm_reblog_activity_id"])) {
                        $post->post_title = 'Rebloged: '. $post->post_title;
                    }
                }
            }

            // modify content
            if ( isset( $options['position_widget_menue']) ) {
                if ( $options['position_widget_menue'] === 'add_blorm_info_before_content' ) {
                    if (isset($a["blorm_reblog_activity_id"])) {
                        $post->post_title = 'Rebloged: '. $post->post_title;
                    }

                    $post->post_content = '<div class="blorm-post-content-container '.$post_class.'" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"><span class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></span>'.$post->post_content.'</div>';
                    //$post->post_excerpt = $post->post_content;
                }
            }

            if ( isset( $options['position_widget_menue']) ) {
                if ( $options['position_widget_menue'] === 'add_blorm_info_after_content' ) {
                    if (isset($a["blorm_reblog_activity_id"])) {
                        $post->post_title = 'Rebloged: '. $post->post_title;
                    }

                    $post->post_content = '<div class="blorm-post-content-container '.$post_class.'" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'">'.$post->post_content.'<span class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></span></div>';
                    //$post->post_excerpt = $post->post_content;
                }
            }

            // modify content to place on image
            if ( isset( $options['position_widget_menue']) ) {
                if ( $options['position_widget_menue'] === 'add_blorm_info_on_image' ) {
                    if (isset($a["blorm_reblog_activity_id"])) {
                        $post->post_title = '<span class="material-icons">content_copy</span>'.$post->post_title;
                    }

                    $post->post_content = '<div class="blorm-post-content-container '.$post_class.'" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'">'.$post->post_content.'</div>';
                    //$post->post_excerpt = $post->post_content;
                }
            }

        }
    }

    return $posts;
}
