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
    $user->blormHandle = $userObjects->blormhandle;
    $user->id = $userObjects->id;
    $user->photoUrl = $userObjects->photo_url;
    $user->websiteId = $userObjects->website_id;
    $user->websiteName = $userObjects->website_name;
    $user->websiteUrl = $userObjects->website_href;
    $user->category = $userObjects->website_category;
    $user->websiteType = $userObjects->website_type;

    $returnObj->user = $user;

    return $returnObj;

}