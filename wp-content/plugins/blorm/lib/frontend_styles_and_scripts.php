<?php

/*
 * setup js and css for the frontend rendering
 *
 */
/*
function getBlormFrontendConfigJs() {
    $jsdata =  "var blormAssets = '".plugins_url()."/blorm/assets/';\n";
    return $jsdata;
}*/


// Enqueue Stylesheet and Js for frontend rendering.

add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_theme_style');
function enqueue_blorm_frontend_theme_style() {

    if (!is_admin()) {
        wp_enqueue_style ('blorm-theme-style', plugins_url('blorm/assets/css/blorm_frontend.css'));
        //wp_enqueue_style( 'jBoxcss', 'https://cdn.jsdelivr.net/gh/StephanWagner/jBox@v1.3.3/dist/jBox.all.min.css');
    }
}

add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_js');
function enqueue_blorm_frontend_js() {

    if (is_admin()) {
      return;
    }
    //wp_enqueue_script( 'jBoxjs',  'https://cdn.jsdelivr.net/gh/StephanWagner/jBox@v1.3.3/dist/jBox.all.min.js');
    wp_enqueue_script( 'blorm-mobile-detect', plugins_url( 'blorm/assets/js/mobile-detect.min.js' ) );
    wp_enqueue_script( 'blorm-widget', plugins_url( 'blorm/assets/js/blorm_widget_menue.js' ) );
    wp_enqueue_script( 'blorm-widget-builder', plugins_url( 'blorm/assets/js/blormWidgetBuilder.js' ) );

    $frontend_options_config = get_option( 'blorm_plugin_options_frontend' );
    if (isset($frontend_options_config['position_widget_menue'])) {
        if ( $frontend_options_config['position_widget_menue'] === 'add_blorm_info_on_image' ) {
            wp_enqueue_script( 'blorm-widget-on-image', plugins_url('blorm/assets/js/blorm_init_widget_on_image.js'));
        }

        if ( $frontend_options_config['position_widget_menue'] === 'add_blorm_info_before_content' ||
            $frontend_options_config['position_widget_menue'] === 'add_blorm_info_after_content' ||
            $frontend_options_config['position_widget_menue'] === 'add_blorm_info_before_title' ||
            $frontend_options_config['position_widget_menue'] === 'add_blorm_info_after_title'
        ) {
            wp_enqueue_script( 'blorm-widget-to-content', plugins_url('blorm/assets/js/blorm_init_widget_to_content.js'));
        }

        if ( $frontend_options_config['position_widget_menue'] === 'add_blorm_info_on_theme_tag' ) {
            wp_enqueue_script( 'blorm-widget-on-theme', plugins_url('blorm/assets/js/blorm_init_widget_on_theme_tag.js'));
        }

        /*if ( $frontend_options_config['position_widget_menue'] === 'add_blorm_info_on_special_class' ) {
            wp_enqueue_script( 'blorm-widget-on-image', plugins_url('blorm/assets/js/blorm_web_widget_on_special_class.js'));
        }*/
    }
}

add_action( 'wp_head', 'add_getstream_data_to_head');
function add_getstream_data_to_head() {

    echo "<!-- begin rendering blorm data reblogs and shares -->\n";

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

    if ($bodyObjects == null) {
        blorm_cron_getstream_user_public_exec();
        $bodyObjects = json_decode(get_option( 'blorm_getstream_cached_post_data' ));
    }

    if ($bodyObjects == null) {
        echo "\n<script type=\"text/javascript\">\n";
        echo "console.log('BLORM ERROR: could not load blorm frontend data from cache');\n";
        echo "var blormapp = {postConfig: {},\n blormPosts: {}\n, reblogedPosts: {}\n}\n";
        echo "</script>\n";
        echo "<!-- end rendering blorm data reblogs and shares -->\n";
        return;
    }
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
                $getStreamData->PostType = "blormpost";
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
                $getStreamData->PostType = "blormreblog";
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

    $blormPostConfig->specialCssClassForPost = "";
    if (isset( $options['special_css_class_for_post'] )) {
        $blormPostConfig->specialCssClassForPost = $options['special_css_class_for_post'];
    }

    $blormPostConfig->specialCssClassForPostImg = "";
    if (isset( $options['special_css_class_for_post_img'] )) {
        $blormPostConfig->specialCssClassForPostImg = $options['special_css_class_for_post_img'];
    }

    echo '<script type="text/javascript">
            var blormapp = {
			postConfig: '.json_encode($blormPostConfig, JSON_UNESCAPED_UNICODE).',
            blormPosts: '.json_encode($aGetStreamCreatedData, JSON_UNESCAPED_UNICODE).',
            reblogedPosts: '.json_encode($aGetStreamReblogedData, JSON_UNESCAPED_UNICODE).',
            getPostById: function(id) {
                    let post = {};
                    if (typeof blormapp.reblogedPosts[id] != "undefined") {
                        post = blormapp.reblogedPosts[id];
                    }
                    if (typeof blormapp.blormPosts[id] != "undefined") {
                        post = blormapp.blormPosts[id];
                    }
                    return post;
                },
            getAllBlormPosts: function() {
                    let allTypeBlormPosts = [];
                    allTypeBlormPosts = Array.from(document.getElementsByClassName("type-blormpost"));
                    let allBlormSharedPosts = [];
                    allBlormSharedPosts = Array.from(document.getElementsByClassName("blorm-shared"));
                    let specialCssClassPosts = [];
                    if (blormapp.postConfig.specialCssClassForPost !== "") {
                        specialCssClassPosts = Array.from(document.getElementsByClassName(this.postConfig.specialCssClassForPost));
                    }
                    return allTypeBlormPosts.concat(allBlormSharedPosts, specialCssClassPosts);
                }
           }</script>';
    echo "<!-- end rendering blorm data reblogs and shares -->\n";
}