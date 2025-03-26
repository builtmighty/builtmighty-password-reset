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


$reset_password_template = 'myaccount/form-reset-password.php';

$user_id = get_current_user_id();

// Check if WooCommerce Template Exists.
if (
    function_exists( 'wc_locate_template' ) &&
    wc_locate_template( $reset_password_template )
) {
    $keys = isset( $keys ) ? $keys : new builtpassKeys();
    // Get WooCommerce Reset Password Form
    wc_get_template( $reset_password_template, [
        'user'  => $user_id,
        'key'   => $keys->get_key( $user_id ),
        'login' => $keys->get_login( $user_id ),
    ]);
} else {
    // Reset form.
    ?>
    <div class="woocommerce-form woocommerce-form-reset reset">
        <div class="built-password-reset">
            <form id="built-password-reset-form" method="post">
                <input type="hidden" name="action" value="builtpass_reset_password">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'builtpass_reset_password' ); ?>">
                <input type="password" name="password" placeholder="New password">
                <input type="password" name="confirm_password" placeholder="Confirm new password">
                <button type="submit">Reset password</button>
            </form>
        </div>
    </div>
    <?php
}

// After internal.
do_action( 'builtpass_after_internal', get_current_user_id() );

get_footer();