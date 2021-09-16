<?php

/*
 * setup js and css for the frontend rendering
 *
 */

function getBlormFrontendConfigJs() {
    $jsdata =  "var blormAssets = '".plugins_url()."/blorm/assets/';\n";
    return $jsdata;
}


// Enqueue Stylesheet and Js for frontend rendering.

add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_theme_style');
function enqueue_blorm_frontend_theme_style() {

    $catId = '';
    $options = get_option( 'blorm_plugin_options_category' );
    if (isset( $options['blorm_category_show_reblogged'] )) {
        $catId = $options['blorm_category_show_reblogged'];
    }

    if (is_home() || is_single() || is_category($catId)) {
        wp_enqueue_style ('blorm-theme-style', plugins_url('blorm/assets/css/blorm_frontend.css'));
    }
}

add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_js');
function enqueue_blorm_frontend_js() {

    $catId = '';
    $options = get_option( 'blorm_plugin_options_category' );
    if (isset( $options['blorm_category_show_reblogged'] )) {
        $catId = $options['blorm_category_show_reblogged'];
    }

    if (is_home() || is_single() || is_category($catId)) {
        wp_enqueue_script( 'blorm-mobile-detect', plugins_url( 'blorm/assets/js/mobile-detect.min.js' ) );
        wp_enqueue_script( 'blorm-theme-js', plugins_url( 'blorm/assets/js/blorm_web_widget.js' ) );
        wp_add_inline_script( 'blorm-theme-js', getBlormFrontendConfigJs(), 'before' );
    }
}

