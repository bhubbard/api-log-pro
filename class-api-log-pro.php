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


require_once 'includes/class-api-log-pro-db.php';
require_once 'includes/class-api-log-pro-rest-api.php';
require_once 'includes/class-api-log-pro-cli.php';

require_once 'admin/admin-page.php';

if ( ! class_exists( 'API_Log_Pro' ) ) {

	/**
	 * API_Log_Pro class.
	 */
	class API_Log_Pro {

		/**
		 * Constructor.
		 *
		 * @access public
		 */
		public function __construct() {

			add_filter( 'rest_request_after_callbacks', array( $this, 'log_requests' ), 10, 3 );

		}

		/**
		 * Log Requests.
		 *
		 * @access public
		 * @param mixed $response Response.
		 * @param mixed $handler Handler.
		 * @param mixed $request Request.
		 */
		public function log_requests( $response, $handler, $request ) {

			if ( ! is_wp_error( $response ) ) {

				$request_uri = esc_url( $_SERVER['REQUEST_URI'] ) ?? null;

				$path            = $request->get_route() ?? '';
				$method          = $request->get_method() ?? '';
				$request_headers = $request->get_headers() ?? array();

				if ( ! empty( $response ) ) {
					$response_headers = $response->get_headers() ?? array();
					$data             = $response->get_data() ?? array();
					$status           = $response->get_status() ?? '';
				}

				$args = array(
					'path'             => $path ?? '',
					'response'         => $data ?? '',
					'response_headers' => $response_headers ?? '',
					'request_headers'  => $request_headers ?? '',
					'status'           => $status ?? '',
					'method'           => $method ?? '',
					'user'             => '',
					'requested_at'     => current_time( 'mysql' ) ?? '0000-00-00 00:00:00',
				);

				$inserted_log_id = $this->add_api_log( $args );

				$query_count       = get_num_queries() ?? '';
				$memory_usage      = memory_get_usage() ?? '';
				$memory_peak_usage = memory_get_peak_usage() ?? '';

				$this->add_log_meta( $inserted_log_id, 'query_count', $query_count, true );
				$this->add_log_meta( $inserted_log_id, 'memory_usage', $memory_usage, true );
				$this->add_log_meta( $inserted_log_id, 'memory_peak_usage', $memory_peak_usage, true );

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

			$path             = $args['path'] ?? '';
			$response         = $args['response'] ?? '';
			$response_headers = $args['response_headers'] ?? '';
			$request_headers  = $args['request_headers'] ?? '';
			$load_time        = $args['load_time'] ?? '';
			$requested_at     = $args['requested_at'] ?? '';
			$status           = $args['status'] ?? '';
			$method           = $args['method'] ?? '';
			$user_id          = $args['user_id'] ?? '';

			$results = $wpdb->insert(
				$table,
				array(
					'id'               => $wpdb->insert_id,
					'path'             => $path,
					'response'         => wp_json_encode( $response ),
					'response_headers' => wp_json_encode( $response_headers ),
					'request_headers'  => wp_json_encode( $request_headers ),
					'status'           => $status,
					'method'           => $method,
					'user'             => $user_id,
					'requested_at'     => $requested_at ?? '0000-00-00 00:00:00',
				),
				array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
			);

			return $wpdb->insert_id;

		}

		/**
		 * Delete All Logs.
		 *
		 * @access public
		 * @param array $args (default: array()) Arguments.
		 */
		public function delete_logs( $args = array() ) {

			global $wpdb;

			$table = $wpdb->prefix . 'api_log_pro';

			$results = $wpdb->get_results( "TRUNCATE TABLE $table" );

			return $results;
		}

		/**
		 * Delete All Logs Metadata.
		 *
		 * @access public
		 * @param array $args (default: array()) Arguments.
		 */
		public function delete_logs_meta( $args = array() ) {

			global $wpdb;

			$table = $wpdb->prefix . 'api_log_pro_meta';

			$results = $wpdb->get_results( "TRUNCATE TABLE $table" );

			return $results;
		}

		/**
		 * Get All Logs.
		 *
		 * @access public
		 * @param array $args (default: array()) Arguments.
		 */
		public function get_logs( $args = array() ) {

			global $wpdb;

			$table = $wpdb->prefix . 'api_log_pro';

			$results = $wpdb->get_results( "SELECT * FROM $table" );

			return $results;

		}

		/**
		 * Get Single Log.
		 *
		 * @access public
		 * @param mixed $log_id Log ID.
		 * @param array $args (default: array()) Arguments.
		 */
		public function get_log( $log_id, $args = array() ) {

			global $wpdb;

			$table = $wpdb->prefix . 'api_log_pro';

			$results = $wpdb->get_results( "SELECT * FROM $table WHERE ID = $log_id" );

			if ( ! empty( $results ) ) {
				return $results[0];
			} else {
				return new WP_Error( 'invalid_log_id', __( 'Sorry no log exists with that ID.', 'api-log-pro' ) );
			}

		}

		/**
		 * Get All Log Meta.
		 *
		 * @access public
		 * @param mixed $log_id Log ID.
		 * @param array $args (default: array()) Arguments.
		 */
		public function get_all_log_meta( $log_id, $args = array() ) {

			global $wpdb;

			$table = $wpdb->prefix . 'api_log_pro_meta';

			$results = $wpdb->get_results( "SELECT * FROM $table WHERE apilog_id = $log_id" );

			return $results;
		}

		/**
		 * Get Log Meta.
		 *
		 * @access public
		 * @param mixed  $log_id Log ID.
		 * @param string $key (default: '') Key.
		 * @param bool   $single (default: false) Single.
		 */
		public function get_log_meta( $log_id, string $key = '', bool $single = false ) {

				$response = get_metadata( 'log', $object_id, $key, $single ) ?? false;

				return $response;
		}

		/**
		 * Add Log Meta.
		 *
		 * @access public
		 * @param mixed  $log_id Log ID.
		 * @param string $key Key.
		 * @param mixed  $value Value.
		 * @param bool   $unique (default: false) Unique.
		 */
		public function add_log_meta( $log_id, string $key, $value, $unique = false ) {

			$response = add_metadata( 'apilog', $log_id, $key, $value, $unique ) ?? false;

			return $response;
		}

		/**
		 * Update Log Meta.
		 *
		 * @access public
		 * @param mixed  $log_id Log ID.
		 * @param string $key Meta Key.
		 * @param mixed  $value Meta Value.
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
		 * @param mixed  $log_id Log ID.
		 * @param string $key (default: '') Key.
		 * @param string $value (default: '') Value.
		 * @param bool   $delete_all (default: false) Delete All.
		 */
		public function delete_log_meta( $log_id, string $key = '', string $value = '', bool $delete_all = false ) {

			$response = delete_metadata( 'apilog', $log_id, $key, $value, $delete_all ) ?? false;

			return $response;
		}


	}

	new API_Log_Pro();

}
