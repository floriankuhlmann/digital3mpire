<?php

/*
 *
 * define options and settings
 *
 */

$menuSlug= 'blorm-plugin';

function blorm_options_page() {
	$hookname = add_menu_page(
        'Blorm plugin page',    // title for browser window
        'Blorm Settings',       // menue name
        'manage_options',       //
        'blorm-plugin',
        'blorm_render_options_page' // name of the rendering function
    );
	add_action( 'load-' . $hookname, 'blorm_plugin_options_page_submit' );
}
add_action( 'admin_menu', 'blorm_options_page' );

function blorm_plugin_options_page_submit() {

	$blorm_plugin_options_api = array();
	$blorm_plugin_options_frontend = array();

    // blorm-plugin-reset-cache-and-data-section
    if ( isset( $_POST['_wpnonce'] ) && $_GET['page'] == 'blorm-plugin' && $_POST['option_page'] == 'blorm-plugin-reset-cache-and-data-section') {
        if (isset( $_POST['deletereblog'] )) {
            if ( $_POST['deletereblog'] == true ){
                $blormPosts = get_posts(array('post_type' => 'blormpost'));
                foreach ($blormPosts as $blormPost) {
                    $id = $blormPost->ID;
                    delete_post_meta($id,"blorm_reblog_teaser_image");
                    delete_post_meta($id,"blorm_reblog_teaser_url");
                    delete_post_meta($id,"blorm_reblog_object_iri");
                    delete_post_meta($id,"blorm_reblog_activity_id");
                    // delete the attached thumbnail in post meta
                    $thumbId = get_post_thumbnail_id($id);
                    delete_post_meta($thumbId, "_wp_attached_file");
                    delete_post_meta($thumbId, "_wp_attachment_metadata");
                    delete_post_meta($id, "_thumbnail_id");

                    // delete the thumbnail in post
                    wp_delete_post($thumbId);

                    // delete the file in media
                    wp_delete_attachment($id, true);

                    // finaly delete the post
                    wp_delete_post($id);
                }

                $recent_create_posts_by_meta = wp_get_recent_posts(array('meta_key' => 'blorm_create_activity_id'));
                foreach ($recent_create_posts_by_meta as $post) {
                    delete_post_meta( $post["ID"],"blorm_create_activity_id");
                    delete_post_meta( $post["ID"],"blorm_create");
                }
            }
        }

        if (isset( $_POST['cacheposts'] )) {
            if ( $_POST['cacheposts'] == true ){
                update_option( 'blorm_getstream_cached_post_data', '');
            }
        }

        if (isset( $_POST['cacheuser'] )) {
            if ( $_POST['cacheuser'] == true ){
                update_option( 'blorm_getstream_cached_user_data', '');
                update_option( 'blorm_getstream_cached_following_users_data', '');
                update_option( 'blorm_getstream_cached_followers_data', '');
            }
        }

        if (isset( $_POST['cachereload'] )) {
            if ( $_POST['cachereload'] == true ){
                blorm_cron_getstream_user_exec();
                blorm_cron_getstream_user_public_exec();
                blorm_cron_getstream_update_followers_exec();
                blorm_cron_getstream_update_following_users_exec();
            }
        }
    }

    // blorm-plugin-section
    if ( isset( $_POST['_wpnonce'] ) && $_GET['page'] == 'blorm-plugin' && $_POST['option_page'] == 'blorm-plugin-section') {
		if( wp_verify_nonce( $_POST['_wpnonce'], 'blorm-plugin-section-options' )) {

			$blorm_plugin_options_api['api_key'] = trim($_POST['blorm_plugin_options_api']['api_key']);
			if (preg_match('/^[a-z0-9]{60}$/i', $blorm_plugin_options_api['api_key'])) {
				update_option('blorm_plugin_options_api', $blorm_plugin_options_api);
			}

			if (sizeof($_POST['blorm_plugin_options_frontend']) != 0) {
				update_option('blorm_plugin_options_frontend', $_POST['blorm_plugin_options_frontend']);
            }

			if (sizeof($_POST['blorm_plugin_options_category']) != 0) {
				//update_option('blorm_plugin_options_category', $_POST['blorm_plugin_options_category']);
			}

			// blorm_plugin_options_category[blorm_category_show_reblogged]
			if (sizeof($_POST['blorm_plugin_options_category']) != 0) {

				// get options
				$options = get_option( 'blorm_plugin_options_category' );

				// get categoryname
				if (isset( $options['blorm_category_show_reblogged'] )) {
					$category = get_category( $options['blorm_category_show_reblogged'] );
				}

				$recent_posts_with_meta = wp_get_recent_posts(array('meta_key' => 'blorm_reblog_activity_id', 'post_type' => 'blormpost'));

				if (!empty($category)) {
	                foreach ($recent_posts_with_meta as $post) {

		                // get options
		                $options = get_option( 'blorm_plugin_options_category' );

		                // get categoryname
		                if (isset( $options['blorm_category_show_reblogged'] )) {
			                wp_remove_object_terms( $post['ID'], $category->cat_name, 'category' );
		                }
	                }
                }

				if ($_POST['blorm_plugin_options_category']['blorm_category_show_reblogged'] != 'no-category-selected') {

                    foreach ($recent_posts_with_meta as $post) {
	                    //error_log(json_encode($post));
	                    wp_set_post_categories( $post['ID'], array( $_POST['blorm_plugin_options_category']['blorm_category_show_reblogged']), true );
                    }
				}

                // update the user and frontend cache
                /*blorm_cron_getstream_user_exec();
                blorm_cron_getstream_user_public_exec();
                blorm_cron_getstream_update_followers_exec();
                blorm_cron_getstream_update_following_users_exec();*/
				update_option('blorm_plugin_options_category', $_POST['blorm_plugin_options_category']);
			}
		}
	}
}


