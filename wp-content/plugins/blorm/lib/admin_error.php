<?php

// remove dashboard widgets
add_action( 'admin_init', 'prepare_dashboard_meta');


function prepare_dashboard_meta() {
    remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_browser_nag','dashboard','normal');
    remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
    remove_action('welcome_panel', 'wp_welcome_panel');
    add_meta_box( 'id1', 'BLORM - Error', 'dashboard_widget_blorm_init_error', 'dashboard', 'normal', 'high' );
}

function dashboard_widget_blorm_init_error() {
    ?>
    <div id="Blorm_usermodule" class="BlormUserModule">
        <p>The BLORM Plugin is activated but there was an error initiating the plugin and connecting to the BLORM API.</p>
        <p>Did you register an API-key and saved it under 'Settings/Blorm Settings' of this plugin?</p>
    </div>
    <?php
}