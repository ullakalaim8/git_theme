<?php


if( ! class_exists( 'themeupdater' ) ){
	include_once( plugin_dir_path( __FILE__ ) . 'theme-updater.php' );
}

$updater = new themeupdater('git_theme');
$updater->set_username( 'ullakalaim8' );
$updater->set_repository( 'git_theme' );
/*
	$updater->authorize( 'abcdefghijk1234567890' ); // Your auth code goes here for private repos
*/
$updater->initialize();