function blorm_render_options_page() {
    ?>
    <h2>Blorm Plugin Settings</h2>
    <form action="<?php menu_page_url( 'blorm-plugin' ) ?>" method="post">
        <?php
        settings_fields( 'blorm-plugin-section' );
        echo "<hr>";
        do_settings_sections( 'blorm-plugin-api-section' );
        echo "<hr>";
        do_settings_sections( 'blorm-plugin-display-config-section' );
        echo "<hr>";
        do_settings_sections( 'blorm-plugin-category-section' );
        echo "<hr>";
        do_settings_sections( 'blorm-plugin-frontend-section' );?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'SAVE' ); ?>" />
    </form>
    <br><br>
    <form action="<?php menu_page_url( 'blorm-plugin' ) ?>" method="post">
         <?php
        settings_fields( 'blorm-plugin-reset-cache-and-data-section' );
         echo "<hr>";
         do_settings_sections( 'blorm-plugin-reset-cache-and-data-section' );?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'RESET & DELETE' ); ?>" />
    </form>
    <?php
}

/*
 * https://developer.wordpress.org/reference/functions/add_settings_field/
 */

/*
 *  api config
 */
add_action( 'admin_init', 'blorm_reset_cache_and_data_section' );
function blorm_reset_cache_and_data_section() {

    // api key
    add_settings_section(
        'blorm-plugin-reset-cache-and-data-section',
        'Reset cache and delete all Blorm data',
        'blorm_plugin_reset_cache_and_data_section_text',
        'blorm-plugin-reset-cache-and-data-section' );

    add_settings_field(
        'blorm_plugin_reset',
        'Reset and delete:',
        'blorm_plugin_reset',
        'blorm-plugin-reset-cache-and-data-section',
        'blorm-plugin-reset-cache-and-data-section' );

}

function blorm_plugin_reset_cache_and_data_section_text() {
    echo "If you click here you can delete all blorm posts, cache and userdata on your local system.
            <br><b>Please rethink twice before you click.</b>";
}

