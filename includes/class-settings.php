<?php

/**
 * Class Contactads settings.
 */
class EJO_Contactads_Settings 
{
	/* Holds the instance of this class. */
	private static $_instance = null;

	/* Returns the instance. */
	public static function init() {
		if ( !self::$_instance )
			self::$_instance = new self;
		return self::$_instance;
	}

	/* Plugin setup. */
	public function __construct() 
	{
		/* Add Settings Page */
		add_action( 'admin_menu', array( $this, 'add_contactadvertenties_setting_menu' ) );

		/* Register Settings for Settings Page */
		add_action( 'admin_init', array( $this, 'initialize_contactadvertentie_settings' ) );

		/* Save settings (before init, because post type registers on init) */
		/* I probably should be using Settings API.. */
		add_action( 'init', array( $this, 'save_contactadvertentie_settings' ), 1 );

		/* Add scripts to settings page */
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts_and_styles' ) ); 
	}

	/***********************
	 * Settings Page
	 ***********************/

	/* */
	public function add_contactadvertenties_setting_menu()
	{
		add_submenu_page( 
			"edit.php?post_type=".EJO_Contactads::$post_type, 
			'Contactadvertenties Instellingen', 
			'Instellingen', 
			'edit_theme_options', 
			'contactadvertentie-settings', 
			array( $this, 'contactadvertentie_settings_page' ) 
		);
	}

	/* Register settings */
	public function initialize_contactadvertentie_settings() 
	{
		// Add option if not already available
		if( false == get_option( 'contactadvertentie_settings' ) ) {  
			add_option( 'contactadvertentie_settings' );
		} 
	}

	/* Save Contactadvertenties settings */
	public function save_contactadvertentie_settings()
	{
		if (isset($_POST['submit']) && !empty($_POST['contactadvertentie-settings']) ) :

			/* Escape slug */
			$_POST['contactadvertentie-settings']['archive_slug'] = sanitize_title( $_POST['contactadvertentie-settings']['archive_slug'] );

			/* Strip slashes */
			$_POST['contactadvertentie-settings']['description'] = stripslashes( $_POST['contactadvertentie-settings']['description'] );

			/* Update settings */
			update_option( "contactadvertentie_settings", $_POST['contactadvertentie-settings'] ); 

		endif;
	}

	/* */
	public function contactadvertentie_settings_page()
	{
	?>
		<div class='wrap' style="max-width:960px;">
			<h2>Contactadvertenties Instellingen</h2>

			<?php 
			/* Let user know the settings are saved */
			if (isset($_POST['submit']) && !empty($_POST['contactadvertentie-settings']) ) {

				flush_rewrite_rules(); /* Flush rewrite rules because archive slug could have changed */

				echo "<div class='updated'><p>Contactadvertenties settings updated successfully.</p></div>";
			}
			?>

			<form action="<?php echo esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ); ?>" method="post">
				<?php wp_nonce_field('contactadvertentie-settings', 'contactadvertentie-settings-nonce'); ?>

				<?php self::show_contactadvertentie_settings(); ?>

				<?php submit_button( 'Wijzigingen opslaan' ); ?>
				<?php // submit_button( 'Standaard Instellingen', 'secondary', 'reset' ); ?>

			</form>

		</div>
	<?php
	}


    public function show_contactadvertentie_settings() 
    {	
    	/* Get post type object */
    	$contactadvertentie_project_post_type = get_post_type_object( EJO_Contactads::$post_type );

    	/* Load settings */
    	extract(
    		wp_parse_args( get_option('contactadvertentie_settings', array()), array( 
	            'title' => '',
	            'description' => '',
	            'archive_slug' => '',
	        ))
	    );

    	//* Fallback for $title and $archive_slug when they are empty
	    $title = $title ? $title : $contactadvertentie_project_post_type->labels->name;
	    $archive_slug = $archive_slug ? $archive_slug : $contactadvertentie_project_post_type->has_archive;
		
    	?>
    	<table class="form-table">
			<tbody>

				<tr>					
					<th scope="row">
						<label for="contactadvertenties-title"><?php _e('Title:'); ?></label>
					</th>
					<td>
						<?php 
						printf( '<input id="%s" value="%s" type="text" name="%s" class="text">',
							'contactadvertenties-title',
							$title,
							'contactadvertentie-settings[title]'
						);
						?>
						<p class="description">Wordt getoond op de archiefpagina, breadcrumbs en meta's tenzij anders aangegeven</p>
					</td>
				</tr>

				<tr>					
					<th scope="row">
						<label for="contactadvertenties_description"><?php _e('Description:'); ?></label>
					</th>
					<td>
						<?php 

						wp_editor( 
							$description, 
							'contactadvertenties_description', 
							array(
								'textarea_name' => 'contactadvertentie-settings[description]',
							) 
						);

						?>
						<p class="description"><?php _e('The description may be shown on the archivepage depending on the theme'); ?></p>
					</td>
				</tr>

				<tr>					
					<th scope="row">
						<label for="contactadvertenties-slug"><?php _e('Archive Slug'); ?></label>
					</th>
					<td>
						<?php 
						printf( '<input id="%s" value="%s" type="text" name="%s" class="text">',
							'contactadvertenties-slug',
							$archive_slug,
							'contactadvertentie-settings[archive_slug]'
						);
						?>
						<p class="description">Bepaalt de <i>slug</i> van de archiefpagina</p>
					</td>
				</tr>
				
			</tbody>
		</table>
		<?php
    }

	/* Manage admin scripts and stylesheets */
	public function add_scripts_and_styles()
	{
		/* Settings Page */
		if (isset($_GET['page']) && $_GET['page'] == 'contactadvertentie-settings') :

			/* Settings page javascript */
			wp_enqueue_script( 'contactadvertenties-admin-settings-page-js', EJO_Contactads_Plugin::$uri . 'assets/js/admin-settings-page.js', array('jquery'));

			/* Settings page stylesheet */
			wp_enqueue_style( 'contactadvertenties-admin-settings-page-css', EJO_Contactads_Plugin::$uri . 'assets/css/admin-settings-page.css' );

		endif;
	}
}