<?php
/**
 * Private.
 * 
 * @since   1.0.0
 */
class builtpassPrivate {

    /**
     * Plugin name.
     */
    private $plugin_name;

    /**
     * Plugin version.
     */
    private $plugin_version;

    /**
     * Construct.
     * 
     * @since   1.0.0
     * @param   string      $plugin_name        The name of the plugin.
     * @param   string      $plugin_version     The version of the plugin.
     */
    public function __construct( $plugin_name, $version ) {

        // Set plugin name.
        $this->plugin_name = $plugin_name;

        // Set plugin version.
        $this->plugin_version = $version;

    }

    /**
     * Add menu page.
     * 
     * @since   1.0.0
     */
    public function add_menu_page() {

        // Add sub-menu.
        add_submenu_page(
            'options-general.php',
            'Password Reset',
            'Password Reset',
            'manage_options',
            'builtpass-password-reset',
            [ $this, 'password_reset' ]
        );

    }

    /**
     * Password reset.
     * 
     * @since   1.0.0
     */
    public function password_reset() {

        // Check for user.
        if( ! is_user_logged_in() ) return;

        // Check user capability.
        if( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Settings. ?>
        <div class="form-wrap builtpass-form">
            <h2>Password Reset</h2><?php

            // Save.
            $this->save( $_POST ); ?>

            <form method="post">
                <div class="builtpass-field">
                    <div class="builtpass-label">
                        <label for="builtpass_reset_password">Reset Option</label>
                    </div>
                    <div class="builtpass-input">
                        <select name="builtpass_reset_password" id="builtpass_reset_password">
                            <option value="disable">Disabled</option><?php

                            // Loop through options.
                            foreach( $this->get_options() as $option ) {

                                // Prepend to option.
                                $option = 'require_' . $option;

                                // Set selected.
                                $selected = ( get_option( 'builtpass_reset_password' ) == $option ) ? 'selected' : ''; ?>

                                <option value="<?php echo $option; ?>" <?php echo $selected; ?>><?php echo ucwords( str_replace( '_', ' ', $option ) ); ?></option><?php

                            } ?>

                        </select>
                    </div>
                </div>
                <div class="builtpass-field">
                    <button type="submit" class="button button-primary">Save</button>
                </div>
            </form>
        </div>
        <style>.builtpass-field{display:flex;align-items:center}.builtpass-label label{font-weight:700;margin-right:15px}.builtpass-field button[type=submit]{margin:15px 0 0}.wp-core-ui .notice.is-dismissible{margin:0 0 15px}</style><?php

    }

    /**
     * Save.
     * 
     * @since   1.0.0
     */
    public function save( $data ) {

        // Save.
        if( ! empty( $data ) && isset( $data['builtpass_reset_password'] ) ) {

            // Update.
            update_option( 'builtpass_reset_password', $data['builtpass_reset_password'] );

            // Message.
            echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';

        }

    }

    /**
     * Get options.
     * 
     * @since   1.0.0
     */
    public function get_options() {

        // Set options.
        $options = [
            'once',
            '90_days',
            '180_days',
            '365_days'
        ];

        // Filter.
        $options = apply_filters( 'builtpass_reset_options', $options );

        // Return.
        return $options;

    }

}
