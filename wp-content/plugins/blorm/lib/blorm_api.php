    <?php

// add ajax methods

add_action( 'rest_api_init','rest_blorm_api_endpoint' );

function rest_blorm_api_endpoint() {

    // http://blog1.blorm/wp-json/blormapi/v1/

    // Register the GET route
    register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
        'methods' => 'GET',
        'callback' =>'rest_blormapi_handler',
        'permission_callback' => '__return_true',
    ));

    // Register the POST route
    register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
        'methods' => 'POST',
        'callback' =>'rest_blormapi_handler',
        'permission_callback' => '__return_true',
    ));

    // Register the PUT route
    register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
        'methods' => 'PUT',
        'callback' =>'rest_blormapi_handler',
        'permission_callback' => '__return_true',
    ));

    // Register the DELETE route
    register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
        'methods' => 'DELETE',
        'callback' =>'rest_blormapi_handler',
        'permission_callback' => '__return_true',
    ));

}

function rest_blormapi_handler(WP_REST_Request $request) {

    add_filter('https_ssl_verify', '__return_false');
    add_filter('https_local_ssl_verify', '__return_false');

    if ( !is_user_logged_in() ) {
        return new WP_REST_Response(array("message" =>"user not logged in"),200 ,array('Content-Type' => 'application/json'));
    }

    if(!empty($_FILES['uploadfile'])) {

        if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );

        $uploadFile = $_FILES['uploadfile'];
        $upload_overrides = array( 'test_form' => false );
        $movedFile = wp_handle_upload( $uploadFile, $upload_overrides );

        if ( $movedFile && !isset( $movedFile['error'] ) ) {
            return new WP_REST_Response(array("message" => "success", "url" => $movedFile['url']),200 ,array('Content-Type' => 'application/json'));

        }
        //error_log("movefile: ".$movedFile['url']);
        return new WP_REST_Response(array("message" => "upload_error"),200 ,array('Content-Type' => 'application/json'));
    }

    // check before wp_remote_request (create, reblog)
    $returnObj = preRequestLocalPostsUpdate($request);
    if (  $returnObj->status == "cached" ) {
        return new WP_REST_Response(json_decode($returnObj->data),200 ,array('Content-Type' => 'application/json'));
    }

    // prepare the request
    $args = array(
        'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
        'method' => $request->get_method(),
        'body' => $request->get_body(),
        'data_format' => 'body',
    );
    $params = $request->get_params();
    $requestURL = CONFIG_BLORM_APIURL ."/". $params['restparameter'];
    if ($returnObj->query !== null) $requestURL .= $returnObj->query;

    error_log("requestURL".$requestURL);
    $response = wp_remote_request($requestURL, $args);

    // check after wp_remote_request (delete)
    postRequestLocalPostsUpdate($request,$response);

    return new WP_REST_Response(json_decode(wp_remote_retrieve_body($response)),200 ,array('Content-Type' => 'application/json'));

}