function blorm_plugin_reset() {

    ?>
    <input type="checkbox" id="deletereblog" name="deletereblog" value="true">
    <label for="deletereblog"> Delete the rebloged posts</label><br>
    <input type="checkbox" id="cacheposts" name="cacheposts" value="true">
    <label for="cacheposts">Reset the posts data cache</label><br>
    <input type="checkbox" id="cacheuser" name="cacheuser" value="true">
    <label for="cacheuser">Reset the user data cache</label><br>
    <input type="checkbox" id="cachereload" name="cachereload" value="true">
    <label for="cacheuser">Reload cache data</label><br>
    <?php

}

/*
 *  api config
 */
add_action( 'admin_init', 'blorm_register_settings_api' );
function blorm_register_settings_api() {

    // api key
    add_settings_section(
            'blorm-plugin-api-section',
            'API Settings',
            'blorm_plugin_api_section_text',
            'blorm-plugin-api-section' );

    add_settings_field(
            'blorm_plugin_setting_api_key',
            'API Key',
            'blorm_plugin_setting_api_key',
            'blorm-plugin-api-section',
            'blorm-plugin-api-section' );

    add_option("blorm_plugin_options_api", array(), "", "yes");
}

function blorm_plugin_frontend_section_text() {
    echo '<p>Here you can set all the options for using the Plugin on the web</p>';

}

function blorm_plugin_setting_api_key() {
    $options = get_option( 'blorm_plugin_options_api' );

    $value = "";
    if (isset( $options['api_key'] )) {
        $value = $options['api_key'];
    }
    echo "<input id='blorm_plugin_options_api_key' name='blorm_plugin_options_api[api_key]' type='password' size='60' value='".esc_attr( $value )."' />";
}


/*
 *  api config
 */

add_action( 'admin_init', 'blorm_register_settings_display_config' );
function blorm_register_settings_display_config() {

    add_settings_section(
        'blorm-plugin-display-config-section',
        'Display Settings',
        'blorm_plugin_display_config_section_text',
        'blorm-plugin-display-config-section' );

    add_settings_field(
        'blorm_plugin_setting_display_config',
        'Show blorm posts on',
        'blorm_plugin_setting_display_config',
        'blorm-plugin-display-config-section',
        'blorm-plugin-display-config-section' );

    add_option("blorm_plugin_options_config_section", array(), "", "yes");
}

function blorm_plugin_display_config_section_text() {
    echo "<p>Decide how to display the rebloged posts:
            <ul>
            <li><i>main loop only</i> - as regular posts in the loop - in most cases used on your homepage</li>
             <li><i>widget only</i> - in the blorm widget, please do not forget to <a href='/wp-admin/widgets.php'>activate the widget</a></li>
             <li><i>category</i> - select a category to show the posts. Do not forget to select the category!</li>
             <li><i>and combination</i> - show posts in different locations</li></ul></p>";
}

function blorm_plugin_setting_display_config() {

    $options = get_option( 'blorm_plugin_options_frontend' );
    $value = "";
    if (isset( $options['display_config'] )) {
        $value = $options['display_config'];
    }

    $isSelected = function($option_value) use ($value){
        if ($value == $option_value) {
            return "selected";
        }
    };

    echo "<p>Select the place to show the posts.<br><br></p>";
    echo "<select id='blorm_plugin_options_frontend-display_config_menue' name='blorm_plugin_options_frontend[display_config]'>\n
            <option value='do-not-show'>Do not show anything</option>\n
            <option value='display_config_loop' ".$isSelected('display_config_loop').">main loop only</option>\n
            <option value='display_config_widget' ".$isSelected('display_config_widget').">widget only</option>\n
            <option value='display_config_category' ".$isSelected('display_config_category').">category only</option>\n
            <option value='display_config_loop_and_widget' ".$isSelected('display_config_loop_and_widget').">main loop and widget</option>\n
            <option value='display_config_loop_and_category' ".$isSelected('display_config_loop_and_category').">main loop and category</option>\n
            <option value='display_config_category_and_widget' ".$isSelected('display_config_category_and_widget').">category and widget</option>\n
            <option value='display_config_loop_and_category_and_widget' ".$isSelected('display_config_loop_and_category_and_widget').">main loop and category and widget</option>\n
           </select>";
}

