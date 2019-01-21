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

$api_log_pro = new API_Log_Pro();

$log = $api_log_pro->get_log( $log_id );

if ( empty( $log ) || is_wp_error( $log ) ) {
	esc_html_e( 'Sorry no Log exists with that ID.', 'api-log-pro' );
} else {


	$log_meta = $api_log_pro->get_all_log_meta( $log_id );

	// Get WordPress Time Zone Settings.
	$gmt_offset = get_option( 'gmt_offset' ) ?? 0;


	?>

<div class="wrap wp-rest-api-log-entry">

	<a href="<?php echo esc_url( '/wp-admin/admin.php?page=apilogpro' ); ?>"><?php esc_html_e( 'Return to Logs', 'api-log-pro' ); ?></a>

<div id="poststuff">
<div class="postbox request-headers">
	<h3 class="hndle"><span><?php esc_html_e( 'Details', 'api-log-pro' ); ?></span></h3>

	<div class="inside">
		<ul>
			<li><strong><?php esc_html_e( 'Log ID:', 'api-log-pro' ); ?></strong> <?php echo esc_html( $log->id ); ?></li>
			<li><strong><?php esc_html_e( 'Path:', 'api-log-pro' ); ?></strong>  <?php echo esc_html( $log->path ); ?></li>
			<li><strong><?php esc_html_e( 'Date:', 'api-log-pro' ); ?></strong>
			<?php echo date( 'F j, Y, g:i A T', current_time( time( esc_html( $log->requested_at ) ), $gmt_offset ) ); ?>
			( <?php echo human_time_diff( current_time( time( esc_html( $log->requested_at ) ), $gmt_offset ), current_time( 'timestamp', $gmt_offset ) ) . __( ' ago', 'api-log-pro' ); ?>)
			</li>
			<li><strong><?php esc_html_e( 'Method:', 'api-log-pro' ); ?></strong> <?php echo esc_html( $log->method ); ?></li>
			<li><strong><?php esc_html_e( 'Status:', 'api-log-pro' ); ?></strong> <?php echo esc_html( $log->status ); ?></li>
		</ul>
	</div>
</div>

<div class="postbox request-headers">
	<h3 class="hndle"><span><?php esc_html_e( 'Response', 'api-log-pro' ); ?></span></h3>

	<div class="inside">
		<pre style="overflow:scroll;"><code class="language-json"><?php echo print_r( json_decode( $log->response ), true ); ?></code></pre>
	</div>
</div>

<div class="postbox request-headers">
	<h3 class="hndle"><span><?php esc_html_e( 'Request Headers', 'api-log-pro' ); ?></span></h3>

	<div class="inside">
		<pre><code><?php echo print_r( json_decode( $log->request_headers ), true ); ?></code></pre>
	</div>
</div>


<div class="postbox request-headers">
	<h3 class="hndle"><span><?php esc_html_e( 'Response Headers', 'api-log-pro' ); ?></span></h3>

	<div class="inside">
		<pre><code><?php echo print_r( json_decode( $log->response_headers ), true ); ?></code></pre>
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
