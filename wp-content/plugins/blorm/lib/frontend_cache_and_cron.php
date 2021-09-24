<?php

/*
 *
 * adding cron schedule for loading the getstream data
 * the getstream data is loaded every 180 seconds to cache the data
 * and prevent the blorm-api from high traffic webpages
 *
*/
add_filter( 'cron_schedules', 'blorm_add_cron_getstream_interval' );
function blorm_add_cron_getstream_interval( $schedules ) {

    $update_intervall = 1;
    $config = get_blorm_config();

    if (isset($config['update_intervall']) ) {
        $update_intervall = $config['update_intervall'];
    }

    $schedules['onehundredeighty_seconds'] = array(
        'interval' => $update_intervall,
        'display'  => esc_html__( 'Every sixty Seconds' ), );
    return $schedules;
}

add_action( 'blorm_cron_getstream_hook', 'blorm_cron_getstream_exec' );
if ( ! wp_next_scheduled( 'blorm_cron_getstream_hook' ) ) {
    wp_schedule_event( time(), 'onehundredeighty_seconds', 'blorm_cron_getstream_hook' );
}

function blorm_cron_getstream_exec() {

    $getstreamPostObjects = "{}";

    $args = array(
        'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
        'method' => 'GET',
        'body' => '',
        'data_format' => 'body',
        'timeout' => 5,
        'sslverify' => true,
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