function preRequestLocalPostsUpdate(&$request) {

    $parameter = $request->get_params();
    $body = $request->get_body();

    $returnObj = new stdClass();
    $returnObj->status = "continue";
    $returnObj->data = null;
    $returnObj->query = null;

    switch($parameter["restparameter"]) {
	    case (preg_match('/^(feed\/timeline)\/?$/', $parameter["restparameter"]) ? true : false) :
            error_log("feed\/timeline: ".$parameter["restparameter"]);

            $query = "";
            if (isset($parameter["limit"])) {
                error_log("timeline limit");
                $query = "limit=" . $parameter["limit"];
            }

            if (isset($parameter["offset"])) {
                error_log("timeline offset");
                $query .= "&offset=" . $parameter["offset"];
            }

            if (isset($parameter["offset"]) || isset($parameter["limit"])) {
                $returnObj->query = "?".$query;
            }
            return $returnObj;
            break;

        //READ
        case (preg_match('/^(user\/data)\/?$/', $parameter["restparameter"]) ? true : false) :

            //error_log($parameter["restparameter"]);
            //error_log(json_encode("hier user/data"));

            break;
        case (preg_match('/^(feed\/followers\/user)\/[0-9]+$/', $parameter["restparameter"]) ? true : false) :
            // can we load from cache?
            $parameter = explode('/', $parameter["restparameter"]);
            $userparameter = end($parameter);

            $blormUserAccountData = getUserAccountDataFromBlorm();
            if ($blormUserAccountData->error !== null) {
                error_log("error blorm_cron_getstream_update_followers_exec: account user data missing in cache");
                return $returnObj;
            }

            if ($blormUserAccountData->user->id != $userparameter) {
                return $returnObj;
            }

            $returnObj->status = "cached";
            $returnObj->data = get_option( 'blorm_getstream_cached_followers_data' );

            return $returnObj;

            break;
        case (preg_match('/^(feed\/following\/timeline)\/[0-9]+$/', $parameter["restparameter"]) ? true : false) :
            // can we load from cache?

            $parameter = explode('/', $parameter["restparameter"]);
            $userparameter = end($parameter);

            $blormUserAccountData = getUserAccountDataFromBlorm();
            if ($blormUserAccountData->error !== null) {
                error_log("error blorm_cron_getstream_update_followers_exec: account user data missing in cache");
                return $returnObj;
            }

            if ($blormUserAccountData->user->id != $userparameter) {
                return $returnObj;
            }

            $returnObj->status = "cached";
            $returnObj->data = get_option( 'blorm_getstream_cached_following_users_data' );

            return $returnObj;

            break;
        // CREATE
        case (preg_match('/^(blogpost\/create)\/?$/', $parameter["restparameter"]) ? true : false) :

            // we need to modify the body, add the irl to the json-object in the body
            $bodyObj = json_decode($body);

            $bodyObj->teaser->url = get_permalink($bodyObj->teaser->postid);
            $request->set_body(json_encode($bodyObj));

            break;

        // UNDO REBLOG
        case (preg_match('/^(blogpost\/undo\/reblog)\/[a-z0-9-]+$/', $parameter["restparameter"]) ? true : false) :

            $parameter = explode('/', $parameter["restparameter"]);

            $delparameter = end($parameter);

            $args = array('post_type' => 'blormpost', 'meta_key' => 'blorm_reblog_activity_id', 'meta_value' => $delparameter);
            $the_query = get_posts( $args );

            if (isset($the_query[0])) {
                delete_post_meta($the_query[0]->ID,"blorm_reblog_teaser_image");
                delete_post_meta($the_query[0]->ID,"blorm_reblog_teaser_url");
                delete_post_meta($the_query[0]->ID,"blorm_reblog_object_iri");
                delete_post_meta($the_query[0]->ID,"blorm_reblog_activity_id");

                // delete the attached thumbnail in post meta
                $thumbId = get_post_thumbnail_id($the_query[0]->ID);
                delete_post_meta($thumbId, "_wp_attached_file");
                delete_post_meta($thumbId, "_wp_attachment_metadata");
                delete_post_meta($the_query[0]->ID, "_thumbnail_id");

                // delete the thumbnail in post
                wp_delete_post($thumbId);

                // delete the file in media
                wp_delete_attachment($the_query[0]->ID, true);

                // finaly delete the post
                wp_delete_post($the_query[0]->ID);
            } else {
                $status = "blorm_reblog_activity_id_is_empty";
            }

            break;
    }

    return $returnObj;
}


