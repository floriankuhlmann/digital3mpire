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
        'update_intervall_180' => '180',
        'update_intervall_120' => '120',
        'update_intervall_60' => '60',
    );

    $configDevFile = ABSPATH . 'config_blorm_dev.php';
    if ( file_exists ( $configDevFile )) {
    	$api_options = include( $configDevFile );
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