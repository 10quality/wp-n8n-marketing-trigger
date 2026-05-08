<?php
/**
 * Campaign trigger buttons view.
 *
 * @package n8n-marketing-trigger
 */
?>
<?php if ( ! $post_id ) : ?>
<p><?php echo esc_html__( 'Save this campaign before sending triggers.', 'n8n-marketing-trigger' ); ?></p>
<?php else : ?>
<p>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'n8n_mt_send_campaign', 'n8n_mt_nonce' ); ?>
        <input type="hidden" name="action" value="n8n_mt_send_test">
        <input type="hidden" name="campaign_id" value="<?php echo esc_attr( $post_id ); ?>">
        <?php submit_button( __( 'Send test', 'n8n-marketing-trigger' ), 'secondary', 'submit', false, $has_test ? [] : [ 'disabled' => 'disabled' ] ); ?>
    </form>
</p>
<p>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'n8n_mt_send_campaign', 'n8n_mt_nonce' ); ?>
        <input type="hidden" name="action" value="n8n_mt_send_campaign">
        <input type="hidden" name="campaign_id" value="<?php echo esc_attr( $post_id ); ?>">
        <?php submit_button( __( 'Send', 'n8n-marketing-trigger' ), 'primary', 'submit', false, $has_production ? [] : [ 'disabled' => 'disabled' ] ); ?>
    </form>
</p>
<?php endif; ?>