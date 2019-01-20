<?php

$log_id = filter_input( INPUT_GET, 'log_id' );


$api_log_pro = new API_Log_Pro();

$log = $api_log_pro->get_log( $log_id );

if ( empty( $log ) || is_wp_error( $log ) ) {
	echo 'Sorry no Log exists with that ID.';
} else {


	$log_meta = $api_log_pro->get_all_log_meta( $log_id );


	?>

<div class="wrap wp-rest-api-log-entry">
<div id="poststuff">
<div class="postbox request-headers">
	<h3 class="hndle"><span>Details</span></h3>

	<div class="inside">
		<ul>
			<li>Date: <?php echo $log->requested_at; ?></li>
			<li>Source: WP REST API</li>
			<li>Method: <?php echo $log->method; ?></li>
			<li>Status: <?php echo $log->status; ?></li>
		</ul>
	</div>
</div>

<div class="postbox request-headers">
	<h3 class="hndle"><span>Response</span></h3>

	<div class="inside">
		<?php echo json_decode( wp_json_encode( $log->response ) ); ?>
	</div>
</div>

<div class="postbox request-headers">
	<h3 class="hndle"><span>Request Headers</span></h3>

	<div class="inside">
		<?php echo json_decode( wp_json_encode( $log->request_headers ) ); ?>
	</div>
</div>


<div class="postbox request-headers">
	<h3 class="hndle"><span>Response Headers</span></h3>

	<div class="inside">
		<?php echo json_decode( wp_json_encode( $log->response_headers ) ); ?>
	</div>
</div>

<div class="postbox request-headers">
	<h3 class="hndle"><span>Meta Data</span></h3>

	<div class="inside">
		<ul>
		<?php
			// var_dump($log_meta);
		foreach ( $log_meta as $meta ) {
			echo '<li>' . $meta->meta_key . ': ' . $meta->meta_value . '</li>';
		}
		?>
		</ul>
	</div>
</div>



</div>
</div>

<?php } ?>