/*
 *  widget rendering
 */

add_action( 'admin_init', 'blorm_register_settings_frontend_section' );
function blorm_register_settings_frontend_section() {

	// frontend rendering options

	add_settings_section(
		'blorm-plugin-frontend-section',
		'Blorm social widget display settings',
		'blorm_plugin_frontend_section_text',
		'blorm-plugin-frontend-section' );

    add_settings_field(
        'blorm_plugin_setting_add_blorm_icon_to_title',
        'Add the blorm icon to shared titles',
        'blorm_plugin_setting_add_blorm_icon_to_title',
        'blorm-plugin-frontend-section',
        'blorm-plugin-frontend-section');

	add_settings_field(
		'blorm_plugin_setting_add_blorm_widget',
		'Blorm social widget',
		'blorm_plugin_setting_add_blorm_widget',
		'blorm-plugin-frontend-section',
		'blorm-plugin-frontend-section' );

    add_settings_field(
        'blorm_plugin_setting_add_special_css_class_post',
        'Use special css class for post teasers',
        'blorm_plugin_setting_add_special_css_class_post',
        'blorm-plugin-frontend-section',
        'blorm-plugin-frontend-section');

    add_settings_field(
        'blorm_plugin_setting_add_special_css_class_post_img',
        'Use special css class for img in posts teasers',
        'blorm_plugin_setting_add_special_css_class_post_img',
        'blorm-plugin-frontend-section',
        'blorm-plugin-frontend-section');

	add_settings_field(
		'blorm_plugin_setting_add_blorm_widget_position',
		'Position of the social widget',
		'blorm_plugin_setting_add_blorm_widget_position',
		'blorm-plugin-frontend-section',
		'blorm-plugin-frontend-section' );

	add_option("blorm_plugin_options_frontend", array(), "", "yes");

}

function blorm_plugin_api_section_text() {
	echo '<p>Here you can set all the options for using the API</p>';
}


function blorm_plugin_setting_add_blorm_icon_to_title() {
    $options = get_option( 'blorm_plugin_options_frontend' );

    $value = "";
    if (isset( $options['blorm_icon_to_title'] )) {
        $value = $options['blorm_icon_to_title'];
    }

    $isSelected = function($option_value) use ($value){
        if ($value == $option_value) {
            return "selected";
        }
    };

    echo "<select id='blorm-plugin-options-frontend-blorm-icon-to-title' name='blorm_plugin_options_frontend[blorm_icon_to_title]'>\n
            <option value='add_blorm_icon_to_title' ".$isSelected('add_blorm_icon_to_title').">Add icon to title</option>\n
            <option value='add_blorm_icon_not_to_title' ".$isSelected('add_blorm_icon_not_to_title').">Do not add icon to title</option>\n
            </select>";

}

function blorm_plugin_setting_add_blorm_widget() {

	$options = get_option( 'blorm_plugin_options_frontend' );

	$value = "";
	if (isset( $options['position_widget_menue'] )) {
		$value = $options['position_widget_menue'];
	}

	$isSelected = function($option_value) use ($value){
		if ($value == $option_value) {
			return "selected";
		}
	};

	echo "<p>Select the position of the blorm social widget for your shared posts. 
             <br>You can try to automaticaly render it on 
             <br>1) an image 
             <br>2) behind|before title|content
             <br>or 3) you insert the php code: <code>blorm_display_widget()</code> manually in your template.<br><br></p>";
	echo "<select id='blorm_plugin_options_frontend-position_widget_menue' name='blorm_plugin_options_frontend[position_widget_menue]'>\n
            <option value='-'>Do not render</option>\n
            <option value='add_blorm_info_on_image' ".$isSelected('add_blorm_info_on_image').">on image</option>\n
            <option value='add_blorm_info_before_content' ".$isSelected('add_blorm_info_before_content').">before content</option>\n
            <option value='add_blorm_info_after_content' ".$isSelected('add_blorm_info_after_content').">after content</option>\n
            <option value='add_blorm_info_before_title' ".$isSelected('add_blorm_info_before_title').">before title</option>\n
            <option value='add_blorm_info_after_title' ".$isSelected('add_blorm_info_after_title').">after title</option>\n
            <option value='add_blorm_info_on_theme_tag' ".$isSelected('add_blorm_info_on_theme_tag').">insert blorm_display_widget() code in theme</option>\n
           </select>";
}

