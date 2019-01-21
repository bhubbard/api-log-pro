<?php
/**
 * API Log Pro Uninstall. Sorry to see you leave.
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


	global $wpdb;

	// Drop Log Table.
	$log_table = $wpdb->prefix . 'api_log_pro';
	$results   = $wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %1s', $log_table ) );

	// Drop Meta Table.
	$meta_table = $wpdb->prefix . 'api_log_pro_meta';
	$results    = $wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %1s', $meta_table ) );

