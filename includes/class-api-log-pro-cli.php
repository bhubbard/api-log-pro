<?php
/**
 * WP-CLI Support for API Log Pro.
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( defined( 'WP_CLI' ) && WP_CLI ) {

	if ( ! class_exists( 'API_Log_Pro_CLI' ) ) {

		/**
		 * API Log Pro.
		 *
		 * ## OPTIONS
		 *
		 * delete: Delete All Logs created by API Log Pro.
		 *
		 *
		 * ## EXAMPLES
		 *
		 * wp api-log-pro delete
		 */
		class API_Log_Pro_CLI {

			/**
			 * Constructor.
			 *
			 * @access public
			 */
			public function __construct() {

			}

			/**
			 * Delete all Logs
			 *
			 * @access public
			 * @param mixed $args Arguments.
			 * @param mixed $assoc_args Associated Arguments.
			 */
			public function delete( $args, $assoc_args ) {

				$api_log_pro  = new API_Log_PRO();
				$results      = $api_log_pro->delete_logs();
				$meta_results = $api_log_pro->delete_logs_meta();

				if ( ! empty( $results ) && ! empty( $meta_results ) ) {
					WP_CLI::success( __( 'The Logs has been cleared.', 'api-log-pro' ) );
				} else {
					WP_CLI::error( __( 'The Logs are either empty, or there was an error.', 'api-log-pro' ) );
				}

			}

		}

		WP_CLI::add_command( 'api-log-pro', 'API_Log_Pro_CLI' );

	}
}