function blorm_plugin_setting_add_special_css_class_post() {
    $options = get_option( 'blorm_plugin_options_frontend' );

    // css class
    $value_special_css_class_for_post = "";
    if (isset( $options['special_css_class_for_post'] )) {
        $value_special_css_class_for_post = $options['special_css_class_for_post'];
    }

    echo "<p>Blorm tries to discover posts on your website automaticaly.</p>";
    echo "<p>If your theme is NOT using the wordpress standard css-classes for styling an article or post you may define here one of your css-classes.<br><br></p>";
    echo "<input type=\"text\" id=\"blorm_plugin_options_frontend-special_css_class_for_post\" name=\"blorm_plugin_options_frontend[special_css_class_for_post]\" value=\"".$value_special_css_class_for_post."\">";

}

function blorm_plugin_setting_add_special_css_class_post_img() {
    $options = get_option( 'blorm_plugin_options_frontend' );

    // css class
    $value_special_css_class_for_post_img = "";
    if (isset( $options['special_css_class_for_post_img'] )) {
        $value_special_css_class_for_post_img = $options['special_css_class_for_post_img'];
    }

    echo "<p>When you decide to render social widget on image, the plugin tries to discover the first image or thumbnail automatic.</p>";
    echo "<p>If this fails you may define here a css-classes to use on the image-tag.<br><br></p>";
    echo "<input type=\"text\" id=\"blorm_plugin_options_frontend-special_css_class_for_post_img\" name=\"blorm_plugin_options_frontend[special_css_class_for_post_img]\" value=\"".$value_special_css_class_for_post_img."\">";

}

