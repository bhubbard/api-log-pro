<?php
/**
 * API Log Pro Admin Page.
 *
 * @package api-log-pro
 */

add_action( 'admin_menu', 'api_log_pro_menu' );

/**
 * Wp_queue_menu function.
 *
 * @access public
 */
function api_log_pro_menu() {
	add_menu_page( 'API Log Pro', 'API Log Pro', 'manage_options', 'apilogpro', 'api_log_pro_page' );
}

/**
 * Function to get the current active tab.
 *
 * @access private
 */
function api_log_pro_menu_active_tab() {
	$active_tab = filter_input( INPUT_GET, 'tab' );
	return isset( $active_tab ) ? $active_tab : 'logs';
}

/**
 * Idxbrokerpro_menu_active_subtab function.
 *
 * @access public
 */
function api_log_pro_menu_active_subtab() {
	$active_subtab = filter_input( INPUT_GET, 'subtab' );
	return isset( $active_subtab ) ? $active_subtab : '';
}

/**
 * Wp_queue_settings_page function.
 *
 * @access public
 */
function api_log_pro_page() {

	// TODO: https://premium.wpmudev.org/blog/wordpress-admin-tables/.
	wp_enqueue_script( 'data-tables', 'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/jquery.dataTables.min.js', array( 'jquery' ), null, true );

	$active_tab = api_log_pro_menu_active_tab();

	$tabs = array( 'logs', 'support' );

	?>
	<div class="wrap settings">

		<form method="post" action="options.php">
			<h1>API Log Pro - <?php echo ucwords( $active_tab ); ?></h1>

			<style>

				.submenu-tab {
					display: inline-block;
					margin: 0 10px 0 0;
					border: 1px solid #CCC;
					padding: 10px;
					background: #e5e5e5;
				}
				.submenu-tab a {
					text-transform: capitalize;
					text-decoration: none;
					font-weight: 600;
					color: #555;
				}
				.submenu-tab a:focus {
					box-shadow: none;
				}
				li.submenu-tab.active {
					background: none;
				}
				li.submenu-tab.active a {
					color: #000;
				}

				li.paginate_button.page-item {
					float: left;
					margin: 5px;
				}
				#logs-table_length {
					float:left;
					display: inline-block;
				}
				#logs-table_paginate {
					float: right;
					display: inline-block;
				}
				a.paginate_button {
	height: 16px;
	border-color: #ddd;
	background: #f7f7f7;
	color: #a0a5aa;
	margin: 5px;

	
		display: inline-block;
	min-width: 17px;
	border: 1px solid #ccc;
	padding: 3px 5px 7px;
	background: #e5e5e5;
	font-size: 16px;
	line-height: 1;
	font-weight: 400;
	text-align: center;
}
.js .postbox .hndle {
	cursor: auto !important;
}
@import "https://cdnjs.cloudflare.com/ajax/libs/prism/1.15.0/themes/prism.min.css";

			</style>
			
			<?php wp_enqueue_script( 'prism', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.15.0/prism.min.js', array( 'jquery' ), null, true ); ?>

				<h2 class="nav-tab-wrapper">
				<?php

				foreach ( $tabs as $tab ) {
					if ( $tab === $active_tab ) {
						$active_tab_class = 'nav-tab-active';
					} else {
						$active_tab_class = '';
					}

					echo '<a href="?page=apilogpro&#38;tab=' . esc_html( $tab ) . '" class="nav-tab ' . esc_html( $active_tab_class ) . ' nav-tab-' . esc_html( $tab ) . '">' . ucwords( $tab ) . '</a>';
				}
				?>
				</h2>	<div class="wrap">



	<?php

	if ( $active_tab && in_array( $active_tab, $tabs, true ) ) {

		include_once $active_tab . '.php';

	} else {
		echo esc_html_e( 'Sorry you are trying to visit the wrong tab.', 'api-log-pro' );
	}

	?>
	</div>
	</form>

</div>
	<?php

}
