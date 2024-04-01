<?php
/**
 * Password reset form.
 * 
 * @since   1.0.0
 */
get_header();

// Process.
$process = new builtpassProcess();
$process->run( $post, [ 'message' => '', 'redirect' => true ] );

// Before internal.
do_action( 'builtpass_before_internal', get_current_user_id() );

// Reset form. ?>
<div class="woocommerce-form woocommerce-form-reset reset">
    <div class="built-password-reset">
        <form id="built-password-reset-form" method="post">
            <input type="hidden" name="action" value="builtpass_reset_password">
            <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'builtpass_reset_password' ); ?>">
            <input type="password" name="password" placeholder="New password">
            <input type="password" name="confirm_password" placeholder="Confirm new password">
            <button type="submit">Reset password</button>
        </form>
    </div>
</div><?php

// After internal.
do_action( 'builtpass_after_internal', get_current_user_id() );

get_footer();