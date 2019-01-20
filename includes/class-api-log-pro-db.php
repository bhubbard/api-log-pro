<?php
	
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;	

if( ! class_exists( 'API_Log_Pro_DB' ) ) {
	
	/**
	 * API_Log_Pro_DB class.
	 */
	class API_Log_Pro_DB {
		
		/**
		 * Constructor
		 * 
		 * @access public
		 */
		public function __construct() {
			
				add_action( 'activated_plugin', array( $this, 'create_log_db' ) );
				add_action( 'activated_plugin', array( $this, 'create_log_meta_db' ) );
				
				add_action( 'plugins_loaded', array( $this, 'register_log_meta_table' ) );

		}
		
		/**
		 * Create Log DB.
		 * 
		 * @access public
		 */
		public function create_log_db() {
			
			global $wpdb;
			
			$wpdb->show_errors();
			$charset_collate = $wpdb->get_charset_collate();
			$table_name      = $wpdb->prefix . 'api_log_pro';
			$sql             =
			"CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				path text,
				response longtext,
				request_headers longtext,
				response_headers longtext,
				load_time bigint,
				requested_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

		}
		
		/**
		 * Create Log Meta DB.
		 * 
		 * @access public
		 */
		public function create_log_meta_db() {
			
				global $wpdb;
				
				$charset_collate = $wpdb->get_charset_collate();
				$table_name      = $wpdb->prefix . 'api_log_pro_meta';
				$sql             =
				"CREATE TABLE $table_name (
				  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `apilog_id` bigint(20) NOT NULL DEFAULT '0',
				  `meta_key` varchar(255) DEFAULT NULL,
				  `meta_value` longtext,
				  PRIMARY KEY (`meta_id`),
				  KEY `apilog_id` (`apilog_id`),
				  KEY `meta_key` (`meta_key`)
				) $charset_collate;";
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
		}
		
		/**
		 * Register Log Meta Table.
		 * 
		 * @access public
		 */
		public function register_log_meta_table() {
			
			global $wpdb;

			$results = $wpdb->apilogmeta = $wpdb->prefix . 'api_log_pro_meta';
			
			return $results;

		}
	}
	
	new API_Log_Pro_DB();
}