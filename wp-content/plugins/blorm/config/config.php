<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 08.10.18
 * Time: 23:54
 */

function get_blorm_config() {


    $options = get_option("blorm_plugin_options_api");

    if ($options == false) {
        $options = array();
    }

    $api_options = Array(
	    'api' => 'https://api.blorm.io',
	    'version' => '0.9',
        'update_intervall' => '180',
    );

    if ( file_exists ( plugin_dir_path( __FILE__ ) . '/config_dev.php' )) {
    	$api_options = include plugin_dir_path( __FILE__ ) . '/config_dev.php';
    }

	$returnArray = array_merge(
        $options,
		$api_options
    );

    return $returnArray;
};

function get_blorm_config_param($key) {

    $config = get_blorm_config();

    if (isset($config[$key]) ) {
        return $config[$key];
    }

    return "no value for key avilable";
};