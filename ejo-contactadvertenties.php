<?php
/**
 * Plugin Name:         CountrySideDating Contactadvertenties
 * Plugin URI:          http://github.com/ejoweb/ejo-contactadvertenties
 * Description:         CountrySideDating Contactadvertenties
 * Version:             0.1
 * Author:              Erik Joling
 * Author URI:          http://www.ejoweb.nl/
 * Text Domain:         ejo-contactadvertenties
 * Domain Path:         /languages
 *
 * GitHub Plugin URI:   https://github.com/EJOweb/ejo-contactadvertenties
 * GitHub Branch:       master
 *
 * Minimum PHP version: 5.3.0
 *
 * @package   EJO Contactadvertenties
 * @version   0.1.0
 * @since     0.1.0
 * @author    Erik Joling <erik@ejoweb.nl>
 * @copyright Copyright (c) 2016, Erik Joling
 * @link      http://github.com/ejoweb
 */

/* Load classes */
require_once( plugin_dir_path( __FILE__ ) . 'class-settings.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-widget.php' );

/* Contactadvertenties */
EJO_Contactadvertenties::init();

/**
 *
 */
final class EJO_Contactadvertenties 
{
	/* Holds the instance of this class. */
	private static $_instance = null;

	/* Return the class instance. */
	public static function init() {
		if ( !self::$_instance )
			self::$_instance = new self;
		return self::$_instance;
	}

	/* Store post type */
	public static $post_type = 'contactadvertentie';

	/* Post type archive */
	public static $post_type_archive = 'contactadvertenties';

	/* Post type category */
	public static $post_type_category = 'contactadvertentie_category';

	/**
	 * Class is initiated at 'after_setup_theme' hook inside ejo-contactadvertenties.php
	 */
	protected function __construct() 
	{
		/* Register Post Type */
		add_action( 'init', array( $this, 'register_post_type' ) );

		/* Register Widget */
		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		/* Settings */
		EJO_Contactadvertenties_Settings::init();

		/* Rewrite contactadvertentie post permalink */
		// add_filter( 'post_type_link', array( $this, 'contactadvertentie_permalink' ), 10, 4 );

		/* Manage columns */
		add_filter( 'manage_edit-'.self::$post_type.'_columns', array( $this, 'edit_contactadvertentie_columns' ) );

		/* Manage columns */
		add_action( 'manage_'.self::$post_type.'_posts_custom_column', array( $this, 'manage_contactadvertentie_columns' ), 10, 2 );
	}

	/* Register Post Type */
	public function register_post_type() 
	{
		/* Get contactadvertentie settings */
		$contactadvertentie_settings = get_option( 'contactadvertentie_settings', array() );

		/* Archive title */
		$title = (isset($contactadvertentie_settings['title'])) ? $contactadvertentie_settings['title'] : ucfirst(self::$post_type_archive);

		/* Archive description */
		$description = (isset($contactadvertentie_settings['description'])) ? $contactadvertentie_settings['description'] : '';

		/* Archive slug */
		$archive_slug = (isset($contactadvertentie_settings['archive-slug'])) ? $contactadvertentie_settings['archive-slug'] : self::$post_type_archive;

		/* Category archive slug */
		$category_archive_slug = 'contactadvertentie-categorie';

		/* Register the Contactadvertenties post type. */
		register_post_type(
			self::$post_type,
			array(
				'description'         => $description,
				'menu_position'       => 9,
				'menu_icon'           => 'dashicons-archive',
				'public'              => true,
				'has_archive'         => $archive_slug,

				/* The rewrite handles the URL structure. */
				'rewrite' => array(
					'slug'       => $archive_slug,
					'with_front' => false,
				),

				/* What features the post type supports. */
				'supports' => array(
					'title',
					'editor',
					'excerpt',
					'author',
					'thumbnail',
					// 'custom-header'
				),

				/* Labels used when displaying the posts. */
				'labels' => array(
					'name'               => $title,
					'singular_name'      => __( 'Advertentie',                   'ejo-contactadvertenties' ),
					'menu_name'          => __( 'Advertenties',              	'ejo-contactadvertenties' ),
					'name_admin_bar'     => __( 'Advertenties',	 			    'ejo-contactadvertenties' ),
					'add_new'            => __( 'Add New',                    			'ejo-contactadvertenties' ),
					'add_new_item'       => __( 'Add New Advertentie',           'ejo-contactadvertenties' ),
					'edit_item'          => __( 'Edit Advertentie',              'ejo-contactadvertenties' ),
					'new_item'           => __( 'New Advertentie',               'ejo-contactadvertenties' ),
					'view_item'          => __( 'View Advertentie',              'ejo-contactadvertenties' ),
					'search_items'       => __( 'Search Advertenties',           'ejo-contactadvertenties' ),
					'not_found'          => __( 'No contactadvertenties found',         'ejo-contactadvertenties' ),
					'not_found_in_trash' => __( 'No contactadvertenties found in trash','ejo-contactadvertenties' ),
					'all_items'          => __( 'All Advertenties',              'ejo-contactadvertenties' ),
				)
			)
		);

		/* Register Category Taxonomy */
		register_taxonomy( 
			self::$post_type_category, 
			null,
			array( 
				'hierarchical'  => true,

				/* The rewrite handles the URL structure. */
				'rewrite' => array( 
					'slug'       => $archive_slug,
					'with_front' => false,
				),

				/* Labels used when displaying the posts. */
				'labels'        => array(
					'name'              => __( 'Categories',				'ejo-contactadvertenties' ),
					'singular_name'     => __( 'Category', 				 	'ejo-contactadvertenties' ),
					'menu_name'         => __( 'Categories', 			 	'ejo-contactadvertenties' ),
					'search_items'      => __( 'Search Categories',      	'ejo-contactadvertenties' ),
					'all_items'         => __( 'All Categories',         	'ejo-contactadvertenties' ),
					'parent_item'       => __( 'Parent Category',        	'ejo-contactadvertenties' ),
					'parent_item_colon' => __( 'Parent Category:',       	'ejo-contactadvertenties' ),
					'edit_item'         => __( 'Edit Category',          	'ejo-contactadvertenties' ),
					'update_item'       => __( 'Update Category',        	'ejo-contactadvertenties' ),
					'add_new_item'      => __( 'Add New Category',       	'ejo-contactadvertenties' ),
					'new_item_name'     => __( 'New Category ',          	'ejo-contactadvertenties' ),
					'popular_items'     => __( 'Popular Categories',     	'ejo-contactadvertenties' ),
					'not_found'			=> __( 'Category not found', 	 	'ejo-contactadvertenties' )
				),
			)
		);

		/* Connect Taxonomy with Post type */
		register_taxonomy_for_object_type( self::$post_type_category, self::$post_type );

		/**
		 * Add rewrite rule for contactadvertentie_category paging to top of rewrite-rules to solve paging 404
		 *
		 * Possible conflict with multipage posts because it precedes:
		 *
		 * $archive-slug/([^/]+)/([^/]+)/page/?([0-9]{1,})/?$	
		 * index.php?contactadvertentie_category=$matches[1]&contactadvertentie_post=$matches[2]&paged=$matches[3]
		 */
		// add_rewrite_rule( 
		// 	"$archive_slug/(.+?)/page/?([0-9]{1,})/?$", 
	 //    	'index.php?contactadvertentie_category=$matches[1]&paged=$matches[2]',  
	 //    	'top'  
		// );
	}

