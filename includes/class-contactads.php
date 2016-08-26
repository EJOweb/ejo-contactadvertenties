<?php

final class EJO_Contactads
{
	//* Slug of this plugin
    const SLUG = EJO_Contactads_Plugin::SLUG;

	/* Store post type */
	public static $post_type = 'contactadvertentie';

	/* Post type archive */
	public static $post_type_archive = 'contactadvertenties';

	/* Post type category */
	public static $post_type_category = 'contactadvertentie_category';

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
		/* Register Post Type */
		add_action( 'init', array( $this, 'register_post_type' ) );

		/* Rewrite contactadvertentie post permalink */
		add_filter( 'post_type_link', array( $this, 'contactadvertentie_permalink' ), 10, 4 );

		/* Manage columns */
		add_filter( 'manage_edit-'.self::$post_type.'_columns', array( $this, 'edit_contactadvertentie_columns' ) );

		/* Manage columns */
		add_action( 'manage_'.self::$post_type.'_posts_custom_column', array( $this, 'manage_contactadvertentie_columns' ), 10, 2 );
	}

	/* Register Post Type */
	public function register_post_type() 
	{
		/* Load settings */
    	extract(
    		wp_parse_args( get_option('contactadvertentie_settings', array()), array( 
	            'title' => '',
	            'description' => '',
	            'archive_slug' => '',
	        ))
	    );

    	//* Fallback for $title and $archive_slug when they are empty
	    $title = $title ? $title : ucfirst(self::$post_type_archive);
	    $archive_slug = $archive_slug ? $archive_slug : self::$post_type_archive;

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
					'slug'       => trailingslashit( $archive_slug ) . '%' . self::$post_type_category . '%',
					'with_front' => false,
					'pages'      => true,
					'feeds'      => true,
					'ep_mask'    => EP_PERMALINK,
				),

				'map_meta_cap' => true,

				'capability_type' => 'contactad',
				'capabilities' => array(

					//* meta caps (don't assign these to roles)
					'edit_post'              => 'edit_contactad',
					'read_post'              => 'read_contactad',
					'delete_post'            => 'delete_contactad',

					//* primitive/meta caps
					'create_posts'           => 'create_contactads',

					//* primitive caps used outside of map_meta_cap()
					'edit_posts'             => 'edit_contactads',
					'edit_others_posts'      => 'edit_others_contactads',
					'publish_posts'          => 'publish_contactads',
					'read_private_posts'     => 'read_private_contactads',

					//* primitive caps used inside of map_meta_cap()
					'read'                   => 'read',
					'delete_posts'           => 'delete_contactads',
					'delete_private_posts'   => 'delete_private_contactads',
					'delete_published_posts' => 'delete_published_contactads',
					'delete_others_posts'    => 'delete_others_contactads',
					'edit_private_posts'     => 'edit_private_contactads',
					'edit_published_posts'   => 'edit_published_contactads'
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
					'name'               => __( $title,					self::SLUG ),
					'singular_name'      => __( 'Advertentie', 			self::SLUG ),
					'menu_name'          => __( 'Advertenties', 		self::SLUG ),
					'name_admin_bar'     => __( 'Advertenties',			self::SLUG ),
					'add_new'            => __( 'Add New', 				self::SLUG ),
					'add_new_item'       => __( 'Add New Advertentie', 	self::SLUG ),
					'edit_item'          => __( 'Edit Advertentie', 	self::SLUG ),
					'new_item'           => __( 'New Advertentie', 		self::SLUG ),
					'view_item'          => __( 'View Advertentie', 	self::SLUG ),
					'search_items'       => __( 'Search Advertenties', 	self::SLUG ),
					'not_found'          => __( 'No contactadvertenties found', 			self::SLUG ),
					'not_found_in_trash' => __( 'No contactadvertenties found in trash',	self::SLUG ),
					'all_items'          => __( 'All Advertenties', 	self::SLUG ),
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

				'capabilities' => array(
					'manage_terms' 	=> 'manage_contactad_categories',
					'edit_terms' 	=> 'edit_contactad_categories',
					'delete_terms' 	=> 'delete_contactad_categories',
					'assign_terms' 	=> 'assign_contactad_categories',
				),

				/* Labels used when displaying the posts. */
				'labels'        => array(
					'name'              => __( 'Categories',				self::SLUG ),
					'singular_name'     => __( 'Category', 				 	self::SLUG ),
					'menu_name'         => __( 'Categories', 			 	self::SLUG ),
					'search_items'      => __( 'Search Categories',      	self::SLUG ),
					'all_items'         => __( 'All Categories',         	self::SLUG ),
					'parent_item'       => __( 'Parent Category',        	self::SLUG ),
					'parent_item_colon' => __( 'Parent Category:',       	self::SLUG ),
					'edit_item'         => __( 'Edit Category',          	self::SLUG ),
					'update_item'       => __( 'Update Category',        	self::SLUG ),
					'add_new_item'      => __( 'Add New Category',       	self::SLUG ),
					'new_item_name'     => __( 'New Category ',          	self::SLUG ),
					'popular_items'     => __( 'Popular Categories',     	self::SLUG ),
					'not_found'			=> __( 'Category not found', 	 	self::SLUG )
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
		add_rewrite_rule( 
			"$archive_slug/(.+?)/page/?([0-9]{1,})/?$", 
	    	'index.php?'.self::$post_type_category.'=$matches[1]&paged=$matches[2]',  
	    	'top'  
		);
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
			self::$post_type_category => __( 'Category', self::SLUG ),
			'date' => __( 'Date' ),
		);

		/* If Wordpress SEO plugin is activated add wpseo-score column */
		if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) 
			$columns['wpseo-score'] = __( 'SEO', self::SLUG );

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
			case self::$post_type_category :

				/* Get the contactadvertentie_categorys for the post. */
				$terms = get_the_terms( $post_id, self::$post_type_category );

				/* If terms were found. */
				if ( !empty( $terms ) ) {

					$out = array();

					/* Loop through each term, linking to the 'edit posts' page for the specific term. */
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, self::$post_type_category => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, self::$post_type_category, 'display' ) )
						);
					}

					/* Join the terms, separating them with a comma. */
					echo join( ', ', $out );
				}

				/* If no terms were found, output a default message. */
				else {
					_e( 'No categories', self::SLUG );
				}

				break;

			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}

	/* Get contacts capabilities */
    public static function get_caps() 
    {
        /* Custom Contactad capabilities */
        return array(
        	// Post type caps.
			'create_contactads',
			'edit_contactads', // Main cap (reference for submenu_page)
			'edit_others_contactads',
			'publish_contactads',
			'read_private_contactads',
			'delete_contactads',
			'delete_private_contactads',
			'delete_published_contactads',
			'delete_others_contactads',
			'edit_private_contactads',
			'edit_published_contactads',

            // Taxonomy caps.
            'manage_contactad_categories',
            'edit_contactad_categories',
            'delete_contactad_categories',
            'assign_contactad_categories',
        );
    }

    //* Add Capabilities
    public static function add_caps()
    {
    	// Get the administrator and editor role.
		$roles = array( get_role( 'administrator' ), get_role( 'editor' ) );

		//* Get Caps for contactads
		$contactad_caps = self::get_caps();

		//* Add caps for all given roles
		foreach ($roles as $role) {

			// If the role exists, add required capabilities for the plugin.
			if ( ! is_null( $role ) ) {

				foreach ($contactad_caps as $contactads_cap) {

					// Add cap to role.
					$role->add_cap( $contactads_cap );
				}
			}
		}
    }

    //* Remove Capabilities
    public static function remove_caps()
    {
    	// Get the administrator and editor role.
		$roles = array( get_role( 'administrator' ), get_role( 'editor' ) );

		//* Get Caps for contactads
		$contactad_caps = self::get_caps();

		//* Add caps for all given roles
		foreach ($roles as $role) {

			// If the role exists, add required capabilities for the plugin.
			if ( ! is_null( $role ) ) {

				foreach ($contactad_caps as $contactads_cap) {

					// Add cap to role.
					$role->remove_cap( $contactads_cap );
				}
			}
		}
    }
}