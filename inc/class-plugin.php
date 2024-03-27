<?php
/**
 * Plugin.
 * 
 * @since   1.0.0
 */
class builtpassPlugin {

    /**
     * Loader.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     builtpassLoader       $loader     Registers all hooks.
     */
    protected $loader;

    /**
     * Identifier.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     string          $identifier     The plugin identifier.
     */
    protected $plugin_name;

    /**
     * Version.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     string          $version        The plugin version.
     */
    protected $version;

    /**
     * Construct.
     * 
     * @since   1.0.0
     */
    public function __construct() {

        // Set version.
        $this->version = BUILTPASS_VERSION;

        // Set name.
        $this->plugin_name = BUILTPASS_NAME;

        // Load dependencies.
        $this->load_dependencies();

        // Load private hooks.
        $this->private_hooks();

        // Load public hooks.
        $this->public_hooks();

    }

    /**
     * Load dependencies.
     * 
     * @since   1.0.0
     * @access  private
     */
    private function load_dependencies() {

        // Loader.
        require_once BUILTPASS_PATH . 'inc/class-loader.php';

        // Utilities.
        require_once BUILTPASS_PATH . 'utilities/class-process.php';
        require_once BUILTPASS_PATH . 'utilities/class-mail.php';
        require_once BUILTPASS_PATH . 'utilities/class-keys.php';

        // Private.
        require_once BUILTPASS_PATH . 'private/class-private.php';

        // Public.
        require_once BUILTPASS_PATH . 'public/class-public.php';

        // Initiate loader.
        $this->loader = new builtpassLoader();

    }

    /**
     * Register admin hooks.
     */
    private function private_hooks() {

        // Set new admin.
        $private = new builtpassPrivate( $this->get_name(), $this->get_version() );

        // Add actions.
        $this->loader->add_action( 'admin_menu', $private, 'add_menu_page' );
        $this->loader->add_action( 'admin_enqueue_scripts', $private, 'enqueue' );

    }

    /**
     * Register public hooks.
     */
    private function public_hooks() {

        // Set new public.
        $public = new builtpassPublic( $this->get_name(), $this->get_version() );

        // Add actions.
        $this->loader->add_action( 'user_register', $public, 'set_key', 10, 1 );
        $this->loader->add_action( 'wp_login', $public, 'login_reset', 10, 2 );
        $this->loader->add_action( 'init', $public, 'redirect_timed_reset' );
        $this->loader->add_action( 'wp_logout', $public, 'logout' );

        // Add filters.
        $this->loader->add_filter( 'template_include', $public, 'reset_password_template' );

    }

    /**
     * Run.
     * 
     * @since   1.0.0
     */
    public function run() {

        // Run the loader.
        $this->loader->run();

    }

    /**
     * Get plugin name.
     * 
     * @since   1.0.0
     * @return  string      The plugin name.
     */
    public function get_name() {

        // Return plugin name.
        return $this->plugin_name;

    }

    /**
     * Get plugin version.
     * 
     * @since   1.0.0
     * @return  string      The plugin version.
     */
    public function get_version() {

        // Return plugin version.
        return $this->version;

    }

}