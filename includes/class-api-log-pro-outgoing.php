<?php


if( ! class_exists( 'API_Log_Pro_Outgoing' ) ) {

	/**
	 * API Log Pro Outgoing Requests.
	 */
	class API_Log_Pro_Outgoing {

		/**
		 * Constructor.
		 */
	    function __construct() {
	        add_filter( 'http_request_args', [ $this, 'start_timer' ] );
	        add_action( 'http_api_debug', [ $this, 'capture_request' ], 10, 5 );
	    }

		/**
		 * Start Timer.
		 * @param  [type] $args Arguments.
		 * @return $args Arguments.
		 */
		function start_timer( $args ) {
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
	    function capture_request( $response, $context, $transport, $args, $url ) {

	        global $wpdb;

	        if ( false !== strpos( $url, 'doing_wp_cron' ) ) {
	            return;
	        }

	        // False to ignore current row
	        $log_data = apply_filters( 'lhr_log_data', [
	            'url' => $url,
	            'request_args' => json_encode( $args ),
	            'response' => json_encode( $response ),
	            'runtime' => ( microtime( true ) - $this->start_time ),
	            'date_added' => current_time( 'mysql' )
	        ]);

			/*
	        if ( false !== $log_data ) {
	            $wpdb->insert( $wpdb->prefix . 'lhr_log', $log_data );
	        }
			*/
	    }

	}

	new API_Log_Pro_Outgoing();
}
