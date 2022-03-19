<?php

// Enqueue Stylesheet and Js for admin area.
add_action( 'admin_enqueue_scripts', 'enqueue_blorm_admin_theme_style');

/**
* Enqueue Stylesheet
*
* @return void
*/
function enqueue_blorm_admin_theme_style() {

    wp_register_style('blorm-admin-theme-style', plugins_url('../assets/css/blorm.css', __FILE__),false, '1.0.0' );
    /* CSS */
    wp_enqueue_style('blorm-admin-theme-style');

    /* JS */
    global $pagenow;
    if (is_admin() && $pagenow == 'index.php') {


        wp_enqueue_script('blorm-admin-theme-app', plugins_url('../assets/js/blormAdminSocialApp.js', __FILE__), '','',true);

        /* Wordpress API backbone.js */
        wp_enqueue_script('wp-api');

        // Register custom variables for the AJAX script.
        wp_localize_script( 'blorm-admin-theme-app', 'restapiVars', [
            'root'  => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
        ]);

        wp_add_inline_script('blorm-admin-theme-app', getConfigJs() ,'before');
    }
}

/**
 * @return string
 *
 * generating javascript data for the vue.js app in the admin area
 *
 */

function getConfigJs() {

    global $blormUserAccountData;

    $jsdata =   "var blogurl = '".CONFIG_BLORM_BLOGURL."';\n";
    $jsdata .=  "var blogdomain = '".CONFIG_BLORM_BLOGDOMAIN."';\n";
    $jsdata .=  "var ajaxapi = blogdomain+ajaxurl;\n";
    $jsdata .=  "var pluginUrl = '".plugins_url()."';\n";
    $jsdata .=  "var blormPluginUrl = '".plugins_url()."/blorm';\n";
    // user data fallback definiton
    // account: is the logged in blorm-account connected to the api via the key
    // user: is the currenty active user (showing feed, followers, etc)
    $userdata =  "var blormapp = {
                    account : {
                        \"name\": \"*\",
                        \"blormhandle\": \"*\",
                        \"id\": \"*\",
                        \"photo_url\": \"*\",
                        \"website_name\": \"*\",
                        \"website_href\": \"*\",
                        \"website_category\": \"*\",
                        \"website_type\": \"*\",
                        \"website_id\": \"*\",
                    }
                };\n";

    if ($blormUserAccountData->error != null) {
        $jsdata .= $userdata;
        return $jsdata;
    }

    // user data setup
    $userdata =  "var blormapp = {
                    account : {
                        \"name\": \"".$blormUserAccountData->user->name."\",
                        \"blormHandle\": \"".$blormUserAccountData->user->blormHandle."\",
                        \"id\": \"".$blormUserAccountData->user->id."\",
                        \"photoUrl\": \"".$blormUserAccountData->user->photoUrl."\",
                        \"websiteName\": \"".$blormUserAccountData->user->websiteName."\",
                        \"websiteUrl\": \"".$blormUserAccountData->user->websiteUrl."\",
                        \"category\": \"".$blormUserAccountData->user->category."\",
                        \"websiteType\": \"".$blormUserAccountData->user->websiteType."\",
                        \"websiteId\": \"".$blormUserAccountData->user->websiteId."\",
                    },
                    recentPosts: [\n";

    // a list of the available posts for sharing in the network (used by the newpost-component
    $recent_posts = wp_get_recent_posts();
    foreach ($recent_posts as $recent_post) {

		// TODO try to json_encode the strings
        $teasertext = str_replace("\n","",filter_var(get_the_excerpt($recent_post['ID']), FILTER_SANITIZE_STRING));
        $teasertext = str_replace("\"","'",$teasertext);

        $posttitle = str_replace("\n","",filter_var($recent_post["post_title"], FILTER_SANITIZE_STRING));
        $posttitle = str_replace("\"","'",$posttitle);
        $userdata .= "{ id:\"".$recent_post["ID"]."\",
                        headline:\"".$posttitle."\",
                        teasertext:\"".$teasertext."\"
                        },\n";
    }
    $userdata .=  "]};\n";

    $jsdata .= $userdata;

    return $jsdata;
}

?>