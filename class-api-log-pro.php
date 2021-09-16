<?php
/**
 * Plugin Name: API Log Pro
 * Description: A simple plugin to log WP Rest API Requests.
 * Author: Brandon Hubbard
 * Author URI: https://hubbardlabs.com
 * Version: 0.0.4
 * Text Domain: api-log-pro
 * Domain Path: /languages/
 * Plugin URI: https://github.com/hubbardlabs/api-log-pro
 * License: GPL3+
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


require_once 'includes/class-api-log-pro-db.php';
require_once 'includes/class-api-log-pro-rest-api.php';
require_once 'includes/class-api-log-pro-cli.php';

require_once 'includes/class-api-log-pro-outgoing.php';

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

			if ( ! is_admin() ) {

				add_filter( 'rest_post_dispatch', array( $this, 'log_requests' ), 10, 3 );

				// add_filter( 'rest_post_dispatch', array( $this, 'log_rest_api_errors' ), 10, 3 ); // Send API Errors to Error Log.
			}

			add_action( 'admin_init', array( $this, 'register_scripts' ) );

		}

		/**
		 * Register Scripts and Styles.
		 *
		 * @access public
		 */
		public function register_scripts() {
			wp_register_style( 'api-log-pro-admin', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css', null, '0.0.1', 'all' );
			wp_register_script( 'data-tables',  plugin_dir_url( __FILE__ ) . 'assets/js/jquery.datatables.min.js', array( 'jquery' ), '1.10.19', true );
			wp_register_script('logs-datatable', plugin_dir_url( __FILE__ ) . 'assets/js/logs-datatables.min.js', array('jquery', 'data-tables'), '0.0.2', true );
			wp_register_script('logs-datatable-outgoing', plugin_dir_url( __FILE__ ) . 'assets/js/logs-datatables-outgoing.js', array('jquery', 'data-tables'), '0.0.2', true );
			wp_register_script( 'highlight', plugin_dir_url( __FILE__ ) . 'assets/js/highlight.pack.js', array('jquery' ), '9.15.10', false );
			wp_register_style( 'highlight-atom-light-one', plugin_dir_url( __FILE__ ) . 'assets/css/highlight-wp-theme.min.css', null, '9.15.10', 'all' );





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
				$this->add_log_meta( $inserted_log_id, 'load_time', timer_stop( 1 ), true );

			// Return Response.
			return $response;

		}

		/**
		 * Log REST API errors
		 *
		 * @param WP_REST_Response $result  Result that will be sent to the client.
		 * @param WP_REST_Server   $server  The API server instance.
		 * @param WP_REST_Request  $request The request used to generate the response.
		 */
		public function log_rest_api_errors( $result, $server, $request ) {
			if ( $result->is_error() ) {
				error_log(
					sprintf(
						'REST request: %s: %s',
						$request->get_route(),
						print_r( $request->get_params(), true )
					)
				);
				error_log(
					sprintf(
						'REST result: %s: %s',
						$result->get_matched_route(),
						print_r( $result->get_data(), true )
					)
				);
			}
			return $result;
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
		 * Delete API Log Item.
		 *
		 * @access public
		 * @param mixed $log_id Log ID.
		 * @param bool  $meta (default: true) Optional, delete meta data.
		 */
		public function delete_api_log( $log_id, $meta = true ) {

			global $wpdb;

			$table = $wpdb->prefix . 'api_log_pro';
			$results = $wpdb->get_results( $wpdb->prepare( 'DELETE * FROM %1s WHERE ID = %d', $table, $log_id ) );

			// TODO: Delete Meta.
			return $results;

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
			$results = $wpdb->query( $wpdb->prepare( 'TRUNCATE TABLE %1s', $table ) );

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
			$results = $wpdb->query( $wpdb->prepare( 'TRUNCATE TABLE %1s', $table ) );

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
			$results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s', $table ) );

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
			$results = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %1s WHERE ID = %d', $table, $log_id ) );

			if ( ! empty( $results ) ) {
				return $results;
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
			$results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s WHERE apilog_id = %d', $table, $log_id ) );
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
