<?php
/**
 * API Log Pro Admin - Log Details Page.
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Get Log ID.
$log_id = filter_input( INPUT_GET, 'log_id' ) ?? '';

$tab = filter_input( INPUT_GET, 'tab' ) ?? '';

$api_log_pro = new API_Log_Pro();


$log = $api_log_pro->get_log( $log_id );




if ( empty( $log ) || is_wp_error( $log ) ) {
	esc_html_e( 'Sorry no Log exists with that ID.', 'api-log-pro' );
} else {

	wp_enqueue_script( 'highlight' );
	wp_enqueue_style( 'highlight-atom-light-one' );
	wp_add_inline_script( 'highlight', 'hljs.initHighlightingOnLoad();' );

	// Get Log Meta.
	$log_meta = $api_log_pro->get_all_log_meta( $log_id );

	// Get WordPress Time Zone Settings.
	$gmt_offset = get_option( 'gmt_offset' ) ?? 0;



	?>

<div class="wrap wp-rest-api-log-entry">
		<a href="<?php echo esc_url( '/wp-admin/admin.php?page=apilogpro&tab=incoming' ); ?>" class="button"><?php esc_html_e( 'Return to Logs', 'api-log-pro' ); ?></a>
	<?php
	echo '<a href="' . esc_url( add_query_arg('action', 'delete_log', admin_url('admin-post.php') ) ) . '&log_id=' . $log_id . '" class="button button-primary">Delete Log</a>';
		// TODO: Delete Button
		// echo '<input class="button button-link-delete" type="submit" name="delete_log" id="delete-log-'. $log_id .'" value="'. __( 'Delete Log', 'rest-api-log' ) . '" />'; !
	?>

<div id="poststuff">
<div class="postbox request-headers">
	<h3 class="hndle"><span><?php esc_html_e( 'Details', 'api-log-pro' ); ?></span></h3>

	<div class="inside">
		<ul>
			<li><strong><?php esc_html_e( 'Log ID:', 'api-log-pro' ); ?></strong> <?php echo esc_html( $log->id ); ?></li>
			<li><strong><?php esc_html_e( 'Path:', 'api-log-pro' ); ?></strong>  <a href="<?php echo esc_html( $log->path ); ?>" target="_blank"><?php echo esc_html( $log->path ); ?></a></li>
			<li><strong><?php esc_html_e( 'Date:', 'api-log-pro' ); ?></strong>
			<?php echo esc_attr( date( 'F j, Y, g:i A T', current_time( strtotime( $log->requested_at ), $gmt_offset ) ) ); ?>
			( <?php echo esc_attr( human_time_diff( current_time( strtotime( $log->requested_at ), $gmt_offset ), current_time( 'timestamp', $gmt_offset ) ) . esc_html( ' ago', 'api-log-pro' ) ); ?>)
			</li>
			<li><strong><?php esc_html_e( 'Method:', 'api-log-pro' ); ?></strong> <?php echo esc_html( $log->method ); ?></li>
			<li><strong><?php esc_html_e( 'Status:', 'api-log-pro' ); ?></strong> <?php echo esc_html( $log->status ); ?></li>
		</ul>
	</div>
</div>

<div class="postbox request-headers">
	<h3 class="hndle"><span><?php esc_html_e( 'Response', 'api-log-pro' ); ?></span></h3>

	<div class="inside" style="overflow: scroll;">

		<?php $response_body = json_decode( $log->response ) ?? ''; ?>

		<pre><code><?php echo esc_html( wp_json_encode( $response_body, JSON_PRETTY_PRINT ) ); ?></code></pre>
	</div>
</div>

<div class="postbox request-headers">
	<h3 class="hndle"><span><?php esc_html_e( 'Request Headers', 'api-log-pro' ); ?></span></h3>

	<div class="inside" style="overflow: scroll;">
		<?php $request_headers = json_decode( $log->request_headers ) ?? ''; ?>
		<pre><code><?php echo esc_html( wp_json_encode( $request_headers, JSON_PRETTY_PRINT ) ); ?></code></pre>
	</div>
</div>


<div class="postbox request-headers">
	<h3 class="hndle"><span><?php esc_html_e( 'Response Headers', 'api-log-pro' ); ?></span></h3>

	<div class="inside" style="overflow: scroll;">
		<?php $response_headers = json_decode( $log->response_headers ) ?? ''; ?>
		<pre><code><?php echo esc_html( wp_json_encode( $response_headers, JSON_PRETTY_PRINT ) ); ?></code></pre>
	</div>
</div>

<div class="postbox request-headers">
	<h3 class="hndle"><span><?php esc_html_e( 'Meta Data', 'api-log-pro' ); ?></span></h3>

	<div class="inside">
		<ul>
		<?php
		foreach ( $log_meta as $meta ) {

			$clean_meta_key = ucwords( str_replace( '_', ' ', $meta->meta_key ) );

			echo '<li><strong>' . esc_html( $clean_meta_key ) . ':</strong> ' . esc_html( $meta->meta_value ) . '</li>';
		}
		?>
		</ul>
	</div>
</div>



</div>
</div>


<?php } ?>
