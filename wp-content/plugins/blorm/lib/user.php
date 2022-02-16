<?php

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