add_action( 'wp_head', 'add_getstream_data_to_head');
function add_getstream_data_to_head() {

    // POSTS ARE CREATED ON THIS PLATFORM AND SHARED ON BLORM
    // we need the information about created post on frontend rendering the posts and will collect them here
    $aBlormCreatePosts = array();

    // get all posts from this plattform that are shared on blorm
    $aRecentPostsCreate = wp_get_recent_posts(array('meta_key' => 'blorm_create', 'meta_value' => '1'));

    // the activity_id is important to connect the posts with the blorm-data
    foreach ( $aRecentPostsCreate as $aRecentPostCreate) {
        $meta = get_post_meta($aRecentPostCreate["ID"]);
        if (!empty($meta)) {
            $aBlormCreatePosts[] = array(
                "post_id" => $aRecentPostCreate["ID"],
                "activity_id" => $meta["blorm_create_activity_id"][0]
            );
        }
    }

    // POSTS ARE CREATED ON REMOTE PLATFORM AND REBLOGGED ON THIS PLATFORM
    // we need the information about reblogged post on frontend rendering the posts and will collect them here
    $aBlormReblogedPosts = array();

    // get all posts from this plattformed that are shared on blorm
    $aRecentPostsRebloged = wp_get_recent_posts(array('meta_key' => 'blorm_reblog_activity_id','post_type' => 'blormpost'));

    //var_dump($aRecentPostsReblogged);
    // the activity_id is important to connect the posts with the blorm-data
    foreach ( $aRecentPostsRebloged as $aRecentPostRebloged) {
        $meta = get_post_meta($aRecentPostRebloged["ID"]);
        if (!empty($meta)) {
            $aBlormReblogedPosts[] = array(
                "post_id" => $aRecentPostRebloged["ID"],
                "activity_id" => $meta["blorm_reblog_activity_id"][0],
                "teaser_image" => $meta["blorm_reblog_teaser_image"][0],
                "teaser_url" => $meta["blorm_reblog_teaser_url"][0],
                "teaser_iri" => $meta["blorm_reblog_object_iri"][0],
            );
        }
    }
    // ALL POSTS FROM THE GETSTREAM TIMELINE
    // we need the blorm-data like comments, shares, retweets to enrich the posts on the local plattform
    // the data is loaded every 180 seconds via cron schedule 'blorm_cron_getstream_hook' and stored to wp_options

    $bodyObjects = json_decode(get_option( 'blorm_getstream_cached_post_data' ));

    if ($bodyObjects == null) return;

    // blorm data for local usage
    $aGetStreamCreatedData = array();
    $aGetStreamReblogedData = array();

    foreach ($bodyObjects as $bodyObject) {

        $getStreamData = new stdClass();
        if (isset($bodyObject->id)) {
            // CREATED POSTS
            // search for the data of the created posts
            if (array_search($bodyObject->id, array_column($aBlormCreatePosts, "activity_id")) !== false) {

                $id = array_search($bodyObject->id, array_column($aBlormCreatePosts, "activity_id"));
                $getStreamData->PostId = $aBlormCreatePosts[$id]["post_id"];

                $getStreamData->ActivityId = $bodyObject->id;

                $getStreamData->ReblogedCount = 0;
                $getStreamData->CommentsCount = 0;
                $getStreamData->SharedCount = 0;

                if (isset($bodyObject->reaction_counts->reblog)) {
                    $getStreamData->ReblogedCount = $bodyObject->reaction_counts->reblog;
                }

                if (isset($bodyObject->latest_reactions->reblog)) {
                    $getStreamData->Rebloged = $bodyObject->latest_reactions->reblog;
                }

                if (isset($bodyObject->reaction_counts->comment)) {
                    $getStreamData->CommentsCount = $bodyObject->reaction_counts->comment;
                }

                if (isset($bodyObject->latest_reactions->comment)) {
                    $getStreamData->Comments = $bodyObject->latest_reactions->comment;
                }

                if (isset($bodyObject->reaction_counts->shared)) {
                    $getStreamData->SharedCount = $bodyObject->reaction_counts->share;
                }

                if (isset($bodyObject->latest_reactions->shared)) {
                    $getStreamData->Shared = $bodyObject->latest_reactions->share;
                }

                $aGetStreamCreatedData[$getStreamData->PostId] = $getStreamData;
            }

            // REBLOGED POSTS
            if (array_search($bodyObject->id, array_column($aBlormReblogedPosts, "activity_id")) !== false) {

                $id = array_search($bodyObject->id, array_column($aBlormReblogedPosts, "activity_id"));
                $getStreamData->PostId = $aBlormReblogedPosts[$id]["post_id"];
                $getStreamData->ActivityId = $bodyObject->id;
                $getStreamData->TeaserImage = $aBlormReblogedPosts[$id]["teaser_image"];
                $getStreamData->TeaserUrl = $aBlormReblogedPosts[$id]["teaser_url"];
                $getStreamData->TeaserIri = $aBlormReblogedPosts[$id]["teaser_iri"];

                if (isset($bodyObject->object->data->data->publishedOnWebsiteName)) {
                    $getStreamData->OriginWebsiteName = $bodyObject->object->data->data->publishedOnWebsiteName;
                }
                if (isset($bodyObject->object->data->data->publishedOnWebsiteUrl)) {
                    $getStreamData->OriginWebsiteUrl = $bodyObject->object->data->data->publishedOnWebsiteUrl;
                }
                $getStreamData->ReblogedCount = 0;
                $getStreamData->CommentsCount = 0;
                $getStreamData->SharedCount = 0;

                if (isset($bodyObject->reaction_counts->reblog)) {
                    $getStreamData->ReblogedCount = $bodyObject->reaction_counts->reblog;
                }

                if (isset($bodyObject->latest_reactions->reblog)) {
                    $getStreamData->Rebloged = $bodyObject->latest_reactions->reblog;
                }

                if (isset($bodyObject->reaction_counts->comment)) {
                    $getStreamData->CommentsCount = $bodyObject->reaction_counts->comment;
                }

                if (isset($bodyObject->latest_reactions->comment)) {
                    $getStreamData->Comments = $bodyObject->latest_reactions->comment;
                }

                if (isset($bodyObject->reaction_counts->share)) {
                    $getStreamData->SharedCount = $bodyObject->reaction_counts->share;
                }

                if (isset($bodyObject->latest_reactions->share)) {
                    $getStreamData->Shared = $bodyObject->latest_reactions->share;
                }

                $aGetStreamReblogedData[$getStreamData->PostId] = $getStreamData;
            }
        }
    }

    $blormPostConfig = new stdClass();
    $blormPostConfig->blormAssets = plugins_url()."/blorm/assets/";

    $options = get_option( 'blorm_plugin_options_frontend' );

    $blormPostConfig->float = "left";
    if (isset( $options['position_widget_menue_adjust_float'] )) {
        $blormPostConfig->float = $options['position_widget_menue_adjust_float'];
    }

    $blormPostConfig->classForWidgetPlacement = "";
    if (isset( $options['position_widget_menue_adjust_classForWidgetPlacement'] )) {
        $blormPostConfig->classForWidgetPlacement = $options['position_widget_menue_adjust_classForWidgetPlacement'];
    }

    $blormPostConfig->positionTop = 0;
    if (isset( $options['position_widget_menue_adjust_positionTop'] )) {
        $blormPostConfig->positionTop = $options['position_widget_menue_adjust_positionTop'];
    }

    $blormPostConfig->positionRight = 0;
    if (isset( $options['position_widget_menue_adjust_positionRight'] )) {
        $blormPostConfig->positionRight = $options['position_widget_menue_adjust_positionRight'];
    }

    $blormPostConfig->positionBottom = 0;
    if (isset( $options['position_widget_menue_adjust_positionBottom'] )) {
        $blormPostConfig->positionBottom = $options['position_widget_menue_adjust_positionBottom'];
    }

    $blormPostConfig->positionLeft = 0;
    if (isset( $options['position_widget_menue_adjust_positionLeft'] )) {
        $blormPostConfig->positionLeft = $options['position_widget_menue_adjust_positionLeft'];
    }

    $blormPostConfig->positionUnit = "px";
    if (isset( $options['position_widget_menue_adjust_positionUnit'] )) {
        $blormPostConfig->positionUnit = $options['position_widget_menue_adjust_positionUnit'];
    }


    echo "<script type=\"text/javascript\">\n\n";
    echo "var blormapp = {
			postConfig: ".json_encode($blormPostConfig, JSON_PRETTY_PRINT).",\n
            blormPosts: ".json_encode($aGetStreamCreatedData, JSON_PRETTY_PRINT).",\n
            reblogedPosts: ".json_encode($aGetStreamReblogedData, JSON_PRETTY_PRINT)."\n";
    echo "}\n</script>";
}