<?php

/*
 *
 * adding cron schedule for loading the getstream data
 * the getstream data is loaded every 180 seconds to cache the data
 * and prevent the blorm-api from high traffic webpages
 *
*/
add_filter( 'cron_schedules', 'blorm_add_cron_intervals' );
function blorm_add_cron_intervals( $schedules ) {

    $config = get_blorm_config();

    $update_intervall_60 = 60;
    $update_intervall_180 = 180;

    if (isset($config['update_intervall_180']) ) $update_intervall_180 = $config['update_intervall_180'];
    if (isset($config['update_intervall_60']) ) $update_intervall_60 = $config['update_intervall_60'];


    $schedules['sixty_seconds'] = array(
        'interval' => $update_intervall_60,
        'display'  => esc_html__( 'Every 60 Seconds' ), );

    $schedules['onehundredeighty_seconds'] = array(
        'interval' => $update_intervall_180,
        'display'  => esc_html__( 'Every 180 Seconds' ), );

    return $schedules;
}

/*
 * cache user account
 *
 */

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

    add_filter('https_ssl_verify', '__return_false');
    add_filter('https_local_ssl_verify', '__return_false');

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

/*
 *
 * update user public feed cache
 *
 */

add_action( 'blorm_cron_getstream_hook', 'blorm_cron_getstream_user_public_exec' );
if ( ! wp_next_scheduled( 'blorm_cron_getstream_hook' ) ) {
    wp_schedule_event( time(), 'onehundredeighty_seconds', 'blorm_cron_getstream_hook' );
}

function blorm_cron_getstream_user_public_exec() {

    $getstreamPostObjects = "{}";

    $args = array(
        'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
        'method' => 'GET',
        'body' => '',
        'data_format' => 'body',
    );

    add_filter('https_ssl_verify', '__return_false');
    add_filter('https_local_ssl_verify', '__return_false');

    $response = wp_remote_request(CONFIG_BLORM_APIURL ."/feed/userpublic", $args);

    if( is_wp_error( $response ) ) {
        error_log($response->get_error_message());
        return;
    }

    if( $response['response']['code'] !== 200 ) {
        error_log("error api request ".CONFIG_BLORM_APIURL ."/feed/userpublic : ".$response['response']['code']);
        return;
    }

    if ($response['body'] != "") {
        $getstreamPostObjects = $response['body'];
    }

    update_option( 'blorm_getstream_cached_post_data', $getstreamPostObjects, true );

}

/*
 * uodate followers cache
 *
 */

add_action( 'blorm_cron_getstream_hook_followers', 'blorm_cron_getstream_update_followers_exec' );
if ( ! wp_next_scheduled( 'blorm_cron_getstream_hook_followers' ) ) {
    wp_schedule_event( time(), 'onehundredeighty_seconds', 'blorm_cron_getstream_hook_followers' );
}

function blorm_cron_getstream_update_followers_exec() {

    $getstreamPostObjects = "{}";

    $args = array(
        'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
        'method' => 'GET',
        'body' => '',
        'data_format' => 'body',
    );

    $blormUserAccountData = getUserAccountDataFromBlorm();
    if ($blormUserAccountData->error !== null) {
        error_log("error blorm_cron_getstream_update_followers_exec: account user data missing in cache");
        return;
    }

    add_filter('https_ssl_verify', '__return_false');
    add_filter('https_local_ssl_verify', '__return_false');

    $response = wp_remote_request(CONFIG_BLORM_APIURL ."/feed/followers/user/".$blormUserAccountData->user->id, $args);

    if( is_wp_error( $response ) ) {
        error_log($response->get_error_message());
        return;
    }

    if( $response['response']['code'] !== 200 ) {
        error_log("error api request ".CONFIG_BLORM_APIURL ."/feed/followers/user/".$blormUserAccountData->user->id." : ".$response['response']['code']);
        return;
    }

    if ($response['body'] != "") {
        $getstreamPostObjects = $response['body'];
    }

    update_option( 'blorm_getstream_cached_followers_data', $getstreamPostObjects, true );
}

/*
 * uodate following users cache
 *
 */

add_action( 'blorm_cron_getstream_hook_following_users', 'blorm_cron_getstream_update_following_users_exec' );
if ( ! wp_next_scheduled( 'blorm_cron_getstream_hook_following_users' ) ) {
    wp_schedule_event( time(), 'onehundredeighty_seconds', 'blorm_cron_getstream_hook_following_users' );
}

function blorm_cron_getstream_update_following_users_exec() {

    $getstreamPostObjects = "{}";

    $args = array(
        'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
        'method' => 'GET',
        'body' => '',
        'data_format' => 'body',
    );

    $blormUserAccountData = getUserAccountDataFromBlorm();
    if ($blormUserAccountData->error !== null) {
        error_log("error blorm_cron_getstream_update_followers_exec: account user data missing in cache");
        return;
    }

    add_filter('https_ssl_verify', '__return_false');
    add_filter('https_local_ssl_verify', '__return_false');

    $response = wp_remote_request(CONFIG_BLORM_APIURL ."/feed/following/timeline/".$blormUserAccountData->user->id, $args);

    if( is_wp_error( $response ) ) {
        error_log($response->get_error_message());
        return;
    }

    if( $response['response']['code'] !== 200 ) {
        error_log("error api request ".CONFIG_BLORM_APIURL ."/following/timeline/".$blormUserAccountData->user->id." : ".$response['response']['code']);
        return;
    }

    if ($response['body'] != "") {
        $getstreamPostObjects = $response['body'];
    }

    update_option( 'blorm_getstream_cached_following_users_data', $getstreamPostObjects, true );

}