function blorm_plugin_setting_add_blorm_widget_position() {

	$options = get_option( 'blorm_plugin_options_frontend' );

	// float
	$value_float = "";
	if (isset( $options['position_widget_menue_adjust_float'] )) {
		$value_float = $options['position_widget_menue_adjust_float'];
	}

	$isSelected_float = function($option_value) use ($value_float){
		if ($value_float == $option_value) {
			return "selected";
		}
	};
	echo "<label for=\"blorm_plugin_options_frontend[position_widget_menue_adjust_float]\">Select the alignment of the social widget for your shared posts. </label>";
	echo "<select id='blorm_plugin_options_frontend-position_widget_menue_adjust_float' name='blorm_plugin_options_frontend[position_widget_menue_adjust_float]'>\n
            <option value='float_left' ".$isSelected_float('float_left').">left</option>\n
            <option value='float_right' ".$isSelected_float('float_right').">right</option>\n
           </select>";

	// css class
	$value_classForWidgetPlacement = "";
	if (isset( $options['position_widget_menue_adjust_classForWidgetPlacement'] )) {
		$value_classForWidgetPlacement = $options['position_widget_menue_adjust_classForWidgetPlacement'];
	}

	echo "<br><br><label for=\"blorm_plugin_options_frontend-position_widget_menue_adjust_classForWidgetPlacement\">Do you want to assign a special css class around the widget. </label>";
	echo "<input type=\"text\" id=\"blorm_plugin_options_frontend-position_widget_menue_adjust_classForWidgetPlacement\" name=\"blorm_plugin_options_frontend[position_widget_menue_adjust_classForWidgetPlacement]\" value=\"".$value_classForWidgetPlacement."\">";


	// unit ( % or px)
	$value_unit = "";
	if (isset( $options['position_widget_menue_adjust_unit'] )) {
		$value_unit = $options['position_widget_menue_adjust_unit'];
	}

	$isSelected_unit = function($option_value) use ($value_unit){
		if ($value_unit == $option_value) {
			return "selected";
		}
	};
	echo "<br><br><label for=\"blorm_plugin_options_frontend[position_widget_menue_adjust_unit]\">Select the unit for adjusting the position. </label>";
	echo "<select id='blorm_plugin_options_frontend-position_widget_menue_adjust_unit' name='blorm_plugin_options_frontend[position_widget_menue_adjust_unit]'>\n
            <option value='unit_px' ".$isSelected_unit('unit_px').">px</option>\n
            <option value='unit_percent' ".$isSelected_unit('unit_percent').">%</option>\n
           </select>";

	// margin top
	$value_positionTop = "";
	if (isset( $options['position_widget_menue_adjust_positionTop'] )) {
		$value_positionTop = $options['position_widget_menue_adjust_positionTop'];
	}

	echo "<br><br><label for=\"blorm_plugin_options_frontend-position_widget_menue_adjust_positionTop\">Widget margin-top: </label>";
	echo "<input type=\"number\" id=\"blorm_plugin_options_frontend-position_widget_menue_adjust_positionTop\" name=\"blorm_plugin_options_frontend[position_widget_menue_adjust_positionTop]\" value=\"".$value_positionTop."\" maxlength=\"4\" size=\"4\">";

    // margin right
	$value_positionRight = "";
	if (isset( $options['position_widget_menue_adjust_positionRight'] )) {
		$value_positionRight = $options['position_widget_menue_adjust_positionRight'];
	}

	echo "<br><br><label for=\"blorm_plugin_options_frontend-position_widget_menue_adjust_positionRight\">Widget margin-right: </label>";
	echo "<input type=\"number\" id=\"blorm_plugin_options_frontend-position_widget_menue_adjust_positionRight\" name=\"blorm_plugin_options_frontend[position_widget_menue_adjust_positionRight]\" value=\"".$value_positionRight."\" maxlength=\"4\" size=\"4\">";

    // margin bottom
	$value_positionBottom = "";
	if (isset( $options['position_widget_menue_adjust_positionBottom'] )) {
		$value_positionBottom = $options['position_widget_menue_adjust_positionBottom'];
	}

	echo "<br><br><label for=\"blorm_plugin_options_frontend-position_widget_menue_adjust_positionBottom\">Widget margin-bottom: </label>";
	echo "<input type=\"number\" id=\"blorm_plugin_options_frontend-position_widget_menue_adjust_positionBottom\" name=\"blorm_plugin_options_frontend[position_widget_menue_adjust_positionBottom]\" value=\"".$value_positionBottom."\" maxlength=\"4\" size=\"4\">";

    // margin left
	$value_positionLeft = "";
	if (isset( $options['position_widget_menue_adjust_positionLeft'] )) {
		$value_positionLeft = $options['position_widget_menue_adjust_positionLeft'];
	}

	echo "<br><br><label for=\"blorm_plugin_options_frontend-position_widget_menue_adjust_positionLeft\">Widget margin-left: </label>";
	echo "<input type=\"number\" id=\"blorm_plugin_options_frontend-position_widget_menue_adjust_positionLeft\" name=\"blorm_plugin_options_frontend[position_widget_menue_adjust_positionLeft]\" value=\"".$value_positionLeft."\" maxlength=\"4\" size=\"4\">";


}


/*
 * category settings
 */

add_action( 'admin_init', 'blorm_register_settings_category' );

function blorm_plugin_category_section_text() {
	echo '<p>Select categories for showing posts on your page or automatic pushing to blorm</p>';
}

function blorm_register_settings_category() {

	// display categories

	add_settings_section(
		'blorm-plugin-category-section',
		'Category settings',
		'blorm_plugin_category_section_text',
		'blorm-plugin-category-section' );

	add_settings_field(
		'blorm_plugin_setting_category_display_reblog',
		'Show reblogged posts',
		'blorm_plugin_setting_category_display_reblog',
		'blorm-plugin-category-section',
		'blorm-plugin-category-section' );

	add_option("blorm_plugin_options_category", array(), "", "yes");

}

