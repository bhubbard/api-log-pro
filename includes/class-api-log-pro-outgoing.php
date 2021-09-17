<?php


if( ! class_exists( 'API_Log_Pro_Outgoing' ) ) {

	/**
	 * API Log Pro Outgoing Requests.
	 */
	class API_Log_Pro_Outgoing {

		/**
		 * Constructor.
		 */
	    public function __construct() {
	        add_filter( 'http_request_args', [ $this, 'start_timer' ] );
	        add_action( 'http_api_debug', [ $this, 'capture_request' ], 10, 5 );

			add_action( 'init', [ $this, 'init' ] );
			add_action( 'api_log_pro_outgoing_cleanup_cron', [ $this, 'cleanup' ] );
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
		 * @param  [type] $args Arguments.
		 * @return $args Arguments.
		 */
		public function start_timer( $args ) {
			$this->start_time = microtime( true );
			return $args;
  		}

		/**
		 * [capture_request description]
		 * @param  [type] $response                [description]
		 * @param  [type] $context                 [description]
		 * @param  [type] $transport               [description]
		 * @param  [type] $args                    [description]
		 * @param  [type] $url                     [description]
		 * @return [type]            [description]
		 */
	    public function capture_request( $response, $context, $transport, $args, $url ) {

	        if ( false !== strpos( $url, 'doing_wp_cron' ) ) {
	            return;
	        }

			/*
			$query_count       = get_num_queries() ?? '';
			$memory_usage      = memory_get_usage() ?? '';
			$memory_peak_usage = memory_get_peak_usage() ?? '';
			*/

			// Get Domain From URL.
			$url_parse = parse_url( $url );
			$host = $url_parse['host'];

			$cookies = wp_remote_retrieve_cookies( $response );

	       // Send Array Data.
	        $log_data = apply_filters( 'api_log_pro_outgoing_data', [
	            'url' => $url,
				'domain' => $host,
	            'request_args' => $args,
	            'response' => $response,
				'response_headers' => wp_remote_retrieve_headers( $response ),
				'status' => wp_remote_retrieve_response_code( $response ),
				'runtime' => ( microtime( true ) - $this->start_time ),
	            'body' => wp_remote_retrieve_body( $response ),
				'method' => $args['method'],
	        ]);


	        if ( false !== $log_data ) {
	            $this->add_outgoing_api_log( $log_data );
	        }

	    }

		/**
		 * [add_outgoing_api_log description]
		 * @param array $args  [description]
		 */
		public function add_outgoing_api_log( $args = array() ) {

			global $wpdb;
			$table = $wpdb->prefix . 'api_log_pro_outgoing';

			$wpdb->show_errors();

			$url              = $args['url'] ?? '';
			$domain           = $args['domain'] ?? '';
			$response         = $args['response'] ?? '';
			$request_args  	  = $args['request_args'] ?? '';
			$status           = $args['status'] ?? '';
			$runtime          = $args['runtime'] ?? '';
			$method           = $args['method'] ?? '';
			$body          	  = $args['body'] ?? '';

			$results = $wpdb->insert(
				$table,
				array(
					'id'               => $wpdb->insert_id,
					'url'              => $url,
					'domain'           => $domain,
					'response'         => wp_json_encode( $response ),
					'request_args'     => wp_json_encode( $request_args ),
					'status'           => $status,
					'method'           => $method,
					'runtime'          => $runtime,
					'body'             => $body,
					'requested_at'     => current_time( 'mysql' ),
				),
				array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
			);

			return $wpdb->insert_id;

		}

		/**
		 * [time_since description]
		 * @param  [type] $time               [description]
		 * @return [type]       [description]
		 */
		public function time_since( $time ) {
	        $time = current_time( 'timestamp' ) - strtotime( $time );
	        $time = ( $time < 1 ) ? 1 : $time;
	        $tokens = array (
	            31536000 => 'year',
	            2592000 => 'month',
	            604800 => 'week',
	            86400 => 'day',
	            3600 => 'hour',
	            60 => 'minute',
	            1 => 'second'
	        );

	        foreach ( $tokens as $unit => $text ) {
	            if ( $time < $unit ) continue;
	            $numberOfUnits = floor( $time / $unit );
	            return $numberOfUnits . ' ' . $text . ( ( $numberOfUnits > 1 ) ? 's' : '' );
	        }
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

			$table = $wpdb->prefix . 'api_log_pro_outgoing';
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

			$table = $wpdb->prefix . 'api_log_pro_outgoing';
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

			$table = $wpdb->prefix . 'api_log_pro_outgoing';
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

			$table = $wpdb->prefix . 'api_log_pro_outgoing';
			$results = $wpdb->query( $wpdb->prepare( 'TRUNCATE TABLE %1s', $table ) );

			return $results;
		}

	}

	new API_Log_Pro_Outgoing();
}
