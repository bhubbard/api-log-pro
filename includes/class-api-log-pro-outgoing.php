<?php
/**
 * Outgoing Log API.
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'API_Log_Pro_Outgoing' ) ) {

	/**
	 * API Log Pro Outgoing Requests.
	 */
	class API_Log_Pro_Outgoing {

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_filter( 'http_request_args', array( $this, 'start_timer' ) );
			add_action( 'http_api_debug', array( $this, 'capture_request' ), 10, 5 );

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'api_log_pro_outgoing_cleanup_cron', array( $this, 'cleanup' ) );
		}


		/**
		 * Init.
		 */
		public function init() {

			if ( ! wp_next_scheduled( 'api_log_pro_outgoing_cleanup_cron' ) ) {
				wp_schedule_single_event( time() + 1296000, 'api_log_pro_outgoing_cleanup_cron' ); // 15 days.
			}

		}

		/**
		 * Start Timer.
		 *
		 * @param  [type] $args Arguments.
		 * @return $args Arguments.
		 */
		public function start_timer( $args ) {
			$this->start_time = microtime( true );
			return $args;
		}

		/**
		 * Capture Request.
		 *
		 * @param  [type] $response Response.
		 * @param  [type] $context Context.
		 * @param  [type] $transport Transport.
		 * @param  [type] $args Arguments.
		 * @param  [type] $url URL.
		 */
		public function capture_request( $response, $context, $transport, $args, $url ) {

			if ( false !== strpos( $url, 'doing_wp_cron' ) ) {
				return;
			}
			// Get Domain From URL.
			$url_parse = wp_parse_url( $url );
			$host      = $url_parse['host'];

			$cookies = wp_remote_retrieve_cookies( $response );

			// Send Array Data.
			$log_data = apply_filters(
				'api_log_pro_outgoing_data',
				array(
					'url'              => $url,
					'domain'           => $host,
					'request_args'     => $args,
					'response'         => $response,
					'response_headers' => wp_remote_retrieve_headers( $response ),
					'status'           => wp_remote_retrieve_response_code( $response ),
					'runtime'          => ( microtime( true ) - $this->start_time ),
					'body'             => wp_remote_retrieve_body( $response ),
					'method'           => $args['method'],
				)
			);

			$this->add_outgoing_api_log( $log_data );

		}

		/**
		 * Add Outgoing API Log.
		 *
		 * @param array $args Arguments.
		 */
		public function add_outgoing_api_log( $args = array() ) {

			global $wpdb;
			$table = $wpdb->prefix . 'api_log_pro_outgoing';

			$url          = $args['url'] ?? '';
			$domain       = $args['domain'] ?? '';
			$response     = $args['response'] ?? '';
			$request_args = $args['request_args'] ?? '';
			$status       = $args['status'] ?? '';
			$runtime      = $args['runtime'] ?? '';
			$method       = $args['method'] ?? '';
			$body         = $args['body'] ?? '';

			$results = $wpdb->insert(
				$table,
				array(
					'url'          => $url,
					'domain'       => $domain,
					'response'     => wp_json_encode( $response ),
					'request_args' => wp_json_encode( $request_args ),
					'status'       => $status,
					'method'       => $method,
					'runtime'      => $runtime,
					'body'         => $body,
					'requested_at' => current_time( 'mysql' ),
				),
				array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
			);

			return $wpdb->insert_id;

		}

		/**
		 * Cleanup.
		 *
		 * @access public
		 */
		public function cleanup() {
			$this->delete_logs();
		}

		/**
		 * Get All Logs.
		 *
		 * @access public
		 * @param array $args (default: array()) Arguments.
		 */
		public function get_logs( $args = array() ) {

			global $wpdb;

			$table   = $wpdb->prefix . 'api_log_pro_outgoing';
			$results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `%1s`', $table ) );

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

			$table   = $wpdb->prefix . 'api_log_pro_outgoing';
			$results = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %1s WHERE ID = %d', $table, $log_id ) );

			if ( ! empty( $results ) ) {
				return $results;
			} else {
				return new WP_Error( 'invalid_log_id', __( 'Sorry no log exists with that ID.', 'api-log-pro' ) );
			}

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

			$table   = $wpdb->prefix . 'api_log_pro_outgoing';
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

			$table   = $wpdb->prefix . 'api_log_pro_outgoing';
			$results = $wpdb->query( $wpdb->prepare( 'TRUNCATE TABLE %1s', $table ) );

			return $results;
		}

	}

	new API_Log_Pro_Outgoing();
}