	/**
	 * Process permalink of contactadvertentie posts
	 */
	public function contactadvertentie_permalink($post_link, $post, $leavename, $sample) 
	{
		/* Check if %contactadvertentie_category% is in url */
	    if ( false !== strpos( $post_link, '%'.self::$post_type_category.'%' ) ) {
	        
	    	/* Get the contactadvertentie category of the post */
	        $contactadvertentie_category = get_the_terms( $post->ID, self::$post_type_category );

	        /* Get the slug of the first contactadvertentie category or fallback to specified string */
        	$contactadvertentie_category_slug = (is_array($contactadvertentie_category)) ? array_pop( $contactadvertentie_category )->slug : 'no-' . self::$post_type_category;

        	/* Replace the placeholder with contactadvertentie-category-slug */
			$post_link = str_replace( '%'.self::$post_type_category.'%', $contactadvertentie_category_slug, $post_link );
	    }

		return $post_link;
	}

	/**
	 * Edit the columns of the contactadvertentie post overview (admin area)
	 */
	public function edit_contactadvertentie_columns( $columns ) 
	{
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title' ),
			'author' => __( 'Author' ),
			'contactadvertentie_category' => __( 'Category', 'ejo-contactadvertenties' ),
			'date' => __( 'Date' ),
		);

		/* If Wordpress SEO plugin is activated add wpseo-score column */
		if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) 
			$columns['wpseo-score'] = __( 'SEO', 'ejo-contactadvertenties' );

		return $columns;
	}

	/**
	 * Process the value of the custom columns in the contactadvertentie post overview (admin area)
	 */
	function manage_contactadvertentie_columns( $column, $post_id ) 
	{
		global $post;

		switch( $column ) {

			/* If displaying the 'contactadvertentie_category' column. */
			case 'contactadvertentie_category' :

				/* Get the contactadvertentie_categorys for the post. */
				$terms = get_the_terms( $post_id, 'contactadvertentie_category' );

				/* If terms were found. */
				if ( !empty( $terms ) ) {

					$out = array();

					/* Loop through each term, linking to the 'edit posts' page for the specific term. */
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'contactadvertentie_category' => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'contactadvertentie_category', 'display' ) )
						);
					}

					/* Join the terms, separating them with a comma. */
					echo join( ', ', $out );
				}

				/* If no terms were found, output a default message. */
				else {
					_e( 'No categories', 'ejo-contactadvertenties' );
				}

				break;

			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}

	/* Register Widget */
	public function register_widget() 
	{ 
	    register_widget( 'EJO_Contactadvertenties_Widget' ); 
	}
}

