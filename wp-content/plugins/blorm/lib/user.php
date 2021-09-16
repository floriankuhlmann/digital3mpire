<?php

/*
 *
 * adding cron schedule for loading the getstream user data
 * the getstream data is loaded every 180 seconds to cache the data
 * and prevent the blorm-api from high traffic webpages
 *
*/
add_filter( 'cron_schedules', 'blorm_add_cron_getstream_user_interval' );
function blorm_add_cron_getstream_user_interval( $schedules ) {

    $update_intervall = 180;
    $config = get_blorm_config();

    if (isset($config['update_intervall']) ) {
        $update_intervall = $config['update_intervall'];
    }

    $schedules['onehundredeighty_seconds'] = array(
        'interval' => $update_intervall,
        'display'  => esc_html__( 'Every sixty Seconds' ), );
    return $schedules;
}

add_action( 'blorm_cron_getstream_user_hook', 'blorm_cron_getstream_user_exec' );

if ( ! wp_next_scheduled( 'blorm_cron_getstream_user_hook' ) ) {
    wp_schedule_event( time(), 'onehundredeighty_seconds', 'blorm_cron_getstream_user_hook' );
}

function blorm_cron_getstream_user_exec() {

    $returnObj = new stdClass();
    $returnObj->error = null;
    $returnObj->user = null;

    $getstreamPostObjects = "{}";

    $args = array(
        'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
        'method' => 'GET',
        'body' => '',
        'data_format' => 'body',
    );
    // @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'.
    $ApiResponse = wp_remote_request(CONFIG_BLORM_APIURL ."/account/data", $args);

    if (is_a($ApiResponse,"WP_ERROR")) {

        if (isset($ApiResponse->errors['http_request_failed'])) {
            error_log($ApiResponse->errors['http_request_failed'][0]);
        }
        return;
    }

    if ($ApiResponse["response"]["message"] == "Unauthorized" ) {
        error_log($ApiResponse["response"]["message"]);
    }

    update_option( 'blorm_getstream_cached_user_data', $ApiResponse["body"], true );

}

function getUserAccountDataFromBlorm() {

    $returnObj = new stdClass();
    $returnObj->error = null;
    $returnObj->user = null;

    // prepare the request
    $userObjects = json_decode(get_option( 'blorm_getstream_cached_user_data' ));

    if ($userObjects == null) {
        blorm_cron_getstream_user_exec();
        $userObjects = json_decode(get_option( 'blorm_getstream_cached_user_data' ));
    }

    if ($userObjects == null) {
        $returnObj->error = "no userdata available";
        return $returnObj;
    }

    if (is_string($userObjects)) {
        if ($userObjects ==  "Token not valid" ) {
            $returnObj->error = "API token is not valid";
            return $returnObj;
        }
    }

    $user = new stdClass();
    $user->name = $userObjects->name;
    $user->blormhandle = $userObjects->blormhandle;
    $user->id = $userObjects->id;
    $user->photo_url = $userObjects->photo_url;
    $user->website_id = $userObjects->website_id;
    $user->website_name = $userObjects->website_name;
    $user->website_href = $userObjects->website_href;
    $user->website_category = $userObjects->website_category;
    $user->website_type = $userObjects->website_type;

    $returnObj->user = $user;

    return $returnObj;

}