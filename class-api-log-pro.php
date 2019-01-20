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
			
			if( ! is_wp_error( $response ) ) {
			
			$request_uri = esc_url( $_SERVER['REQUEST_URI'] ) ?? null;
			
			$path = $request->get_route() ?? '';
			$method   = $request->get_method() ?? '';
			$request_headers = $request->get_headers() ?? '';
			$response_headers = $response->get_headers() ?? '';
			
			
			$args = array(
				'path' => $path ?? '',
				'response' => $response ?? '',
				'response_headers' => $response_headers ?? '',
				'request_headers' => $request_headers ?? '',
				'load_time' => json_encode( array( 'time' => time() ) ) ?? '',
				'requested_at' => current_time( 'mysql' ) ?? '0000-00-00 00:00:00',
			); 
			
			$inserted_log_id = $this->add_api_log( $args );
			
			$query_count 		= get_num_queries() ?? '';
			$memory_usage 		= round( size_format( memory_get_usage() ), 2 ) ?? '';
			$memory_peak_usage 	= round( size_format( memory_get_peak_usage() ), 2 ) ?? '';
			$memory_limit 		= round( size_format( $this->let_to_num( WP_MEMORY_LIMIT ) ), 2 ) ?? '';
			
			
			$this->add_log_meta( $inserted_log_id, 'query_count', $query_count, true );
			$this->add_log_meta( $inserted_log_id, 'memory_usage', $memory_usage, true );
			$this->add_log_meta( $inserted_log_id, 'memory_peak_usage', $memory_peak_usage, true );
			$this->add_log_meta( $inserted_log_id, 'memory_limit', $memory_limit, true );
			
			}
			
			// Return Response.
			return $response;
			
		}

		
		/**
		 * Add API Log.
		 * 
		 * @access public
		 * @param array $args (default: array()) Arguments.
		 */
		public function add_api_log( $args = array() ) {
			
			global $wpdb;
			
			$table = $wpdb->prefix . 'api_log_pro';
			
			$path        			= $args['path'] ?? '';
			$response      			= $args['response'] ?? '';
			$response_headers       = $args['response_headers'] ?? '';
			$request_headers 		= $args['request_headers'] ?? '';
			$load_time 				= $args['load_time'] ?? '';
			$requested_at   		= $args['requested_at'] ?? '';
	
			
			$results = $wpdb->insert(
				$table,
				array(
					'id'       		 		 => $wpdb->insert_id,
					'path' 			 		 => $path,
					'response'				 => wp_json_encode( $response ),
					'response_headers'       => wp_json_encode( $response_headers ),
					'request_headers'        => wp_json_encode( $request_headers ),
					'load_time'				 => $load_time,
					'requested_at'   		 => $requested_at ?? '0000-00-00 00:00:00',
				),
				array( '%d', '%s', '%s', '%s', '%s', '%d', '%s' )
			);
		
			return $wpdb->insert_id;
			
			
		}
		
		/**
		 * Let to Number.
		 *
		 * This function transforms the php.ini notation for numbers (like '2M') to an integer.
		 *
		 * @param $size Size.
		 * @return int Int.
		 */
		public function let_to_num( $size ) {
		    $l 		 = substr( $size, -1 );
		    $ret 	 = substr( $size, 0, -1 );
		    switch( strtoupper( $l ) ) {
			    case 'P':
			        $ret *= 1024;
			    case 'T':
			        $ret *= 1024;
			    case 'G':
			        $ret *= 1024;
			    case 'M':
			        $ret *= 1024;
			    case 'K':
			        $ret *= 1024;
		    }
		    return $ret;
		}

		
		/**
		 * Get Log Meta.
		 * 
		 * @access public
		 * @param mixed $log_id Log ID.
		 * @param string $key (default: '') Key.
		 * @param bool $single (default: false) Single.
		 */
		public function get_log_meta( $log_id, string $key = '', bool $single = false  ) {
				
				$response = get_metadata( 'log', $object_id, $key, $single ) ?? false;

				return $response;
		}
		
		/**
		 * Add Log Meta..
		 * 
		 * @access public
		 * @param mixed $log_id Log ID.
		 * @param string $key Key.
		 * @param mixed $value Value.
		 * @param bool $unique (default: false) Unique.
		 */
		public function add_log_meta( $log_id, string $key, $value, $unique = false ) {
			
			$response = add_metadata( 'apilog', $log_id, $key, $value, $unique ) ?? false;
			
			return $response;
		}
		
		/**
		 * Update Log Meta.
		 * 
		 * @access public
		 * @param mixed $log_id Log ID.
		 * @param string $key Meta Key.
		 * @param mixed $value Meta Value.
		 * @param string $prev_value (default: '') Previous Value.
		 */
		public function update_log_meta( $log_id, string $key, $value, $prev_value = '' ) {
			
			$response = update_metadata( 'apilog', $log_id, $key, $value, $prev_value ) ?? false;
			
			return $response;
		}
		
		/**
		 * Delete Log Meta.
		 * 
		 * @access public
		 * @param mixed $log_id Log ID.
		 * @param string $key (default: '') Key.
		 * @param string $value (default: '') Value.
		 * @param bool $delete_all (default: false) Delete All.
		 */
		public function delete_log_meta( $log_id, string $key = '', string $value = '', bool $delete_all = false ) {
			
			$response = delete_metadata( 'apilog', $log_id, $key, $value, $delete_all ) ?? false;
			
			return $response;
		}
		
		
	}
	
	new API_Log_Pro();
	
}