function blorm_plugin_setting_category_automatic_post() {
	$options = get_option( 'blorm_plugin_options_category' );

	$value = "";
	if (isset( $options['blorm_category_automatic'] )) {
		$value = $options['blorm_category_automatic'];
	}

	$categories = get_categories( array(
		'orderby' => 'name',
		'order'   => 'ASC',
		'hide_empty'      => false,
	) );

	echo "<p>Posts from this category will be pushed to blorm automatic.<br><br></p>";
	echo "<select id='blorm_plugin_setting_blorm_category_automatic' name='blorm_plugin_options_category[blorm_category_automatic]'>\n
            <option value='no-category-selected'>---</option>";
	foreach( $categories as $category ) {
		if ($value == $category->cat_ID) {
			echo "<option value=\"".$category->cat_ID."\" selected>".$category->name."</option>";
		} else {
			echo "<option value=\"".$category->cat_ID."\">".$category->name."</option>";
		}
	}
	echo "</select>";

}

function blorm_plugin_setting_category_display_reblog() {
	$options = get_option( 'blorm_plugin_options_category' );

	$value = "";
	if (isset( $options['blorm_category_show_reblogged'] )) {
		$value = $options['blorm_category_show_reblogged'];
	}

	$categories = get_categories( array(
		'orderby' => 'name',
		'order'   => 'ASC',
		'hide_empty'      => false,
	) );

	echo "<p>Select a category to display your reblogged posts.<br>If nothing is selected it will be put in the standard loop wich is shown on home in most cases.<br><br></p>";
	echo "<select id='blorm_plugin_setting_blorm_category_show_reblogged' name='blorm_plugin_options_category[blorm_category_show_reblogged]'>\n
            <option value='no-category-selected'>---</option>";
	foreach( $categories as $category ) {
		if ($value == $category->cat_ID) {
			echo "<option value=\"".$category->cat_ID."\" selected>".$category->name."</option>";
		} else {
			echo "<option value=\"".$category->cat_ID."\">".$category->name."</option>";
		}
	}
	echo "</select>";

}


