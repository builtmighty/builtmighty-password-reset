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
        if( ! current_user_can( 'manage_options' ) ) return;

        // Settings. ?>
        <div class="form-wrap builtpass-form">
            <div class="builtpass-form-inner">
                <h2>🔑 Password Reset</h2><?php

                // Save.
                $this->save( $_POST ); ?>

                <div class="builtpass-form-nav">
                    <ul>
                        <li><span class="builtpass-nav tab-active" data-id="timed">Timed Reset</span></li>
                        <li><span class="builtpass-nav" data-id="bulk">Bulk Reset</span></li>
                    </ul>
                </div>
                <div class="builtpass-form-tabs">
                    <div id="timed" class="builtpass-form-tab">
                        <h3>Timed Reset</h3>
                        <p>Require users to update their passwords, automatically, after a defined period of time.</p>
                        <form method="post"><?php

                            // Loop through timed fields.
                            foreach( $this->get_timed_fields() as $key => $field ) {

                                // Output. 
                                echo $this->get_field( $key, $field );

                            } ?>

                        </form>
                    </div>
                    <div id="bulk" class="builtpass-form-tab" style="display:none">
                        <h3>Bulk Reset</h3>
                        <p>Require users to update their passwords if they are a specific role. If a user attempts to login, they will be logged out and will receive a reset password link in their email.</p><?php

                        // Check if reset is set.
                        if( ! empty( get_option( 'builtpass_bulk_reset' ) ) ) {

                            // Output. ?>
                            <div class="builtpass-bulk-resets">
                                <div class="builtpass-bulk-reset builtpass-bulk-reset-head">
                                    <div class="builtpass-bulk-reset-role">
                                        Role
                                    </div>
                                    <div class="builtpass-bulk-reset-time">
                                        Required Reset After Login
                                    </div>
                                    <?php
                                    $interval_option = get_option( 'builtpass_bulk_exclusion_interval', 'none' );
                                    if ( $interval_option !== 'none') :
                                        ?>
                                        <div class="builtpass-bulk-exlusion-interval">
                                            Unless Registered After
                                        </div>
                                        <?php

                                        

                                    endif;
                                    ?>
                                </div>
                                <?php

                                // Loop.
                                foreach( get_option( 'builtpass_bulk_reset' ) as $role => $time ) {

                                    // Output. ?>
                                    <div class="builtpass-bulk-reset">
                                        <div class="builtpass-bulk-reset-role">
                                            <?php echo ucwords( str_replace( '_', ' ', get_role( $role )->name ) ); ?>
                                        </div>
                                        <div class="builtpass-bulk-reset-time">
                                            <?php echo date( 'F j, Y h:ia', $time ); ?>
                                        </div>
                                        <?php
                                        if ( $interval_option !== 'none') :
                                            $converted_interval = builtpassHelper::convert_interval_to_datetime( $interval_option );

                                            // Subtract the interval from the current time.
                                            $cutoff_time = strtotime( '-' . str_replace('_', ' ', $interval_option), $time );

                                            // Format the cutoff time.
                                            $formatted_cutoff_time = date( 'F j, Y h:ia', $cutoff_time );
                                            ?>
                                            <div class="builtpass-bulk-exlusion-interval">
                                                <?php echo $formatted_cutoff_time; ?>
                                            </div>
                                            <?php
                                        endif; // endif ( $interval_option !== 'none') :
                                        ?>
                                    </div><?php

                                } ?>

                            </div><?php

                        } ?>
                        <form method="post"><?php

                            // Loop through bulk fields.
                            foreach( $this->get_bulk_fields() as $key => $field ) {

                                // Output. 
                                echo $this->get_field( $key, $field );

                            } ?>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <style>.builtpass-field{display:flex;align-items:center;gap:1em;flex-wrap:wrap;}.builtpass-label label{font-weight:700;margin-right:15px}.wp-core-ui .notice.is-dismissible{margin:0 0 15px}</style><?php

    }

    /**
     * Save.
     * 
     * @since   1.0.0
     */
    public function save( $data ) {

        // Save.
        if( ! empty( $data ) ) {

            // Check save type.
            if( isset( $data['builtpass_timed_save'] ) ) {

                // Loop through fields.
                foreach( $this->get_timed_fields() as $key => $field ) {

                    // Check if set.
                    if( isset( $data[ $key ] ) ) {

                        // Update.
                        update_option( $key, $data[ $key ] );

                    } else {

                        // Delete.
                        delete_option( $key );

                    }

                }

                // Message.
                echo '<div class="notice notice-success is-dismissible"><p>Timed reset settings saved.</p></div>';

            } elseif( isset( $data['builtpass_bulk_save'] ) ) {

                // Save textareas.
                foreach( $this->get_bulk_fields() as $key => $field ) {

                    // Check type.
                    if( $field['type'] != 'textarea' ) continue;

                    // Check if set.
                    if( isset( $data[ $key ] ) ) {

                        // Update.
                        update_option( $key, $data[ $key ] );

                    } else {

                        // Delete.
                        delete_option( $key );

                    }

                }

                // Get/set save data.
                $save_data = ( ! empty( get_option( 'builtpass_bulk_reset' ) ) ) ? get_option( 'builtpass_bulk_reset' ) : [];

                // Loop through roles.
                foreach( $data['builtpass_bulk_roles'] as $role ) {

                    // Set.
                    $save_data[$role] = time();

                }

                // Update.
                update_option( 'builtpass_bulk_reset', $save_data );

                // Message.
                echo '<div class="notice notice-success is-dismissible"><p>Bulk reset settings saved.</p></div>';

            } elseif( isset( $data['builtpass_bulk_clear'] ) ) {

                // Delete.
                delete_option( 'builtpass_bulk_reset' );

                // Message.
                echo '<div class="notice notice-success is-dismissible"><p>Bulk reset for roles cleared.</p></div>';

            }

            // Save exclusion interval.
            if ( isset( $data['builtpass_bulk_exclusion_interval'] ) )
                update_option( 'builtpass_bulk_exclusion_interval', sanitize_text_field( $data['builtpass_bulk_exclusion_interval'] ) );

        }

    }

    /**
     * Get field.
     * 
     * @since   1.0.0
     */
    public function get_field( $key, $field ) {

        // Get value.
        $value = get_option( $key );

        // Start output buffering.
        ob_start();

        // Field. ?>
        <div class="builtpass-field builtpass-field-<?php echo $field['type']; ?>"><?php

            // Check for a label.
            if( $field['type'] != 'submit' ) { ?>

                <label for="<?php echo $key; ?>" class="builtpass-label">
                    <?php echo $field['label']; ?>
                </label><?php

            }

            // Select.
            if( $field['type'] == 'select' ) {

                // Output select. ?>
                <select name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php

                    // Loop through options.
                    foreach( $field['options'] as $option_id => $option ) {

                        // Set.
                        $selected = ( $value == $option_id ) ? ' selected' : '';

                        // Output option.
                        echo '<option value="' . $option_id . '"' . $selected . '>' . $option . '</option>';

                    } ?>

                </select><?php

            } elseif( $field['type'] == 'checkbox' ) {

                // Check if options.
                if( ! empty( $field['options'] ) ) {

                    // Loop through options.
                    foreach( $field['options'] as $option_id => $option ) {

                        // Set.
                        $checked = ( in_array( $option_id, (array)$value ) ) ? ' checked' : '';

                        // Output checkbox.?> 
                        <div class="builtpass-checkbox-field"><input type="checkbox" name="<?php echo $key; ?>[]" id="<?php echo $key; ?>-<?php echo $option_id; ?>" value="<?php echo $option_id; ?>"<?php echo $checked; ?> /><span><?php echo $option; ?></span></div><?php

                    }

                } else {

                    // Output checkbox. ?>
                    <input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="1" <?php checked( $value, 1 ); ?> /><?php

                }

            } elseif( in_array( $field['type'], [ 'text', 'number', 'hidden', 'password' ] ) ) {

                // Output input. ?>
                <input type="<?php echo $field['type']; ?>" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>" /><?php

            } elseif( $field['type'] == 'textarea' ) {

                // Output textarea. ?>
                <textarea name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo $value; ?></textarea><?php
            
            } elseif( $field['type'] == 'submit' ) {

                // Output class.
                $class = ( isset( $field['class'] ) ) ? ' class="' . $field['class'] . '"' : '';

                // Output submit. ?>
                <button type="submit" name="<?php echo $key; ?>" <?php echo $class; ?>><?php echo $field['label']; ?></button><?php

            }
            
            // Check for a description.
            if( isset( $field['description'] ) ) :
                ?>
                <p class="description">
                    <?php echo $field['description']; ?>
                </p>
                <?php
            endif;
            ?>

        </div><?php

        // Return.
        return ob_get_clean();

    }

    /**
     * Get timed fields.
     * 
     * @since   1.0.0
     */
    public function get_timed_fields() {

        // Set fields.
        $fields = [
            'builtpass_timed_reset'     => [
                'label'     => 'Reset',
                'type'      => 'select',
                'options'   => $this->get_times()
            ],
            'builtpass_timed_roles'     => [
                'label'     => 'Roles',
                'type'      => 'checkbox',
                'options'   => $this->get_roles()
            ],
            'builtpass_timed_save'      => [
                'label'     => 'Save',
                'type'      => 'submit',
                'class'     => 'button button-primary',
            ]
        ];

        // Filter.
        $fields = apply_filters( 'builtpass_timed_fields', $fields );

        // Return.
        return $fields;

    }

    /**
     * Get bulk fields.
     * 
     * @since   1.0.0
     */
    public function get_bulk_fields() {

        // Set fields.
        $fields = [
            'builtpass_bulk_exclusion_interval' => [
                'label'       => __( 'Exclusion Interval', BUILTPASS_DOMAIN ),
                'description' => __( 'If a user has reset their password within this interval, they will not be required to reset their password again.', BUILTPASS_DOMAIN ),
                'type'        => 'select',
                'options'     => $this->get_exclusion_intervals()
            ],
            'builtpass_bulk_roles'     => [
                'label'     => 'Roles',
                'type'      => 'checkbox',
                'options'   => $this->get_roles()
            ],
            'builtpass_bulk_page'      => [
                'label'     => 'Page Content',
                'type'      => 'textarea',
            ],
            'builtpass_bulk_email'     => [
                'label'     => 'Email Content',
                'type'      => 'textarea',
            ],
            'builtpass_bulk_save'      => [
                'label'     => 'Save',
                'type'      => 'submit',
                'class'     => 'button button-primary',
            ],
            'builtpass_bulk_clear'      => [
                'label'     => 'Clear Reset',
                'type'      => 'submit',
                'class'     => 'button button-secondary',
            ],
        ];

        // Filter.
        $fields = apply_filters( 'builtpass_bulk_fields', $fields );

        // Return.
        return $fields;

    }


    /**
     * Get exclusion intervals.
     * 
     * @return  array - The exclusion intervals.
     * 
     * @since   1.1.0
     */
    public function get_exclusion_intervals() {

        // Set intervals.
        $intervals = [
            'none'      => __( 'None', BUILTPASS_DOMAIN ),
            '6_months'  => __( '6 Months', BUILTPASS_DOMAIN ),
            '1_year'    => __( '1 Year', BUILTPASS_DOMAIN ),
        ];

        /**
         * Filter the exclusion intervals.
         * 
         * @since   1.1.0
         * @param   array   $intervals   The exclusion intervals.
         */
        $intervals = apply_filters( 'builtpass_exclusion_intervals', $intervals );

        // Return.
        return $intervals;
    }

    /**
     * Get times.
     * 
     * @since   1.0.0
     */
    public function get_times() {

        // Set times.
        $times = [
            'disabled'  => 'Disabled',
            '90'        => 'Every 90 Days',
            '180'       => 'Every 180 Days',
            '365'       => 'Every Year',
        ];

        // Filter.
        $times = apply_filters( 'builtpass_reset_times', $times );

        // Return.
        return $times;

    }

    /**
     * Get user roles for the site.
     * 
     * @since   1.0.0
     */
    public function get_roles() {

        // Get roles.
        $roles = get_editable_roles();

        // Set.
        $options = [];

        // Loop through roles.
        foreach( $roles as $role => $details ) {

            // Set.
            $options[ $role ] = ucwords( $details['name'] );

        }

        // Remove admin.
        unset( $options['administrator'] );

        // Return.
        return $options;

    }

    /**
     * Enqueue.
     * 
     * @since   1.0.0
     */
    public function enqueue() {

        // Load on admin.
        if( $_GET['page'] == 'builtpass-password-reset' ) {

            // CSS.
            wp_enqueue_style( 'builtpass-admin', BUILTPASS_URI . 'private/assets/css/admin.css', [], BUILTPASS_VERSION );

            // JS.
            wp_enqueue_script( 'builtpass-admin', BUILTPASS_URI . 'private/assets/js/admin.js', [ 'jquery' ], BUILTPASS_VERSION, true );

        }

    }

}
