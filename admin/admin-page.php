<?php
/**
 * API Log Pro Admin Page.
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', 'api_log_pro_menu' );

/**
 * Wp_queue_menu function.
 *
 * @access public
 */
function api_log_pro_menu() {
	add_menu_page( 'API Log Pro', 'API Log Pro', 'manage_options', 'apilogpro', 'api_log_pro_page', 'dashicons-cloud' );
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



	$active_tab = api_log_pro_menu_active_tab();

	$tabs = array( 'logs', 'support' );

	?>
	<div class="wrap settings">

		<form method="post" action="options.php">
			<h1><?php esc_html_e( 'API Log Pro -', 'api-log-pro' ); ?> <?php echo esc_html( ucwords( $active_tab ) ); ?></h1>

			<?php wp_enqueue_style( 'api-log-pro-admin' ); ?>


				<h2 class="nav-tab-wrapper">
				<?php

				foreach ( $tabs as $tab ) {
					if ( $tab === $active_tab ) {
						$active_tab_class = 'nav-tab-active';
					} else {
						$active_tab_class = '';
					}

					echo '<a href="?page=apilogpro&#38;tab=' . esc_html( $tab ) . '" class="nav-tab ' . esc_html( $active_tab_class ) . ' nav-tab-' . esc_html( $tab ) . '">' . esc_html( ucwords( $tab ) ) . '</a>';
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
