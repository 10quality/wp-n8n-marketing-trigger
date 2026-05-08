<?php
/**
 * Payload example view.
 *
 * @package n8n-marketing-trigger
 */
?>
<h2><?php echo esc_html__( 'Example', 'n8n-marketing-trigger' ); ?></h2>
<pre style="background:#111111;color:#f5f5f5;padding:16px;border-radius:8px;overflow:auto;line-height:1.5;"><?php echo esc_html( wp_json_encode( $payload, JSON_PRETTY_PRINT ) ); ?></pre>