function postRequestLocalPostsUpdate($request, $response) {

    $parameter = $request->get_params();
    $body = $request->get_body();

    error_log("postRequestLocalPostsUpdate restparameter: ".$parameter["restparameter"]);

    switch($parameter["restparameter"]) {
	    case (preg_match('/^(feed\/timeline)\/?$/', $parameter["restparameter"]) ? true : false) :

		    break;

	    // CREATE
        case (preg_match('/^(blogpost\/create)\/?$/', $parameter["restparameter"]) ? true : false) :

            $bodyObj = json_decode($body);

            if ($response["response"]["code"] == "200") {
                // we want to save the state of the post tp prevent reposting it later again (the content-object in getstream-database is unique)
                add_post_meta($bodyObj->{'teaser'}->{'postid'},"blorm_create",true);

                $rBody = json_decode($response["body"]);
                add_post_meta($bodyObj->{'teaser'}->{'postid'},"blorm_create_activity_id",$rBody->data->activity_id);
            }
            blorm_cron_getstream_user_public_exec();
            break;

        // REBLOG
        case (preg_match('/^(blogpost\/reblog)\/?$/', $parameter["restparameter"]) ? true : false) :
            error_log("postRequestLocalPostsUpdate restparameter REBLOG");

            if (!is_a($response, 'WP_Error' )){

                $requestBodyObj = json_decode($body);
                $responseBodyObj = json_decode($response["body"]);

                /*$content = "<span data-blorm-id=\"".$responseBodyObj->{'data'}->{'activity_id'}."\"><a href=\"".$requestBodyObj->{'origin_post_data'}->{'url'}."\">
                            ".$requestBodyObj->{'origin_post_data'}->{'text'}."</a></span>";*/

                // save custom post
                $post_id = wp_insert_post(array(
                    "post_title" => "<span class=\'blorm_reblog\'>" . $requestBodyObj->{'origin_post_data'}->{'headline'} . "</span>",
                    "post_content" => $requestBodyObj->{'origin_post_data'}->{'text'},
                    "post_status" => "publish",
                    //"post_category" => array("Blorm"),
                    "post_type" => "blormpost"
                ));

                $options = get_option( 'blorm_plugin_options_category' );
                if (isset( $options['blorm_category_show_reblogged'] )) {
                    wp_set_post_categories( $post_id, array( $options['blorm_category_show_reblogged']), true );
                }

                add_post_meta($post_id, "blorm_reblog_teaser_image", $requestBodyObj->{'origin_post_data'}->{'image'});
                add_post_meta($post_id, "blorm_reblog_teaser_url", $requestBodyObj->{'origin_post_data'}->{'url'});
                add_post_meta($post_id, "blorm_reblog_object_iri", $requestBodyObj->{'origin_post'}->{'object_iri'});
                add_post_meta($post_id, "blorm_reblog_activity_id", $responseBodyObj->{'data'}->{'user_activity_id'});

                // prepare the title for the file
                $filetitle = sanitize_title(
                    $requestBodyObj->{'origin_post_data'}->{'headline'},
                    $fallback_title = 'blorm-post-thumbnail-image',
                    $context = 'save' );

                // load the file from the origin source
                $dImage = file_get_contents( $requestBodyObj->{'origin_post_data'}->{'image'});
                $ext = pathinfo($requestBodyObj->{'origin_post_data'}->{'image'}, PATHINFO_EXTENSION);

                // final filename
                $filename = $filetitle.".".$ext;

                // upload it into wordpress
                $upload_file = wp_upload_bits(
                    $filename,
                    null,
                    $dImage);


                if ( ! $upload_file['error'] ) {
                    // if succesfull insert the new file into the media library (create a new attachment post type).
                    $wp_filetype = wp_check_filetype($filename, null );

                    $attachment = array(
                      'post_mime_type' => $wp_filetype['type'],
                      'post_parent'    => $post_id,
                      'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
                      'post_content'   => '',
                      'post_status'    => 'inherit'
                    );

                    $attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_id );

                    if ( ! is_wp_error( $attachment_id ) ) {
                      // if attachment post was successfully created, insert it as a thumbnail to the post $post_id.
                      require_once(ABSPATH . "wp-admin" . '/includes/image.php');

                      $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );

                      wp_update_attachment_metadata( $attachment_id,  $attachment_data );
                      set_post_thumbnail( $post_id, $attachment_id );
                    }
                }
                blorm_cron_getstream_user_public_exec();
            }
            break;

        // SHARE
        case (preg_match('/^(blogpost\/share)\/?$/', $parameter["restparameter"]) ? true : false) :

            blorm_cron_getstream_user_public_exec();
            break;

        // DELETE
        case (preg_match('/^(blogpost\/delete\/)[a-z0-9-]+$/', $parameter["restparameter"]) ? true : false) :

        	$delparameter = explode('/', $parameter["restparameter"]);

            if (!is_a($response, 'WP_Error' )) {
                $recent_posts_with_meta = wp_get_recent_posts(array('meta_key' => 'blorm_create_activity_id', 'meta_value' => end($delparameter)));
	            if (isset($recent_posts_with_meta[0])) {
		            delete_post_meta($recent_posts_with_meta[0]["ID"],"blorm_create_activity_id");
		            delete_post_meta($recent_posts_with_meta[0]["ID"],"blorm_create");
	            }
                blorm_cron_getstream_user_public_exec();
            }

            break;

        // CREATE
        case (preg_match('/^(user\/follow\/blormhandle\/)[a-z0-9-]+$/', $parameter["restparameter"]) ? true : false) :
            if ($response["response"]["code"] == "200") {
                blorm_cron_getstream_update_following_users_exec();
            }
            break;

        case (preg_match('/^(user\/unfollow\/blormhandle\/)[a-z0-9-]+$/', $parameter["restparameter"]) ? true : false) :

            if ($response["response"]["code"] == "200") {
                blorm_cron_getstream_update_following_users_exec();
            }
            break;

        default:
            //error_log("localFeeedUpdate restparameter: no matches");
    }
}