<?php
/**
 * Password reset form.
 * 
 * @since   1.0.0
 */
// Classes.
$process = new builtpassProcess();
$keys    = new builtpassKeys();
$help    = new builtpassHelper();

// Access.
$access = true;

// Check if user is logged in.
if( is_user_logged_in() ) {

    // Redirect.
    wp_redirect( $help->redirect_url() );
    exit;

}

// Check for required key and user.
if( ! isset( $_GET['key'] ) || ! isset( $_GET['user'] ) ) $access = false;

// Validate key.
if( ! $keys->validate( $_GET['key'], $_GET['user'] ) ) $access = false;

// Check for access.
if( ! $access ) {

    // Redirect.
    wp_redirect( home_url( '/?password-reset-expired=true&user=' . $_GET['user'] ) );
    exit;

}

// Enqueue Main Script.
wp_enqueue_script( 'builtpass-main-js' );

// Process.
$process->run( $_POST, [ 'message' => '', 'redirect' => true ] );

// Header.
get_header();

// Before external.
do_action( 'builtpass_before_external', $_GET['user'] );

$reset_password_template = 'myaccount/form-reset-password.php';

// Check if WooCommerce Template Exists.
if (
    function_exists( 'wc_locate_template' ) &&
    wc_locate_template( $reset_password_template )
) {
    // Get WooCommerce Reset Password Form
    wc_get_template( $reset_password_template, [
        'user'  => $_GET['user'],
        'key'   => $_GET['key'],
        'login' => $keys->get_login( $_GET['user'] ),
    ]);
} else {
    // Reset form.
    ?>
    <div class="woocommerce-form woocommerce-form-reset reset">
        <div class="built-password-reset">
            <form id="built-password-reset-form" method="post">
                <input type="hidden" name="action" value="builtpass_reset_password">
                <input type="hidden" name="user_id" value="<?php echo $_GET['user']; ?>">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'builtpass_reset_password' ); ?>">
                <input type="password" name="password" placeholder="New password">
                <input type="password" name="confirm_password" placeholder="Confirm new password">
                <button type="submit">Reset password</button>
            </form>
        </div>
    </div>
    <?php
}

// After external.
do_action( 'builtpass_after_external', $_GET['user'] );

get_footer();