<?php
/**
 * API Log Pro Admin Support Page.
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

echo '<p>';
esc_html_e( 'At this time for support please create an issue on github.', 'api-log-pro' );
echo '<br /><br /><a class="button" href="https://github.com/hubbardlabs/api-log-pro/issues">Submit Issue</a>';
echo '</p>';
