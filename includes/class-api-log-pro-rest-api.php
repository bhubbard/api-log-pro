<?php
/**
 * API Log Pro Rest API Support.
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'API_Log_Pro_Rest_API' ) ) {

	/**
	 * API_Log_Pro_Rest_API class.
	 */
	class API_Log_Pro_Rest_API {

		/**
		 * Constructor.
		 *
		 * @access public
		 */
		public function __construct() {

			add_action(
				'rest_api_init',
				function() {
					register_rest_route(
						'api-log-pro/v1',
						'logs',
						array(
							'methods'  => array( 'get' ),
							'callback' => array( $this, 'get_api_logs' ),
						/*
						'permission_callback' => array( $this, 'permission_check' ),
						*/
						)
					);
				}
			);

		}

		/**
		 * Get API Logs.
		 *
		 * @access public
		 * @param WP_REST_Request $request Request.
		 */
		public function get_api_logs( WP_REST_Request $request ) {

			$api_log_pro = new API_Log_Pro() ?? false;
			$results     = $api_log_pro->get_logs() ?? array();

			// TODO: Acutally Format Response.
			return rest_ensure_response( $results );
		}

		/**
		 * Permission Check..
		 *
		 * @access public
		 * @param mixed $data Data.
		 */
		public function permission_check( $data ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return new WP_Error( 'forbidden', 'You are not allowed to do that.', array( 'status' => 403 ) );
			}
			return true;
		}

	}

	new API_Log_Pro_Rest_API();

}
