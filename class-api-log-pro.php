<?php
/**
 * Plugin Name: API Log Pro
 * Description: A simple plugin to log WP Rest API Requests.
 * Author: Brandon Hubbard
 * Author URI: http://github.com/bhubbard
 * Version: 0.0.1
 * Text Domain: api-log-pro
 * Domain Path: /languages/
 * Plugin URI: https://github.com/bhubbard/api-log-pro
 * License: GPL3+
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

include_once('includes/class-api-log-pro-db.php');


if( ! class_exists( 'API_Log_Pro' )) {
	
	/**
	 * API_Log_Pro class.
	 */
	class API_Log_Pro {
		
		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		function __construct() {
			
			add_filter( 'rest_request_after_callbacks', array( $this, 'log_requests' ), 10, 3 );
	
		}
		
		public function log_requests( $response, $handler, $request ) {
			
			$request_uri = esc_url( $_SERVER['REQUEST_URI'] ) ?? null;
			
			$path = $request->get_route();
			$method   = $request->get_method();
			$request_headers = $request->get_headers();
			$response_headers = $response->get_headers();
			
			$args = array(
				'path' => $path ?? '',
				'response' => $response ?? '',
				'response_headers' => $response_headers ?? '',
				'request_headers' => $request_headers ?? '',
				'load_time' => $load_time ?? '',
				'requested_at' => current_time( 'mysql' ) ?? '0000-00-00 00:00:00',
			); 
			
			$this->add_api_log( $args );
			
			return $response;
			
		}


		public function add_api_log( $args = array() ) {
			
			global $wpdb;
			
			$table = $wpdb->prefix . 'api_log_pro';
			
			$path        			= $args['path'] ?? '';
			$response      			= $args['response'] ?? '';
			$response_headers       = $args['response_headers'] ?? '';
			$request_headers 		= $args['request_headers'] ?? '';
			$load_time 				= $args['request_headers'] ?? '';
			$requested_at   		= $args['requested_at'] ?? '';
		
			$wpdb->show_errors();
			
			$results = $wpdb->insert(
				$table,
				array(
					'id'       		 		 => $wpdb->insert_id,
					'path' 			 		 => $path,
					'response'				 => wp_json_encode( $response ),
					'response_headers'       => wp_json_encode( $response_headers ),
					'request_headers'        => wp_json_encode( $request_headers ),
					'load_time'				 => '5',
					'requested_at'   		 => $requested_at ?? '0000-00-00 00:00:00',
				),
				array( '%d', '%s', '%s', '%s', '%s', '%d', '%s' )
			);
		
			 return $results;
			
			
		}

		
		/**
		 * Get Log Meta.
		 * 
		 * @access public
		 * @param mixed $log_id
		 * @param string $key (default: '')
		 * @param bool $single (default: false)
		 */
		public function get_log_meta( $log_id, string $key = '', bool $single = false  ) {
				
				$response = get_metadata( 'log', $object_id, $key, $single ) ?? false;

				return $response;
		}
		
		/**
		 * Add Log Meta..
		 * 
		 * @access public
		 * @param mixed $log_id
		 * @param string $key
		 * @param mixed $value
		 * @param bool $unique (default: false)
		 */
		public function add_log_meta( $log_id, string $key, $value, $unique = false ) {
			
			$response = add_metadata( 'apilog', $log_id, $key, $value, $unique ) ?? false;
			
			return $response;
		}
		
		/**
		 * Update Log Meta.
		 * 
		 * @access public
		 * @param mixed $log_id
		 * @param string $key
		 * @param mixed $value
		 * @param string $prev_value (default: '')
		 */
		public function update_log_meta( $log_id, string $key, $value, $prev_value = '' ) {
			
			$response = update_metadata( 'apilog', $log_id, $key, $value, $prev_value ) ?? false;
			
			return $response;
		}
		
		/**
		 * Delete Log Meta.
		 * 
		 * @access public
		 * @param mixed $log_id
		 * @param string $key (default: '')
		 * @param string $value (default: '')
		 * @param bool $delete_all (default: false)
		 */
		public function delete_log_meta( $log_id, string $key = '', string $value = '', bool $delete_all = false ) {
			
			$response = delete_metadata( 'apilog', $log_id, $key, $value, $delete_all ) ?? false;
			
			return $response;
		}
		
		
	}
	
	new API_Log_Pro();
	
}