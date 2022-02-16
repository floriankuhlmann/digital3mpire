<?php

function route_handler($request)
{
    $custom_query = new WP_Query();
//Insert queries for more specific information
//Modify or simplify the query results
//Here I'm just returning the query results.
return json_encode($custom_query);
}
add_action('rest_api_init', function () {
register_rest_route('custom-theme/v1', '/getWPPost', array(
'methods' => 'POST',
'callback' => 'route_handler',
    'permission_callback' => '__return_true',

));
});