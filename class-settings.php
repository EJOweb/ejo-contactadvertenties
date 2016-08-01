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
		add_action( 'admin_init', array( $this, 'initialize_contactadvertenties_settings' ) );

		/* Save settings (before init, because post type registers on init) */
		/* I probably should be using Settings API.. */
		add_action( 'init', array( $this, 'save_contactadvertenties_settings' ), 1 );

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
			'contactadvertenties-settings', 
			array( $this, 'contactadvertenties_settings_page' ) 
		);
	}

	/* Register settings */
	public function initialize_contactadvertenties_settings() 
	{
		// Add option if not already available
		if( false == get_option( 'contactadvertenties_settings' ) ) {  
			add_option( 'contactadvertenties_settings' );
		} 
	}

	/* Save Contactadvertenties settings */
	public function save_contactadvertenties_settings()
	{
		if (isset($_POST['submit']) && !empty($_POST['contactadvertenties-settings']) ) :

			/* Escape slug */
			$_POST['contactadvertenties-settings']['archive-slug'] = sanitize_title( $_POST['contactadvertenties-settings']['archive-slug'] );

			/* Strip slashes */
			$_POST['contactadvertenties-settings']['description'] = stripslashes( $_POST['contactadvertenties-settings']['description'] );

			/* Update settings */
			update_option( "contactadvertenties_settings", $_POST['contactadvertenties-settings'] ); 

		endif;
	}

	/* */
	public function contactadvertenties_settings_page()
	{
	?>
		<div class='wrap' style="max-width:960px;">
			<h2>Contactadvertenties Instellingen</h2>

			<?php 
			/* Let user know the settings are saved */
			if (isset($_POST['submit']) && !empty($_POST['contactadvertenties-settings']) ) {

				flush_rewrite_rules(); /* Flush rewrite rules because archive slug could have changed */

				echo "<div class='updated'><p>Contactadvertenties settings updated successfully.</p></div>";
			}
			?>

			<form action="<?php echo esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ); ?>" method="post">
				<?php wp_nonce_field('contactadvertenties-settings', 'contactadvertenties-settings-nonce'); ?>

				<?php self::show_contactadvertenties_settings(); ?>

				<?php submit_button( 'Wijzigingen opslaan' ); ?>
				<?php // submit_button( 'Standaard Instellingen', 'secondary', 'reset' ); ?>

			</form>

		</div>
	<?php
	}


    public function show_contactadvertenties_settings() 
    {	
    	/* Get post type object */
    	$contactadvertenties_project_post_type = get_post_type_object( EJO_Contactads::$post_type );

    	/* Load settings */
    	$contactadvertenties_settings = get_option('contactadvertenties_settings', array());

		/* Archive title */
		$title = (isset($contactadvertenties_settings['title'])) ? $contactadvertenties_settings['title'] : $contactadvertenties_project_post_type->labels->name;

		/* Archive description */
		$description = (isset($contactadvertenties_settings['description'])) ? $contactadvertenties_settings['description'] : $contactadvertenties_project_post_type->description;

		/* Archive slug */
		$archive_slug = (isset($contactadvertenties_settings['archive-slug'])) ? $contactadvertenties_settings['archive-slug'] : $contactadvertenties_project_post_type->has_archive;
		
    	?>
    	<table class="form-table">
			<tbody>

				<tr>					
					<th scope="row">
						<label for="contactadvertenties-title"><?php _e('Title:'); ?></label>
					</th>
					<td>
						<input
							id="contactadvertenties-title"
							value="<?php echo $title; ?>"
							type="text"
							name="contactadvertenties-settings[title]"
							class="text"
						>
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
								'textarea_name' => 'contactadvertenties-settings[description]',
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
						<input
							id="contactadvertenties-slug"
							value="<?php echo $archive_slug; ?>"
							type="text"
							name="contactadvertenties-settings[archive-slug]"
							class="text"
						>
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
		if (isset($_GET['page']) && $_GET['page'] == 'contactadvertenties-settings') :

			/* Settings page javascript */
			wp_enqueue_script( 'contactadvertenties-admin-settings-page-js', plugin_dir_url( __FILE__ ) . 'assets/js/admin-settings-page.js', array('jquery'));

			/* Settings page stylesheet */
			wp_enqueue_style( 'contactadvertenties-admin-settings-page-css', plugin_dir_url( __FILE__ ) . 'assets/css/admin-settings-page.css' );

		endif;
	}
}