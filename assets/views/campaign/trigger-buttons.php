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
<div id="n8n-mt-trigger-controls" class="n8n-mt-trigger-controls">
    <p>
        <button
            type="button"
            class="button button-secondary n8n-mt-trigger-button"
            data-mode="test"
            <?php disabled( ! $has_test ); ?>
        >
            <?php echo esc_html__( 'Send test', 'n8n-marketing-trigger' ); ?>
        </button>
    </p>
    <p>
        <button
            type="button"
            class="button button-primary n8n-mt-trigger-button"
            data-mode="production"
            <?php disabled( ! $has_production ); ?>
        >
            <?php echo esc_html__( 'Send', 'n8n-marketing-trigger' ); ?>
        </button>
    </p>
    <p id="n8n-mt-trigger-feedback" aria-live="polite"></p>
</div>
<?php endif; ?>
