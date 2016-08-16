<?php
/**
 * Plugin Name:         EJO Contactadvertenties
 * Plugin URI:          http://github.com/ejoweb/ejo-contactadvertenties
 * Description:         EJO Contactadvertenties
 * Version:             0.3.1
 * Author:              Erik Joling
 * Author URI:          https://www.ejoweb.nl/
 * Text Domain:         ejo-contactads
 * Domain Path:         /languages
 *
 * GitHub Plugin URI:   https://github.com/EJOweb/ejo-contactadvertenties
 * GitHub Branch:       master
 *
 * Minimum PHP version: 5.3.0
 */

/**
 *
 */
final class EJO_Contactads_Plugin
{
	//* Slug of this plugin
    const SLUG = 'ejo-contactadvertenties';

    //* Version number of this plugin
    const VERSION = '0.3.1';

	//* Stores the directory path for this plugin.
    public static $dir;

    //* Stores the directory URI for this plugin.
    public static $uri;

	/* Holds the instance of this class. */
	private static $_instance = null;

	/* Return the class instance. */
	public static function init() {
		if ( !self::$_instance )
			self::$_instance = new self;
		return self::$_instance;
	}

	//* No clones please!
    protected function __clone() {}

	/* Plugin setup. */
	protected function __construct() 
	{
		//* Setup
        // add_action( 'plugins_loaded', array( $this, 'setup' ), 1 );
        self::setup();

		/* Register Widget */
		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		/* Add activation hook */
        register_activation_hook( __FILE__, array( 'EJO_Contactads', 'on_plugin_activation') );

        /* Add uninstall hook */
        // register_uninstall_hook( __FILE__, array( 'EJO_Contactads', 'on_plugin_uninstall') );
        register_deactivation_hook( __FILE__, array( 'EJO_Contactads', 'on_plugin_deactivation') );
	}

	//* Defines the directory path and URI for the plugin.
    private static function setup() 
    {
    	//* Set plugin dir and uri
        self::$dir = plugin_dir_path( __FILE__ ); // with trailing slash
        self::$uri = plugin_dir_url( __FILE__ ); // with trailing slash

        //* Load the translation for the plugin
        load_plugin_textdomain( self::SLUG, false, self::SLUG.'/languages' );

        /* Load classes */
		require_once( self::$dir . 'includes/class-contactads.php' );
		require_once( self::$dir . 'includes/class-settings.php' );
		require_once( self::$dir . 'includes/class-widget.php' );

		/* Settings */
		EJO_Contactads::init();

		/* Settings */
		EJO_Contactads_Settings::init();
    }

	/* Fire when activating this plugin */
    public static function on_plugin_activation()
    {
    	//* Flush rules to process permalinks of custom post type
    	flush_rewrite_rules();

    	//* Add contactads capabilities
    	EJO_Contactads::add_caps();	
    }

    /* Fire when deactivating this plugin */
    public static function on_plugin_deactivation()
    {
    	//* Flush rules to process permalinks of custom post type
    	flush_rewrite_rules();

    	//* Remove contactads capabilities
    	EJO_Contactads::remove_caps();
    }

	/* Register Widget */
	public function register_widget() 
	{ 
	    register_widget( 'EJO_Contactads_Widget' ); 
	}
}

/* Contactadvertenties */
EJO_Contactads_Plugin::init();