function blorm_plugin_setting_add_blorm_info() {

	echo "<p>The blorm info bar shows interactions of the post with other pages (retweet, share and comments) to the users of yout side. 
            <br>The best place for the menue bar depends on the design of your theme.</p>
             <p>We suggest to select just one of the possible positions.</p>";

	$options = get_option( 'blorm_plugin_options_frontend' );

	$add_blorm_info_before_title = "";
	if (isset( $options['add_blorm_info_before_title'] )) {
		$add_blorm_info_before_title = $options['add_blorm_info_before_title'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_before_title ), false)) {
		echo "<input id='blorm_plugin_setting_info_before_title' name='blorm_plugin_options_frontend[add_blorm_info_before_title]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_info_before_title' name='blorm_plugin_options_frontend[add_blorm_info_before_title]' type='checkbox' value='show' />";
	}
	echo "&nbsp;before title</p>";

	$add_blorm_info_after_title = "";
	if (isset( $options['add_blorm_info_after_title'] )) {
		$add_blorm_info_after_title = $options['add_blorm_info_after_title'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_after_title ), false)) {
		echo "<input id='blorm_plugin_setting_info_after_title' name='blorm_plugin_options_frontend[add_blorm_info_after_title]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_info_after_title' name='blorm_plugin_options_frontend[add_blorm_info_after_title]' type='checkbox' value='show' />";
	}
	echo "&nbsp;after title</p>";

	$add_blorm_info_before_content = "";
	if (isset( $options['add_blorm_info_before_content'] )) {
		$add_blorm_info_before_content = $options['add_blorm_info_before_content'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_before_content ), false)) {
		echo "<input id='blorm_plugin_setting_info_before_content' name='blorm_plugin_options_frontend[add_blorm_info_before_content]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_info_before_content' name='blorm_plugin_options_frontend[add_blorm_info_before_content]' type='checkbox' value='show' />";
	}
	echo "&nbsp;before content</p>";

	$add_blorm_info_after_content = "";
	if (isset( $options['add_blorm_info_after_content'] )) {
		$add_blorm_info_after_content = $options['add_blorm_info_after_content'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_after_content ), false)) {
		echo "<input id='blorm_plugin_setting_info_after_content' name='blorm_plugin_options_frontend[add_blorm_info_after_content]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_info_after_content' name='blorm_plugin_options_frontend[add_blorm_info_after_content]' type='checkbox' value='show' />";
	}
	echo "&nbsp;after content</p>";

	echo "<br><hr><br>";
	echo "<p>If your theme uses the standard class names for the posts you may also try to use on of these positions</p>";

	$add_blorm_info_before_post_header = "";
	if (isset( $options['add_blorm_info_before_post_header'] )) {
		$add_blorm_info_before_post_header = $options['add_blorm_info_before_post_header'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_before_post_header ), false)) {
		echo "<input id='blorm_plugin_setting_before_post_header' name='blorm_plugin_options_frontend[add_blorm_info_before_post_header]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_before_post_header' name='blorm_plugin_options_frontend[add_blorm_info_before_post_header]' type='checkbox' value='show' />";
	}
	echo "&nbsp;before html-block 'post header'</p>";


	$add_blorm_info_after_post_header = "";
	if (isset( $options['add_blorm_info_after_post_header'] )) {
		$add_blorm_info_after_post_header = $options['add_blorm_info_after_post_header'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_after_post_header ), false)) {
		echo "<input id='blorm_plugin_setting_after_post_header' name='blorm_plugin_options_frontend[add_blorm_info_after_post_header]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_after_post_header' name='blorm_plugin_options_frontend[add_blorm_info_after_post_header]' type='checkbox' value='show' />";
	}
	echo "&nbsp;after html-block 'post header'</p>";


	$add_blorm_info_before_post_content = "";
	if (isset( $options['add_blorm_info_before_post_content'] )) {
		$add_blorm_info_before_post_content = $options['add_blorm_info_before_post_content'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_before_post_content ), false)) {
		echo "<input id='blorm_plugin_setting_before_post_content' name='blorm_plugin_options_frontend[add_blorm_info_before_post_content]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_before_post_content' name='blorm_plugin_options_frontend[add_blorm_info_before_post_content]' type='checkbox' value='show' />";
	}
	echo "&nbsp;before html-block 'post content'</p>";


	$add_blorm_info_after_post_content = "";
	if (isset( $options['add_blorm_info_after_post_content'] )) {
		$add_blorm_info_after_post_content = $options['add_blorm_info_after_post_content'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_after_post_content ), false)) {
		echo "<input id='blorm_plugin_setting_after_post_content' name='blorm_plugin_options_frontend[add_blorm_info_after_post_content]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_after_post_content' name='blorm_plugin_options_frontend[add_blorm_info_after_post_content]' type='checkbox' value='show' />";
	}
	echo "&nbsp;after html-block 'post content'</p>";


	$add_blorm_info_before_post_footer = "";
	if (isset( $options['add_blorm_info_before_post_footer'] )) {
		$add_blorm_info_before_post_footer = $options['add_blorm_info_before_post_footer'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_before_post_footer ), false)) {
		echo "<input id='blorm_plugin_setting_before_post_footer' name='blorm_plugin_options_frontend[add_blorm_info_before_post_footer]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_before_post_footer' name='blorm_plugin_options_frontend[add_blorm_info_before_post_footer]' type='checkbox' value='show' />";
	}
	echo "&nbsp;before html-block 'post footer'</p>";


	$add_blorm_info_after_post_footer = "";
	if (isset( $options['add_blorm_info_after_post_footer'] )) {
		$add_blorm_info_after_post_footer = $options['add_blorm_info_after_post_footer'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_after_post_footer ), false)) {
		echo "<input id='blorm_plugin_setting_after_post_footer' name='blorm_plugin_options_frontend[add_blorm_info_after_post_footer]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_after_post_footer' name='blorm_plugin_options_frontend[add_blorm_info_after_post_footer]' type='checkbox' value='show' />";
	}
	echo "&nbsp;after html-block 'post footer'</p>";

}