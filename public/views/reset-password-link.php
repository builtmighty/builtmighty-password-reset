<?php
/**
 * Password reset form.
 * 
 * @since   1.0.0
 */
// Classes.
$process = new builtpassProcess();
$keys    = new builtpassKeys();

// Access.
$access = true;

// Check for required key and user.
if( ! isset( $_GET['key'] ) || ! isset( $_GET['user'] ) ) $access = false;

// Validate key.
if( ! $keys->validate( $_GET['key'], $_GET['user'] ) ) $access = false;

// Check for access.
if( ! $access ) {

    // Redirect.
    wp_redirect( home_url() );
    exit;

}

// Set data.
$data = [
    'message'   => '',
    'redirect'  => true
];

// Reset.
if( isset( $_POST['action'] ) && $_POST['action'] == 'builtpass_reset_password' ) {

    // Check nonce.
    $data = $process->check_nonce( $_POST, $data );
    
    // Check match.
    $data = $process->check_match( $_POST, $data );

    // Check strength.
    $data = $process->check_strength( $_POST, $data );

    // Check for message.
    if( ! empty( $data['message'] ) ) {

        // Display message.
        echo '<div class="builtpass-error">' . $data['message'] . '</div>';

    } else {

        // Save password.
        $process->save_password( $_POST );

    }

    // Check for redirect.
    if( $data['redirect'] ) {

        // Redirect.
        wp_redirect( home_url( '/my-account' ) );
        exit;

    }
    
}

// Header.
get_header();

// Reset form. ?>
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
</div><?php

get_footer();