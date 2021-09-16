<?php
/**
 * API Log Pro Admin - Settings.
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


?>

<form method="post" action="<?php echo admin_url( 'admin.php' ); ?>">

	<p>Checkbox to enable incoming</p>
	<p>Input box to set cron clear time. Default 15 days.</p>

	<p>Checkbox to enable outgoing</p>
	<p>Input box to set cron clear time. Default 15 days.</p>

<button class="button" name="delete_logs">Delete All Logs</button>
<button class="button" name="delete_incoming_logs">Delete Incoming Logs</button>
<button class="button" name="delete_outgoing_logs">Delete Outgoing Logs</button>

	<input type="hidden" name="action" value="" />
  	<input type="submit" value="Submit" />

</form>

<?php



var_dump( $_POST );
