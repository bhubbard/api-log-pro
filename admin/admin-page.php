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
	return isset( $active_tab ) ? $active_tab : 'companies';
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

	$tabs = array( 'logs', 'settings', 'support' );

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
			</style>
				<h2 class="nav-tab-wrapper">
				<?php

				foreach ( $tabs as $tab ) {
					if ( $tab === $active_tab ) {
						$active_tab_class = 'nav-tab-active';
					} else {
						$active_tab_class = '';
					}
						echo '<a href="?page=apilogpro&#38;tab=' . $tab . '" class="nav-tab ' . $active_tab_class . ' nav-tab-' . $tab . '">' . ucwords( $tab ) . '</a>';
				}
				?>
				</h2>	<div class="wrap">



	<?php

	if ( $active_tab ) {

		include_once $active_tab . '.php';

	} else {
		echo 'Coming Soon.';
	}
	?>
	</div>
	</form>

</div><?php
}
?>
