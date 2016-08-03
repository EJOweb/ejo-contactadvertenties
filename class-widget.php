<?php



/**
 * Class used to implement a Contactads widget.
 */
final class EJO_Contactads_Widget extends WP_Widget
{
	/**
	 * Sets up a new widget instance.
	 */
	function __construct() 
	{
		$widget_title = __('Contactadvertenties Widget', 'ejo-contactadvertenties');

		$widget_info = array(
			'classname'   => 'contactadvertenties-widget',
			'description' => __('Text followed by contactadvertenties categories', 'ejo-contactadvertenties'),
		);

		parent::__construct( 'contactadvertenties-widget', $widget_title, $widget_info );
	}


	/**
	 * Outputs the content for the current widget instance.
	 */
	public function widget( $args, $instance ) 
	{
		/** 
		 * Combine $instance data with defaults
		 * Then extract variables of this array
		 */
        extract( wp_parse_args( $instance, array( 
            'post_id' => '',
            'link_text' => '',
        )));

        if (empty($post_id))
        	return;

        $post = get_post($post_id);

		if (empty($post))
        	return;

		$url = get_permalink( $post->ID );

		$content = apply_filters('the_content', $post->post_content);
		$content = str_replace(']]>', ']]&gt;', $content);

		$image_id = get_post_thumbnail_id( $post_id );
		$image_size = apply_filters( 'ejo_contactads_widget_image_size', 'thumbnail' );
		$image_url = ( ! empty($image_id) ) ? wp_get_attachment_image_src($image_id, $image_size)[0] : '';

		$categories = wp_get_post_terms( $post_id, EJO_Contactads::$post_type_category );
		$category = ( ! empty($categories) ) ? $categories[0] : '';
		
		?>

		<?php echo $args['before_widget']; ?>

		<?php if ( ! empty($image_url) ) : ?>

			<div class="featured-image">
				<img src="<?php echo $image_url; ?>">
			</div>

		<?php endif; ?>

		<div class="entry-header">

			<?php if ( ! empty($category) ) : ?>
				<a href="<?php echo get_term_link( $category->term_id ); ?>" class="category"><?php echo $category->name; ?></a>
			<?php endif; ?>

			<h3><?php echo $post->post_title; ?></h3>
		</div>

		<div class="entry-content">
			<?php echo $content; ?>
		</div>

		<?php if (!empty($link_text)) : ?>

			<a href="<?php echo $url; ?>" class="button"><?php echo $link_text; ?></a>

		<?php endif; // URL check ?>
	
		<?php echo $args['after_widget']; ?>

		<?php
	}

	/**
	 * Outputs the widget settings form.
	 */
 	public function form( $instance ) 
 	{
		/** 
		 * Combine $instance data with defaults
		 * Then extract variables of this array
		 */
        extract( wp_parse_args( $instance, array( 
            'post_id' => '',
            'link_text' => '',
        )));

         //* Get posts
	    $posts = get_posts( array(
			'post_type' => EJO_Contactads::$post_type,
			'posts_per_page' => -1,
		));

		?>

		<p>
			<label for="<?php echo $this->get_field_id('post_id'); ?>"><?php _e('Contactadvertentie:') ?></label>
			<select id="<?php echo $this->get_field_id('post_id'); ?>" name="<?php echo $this->get_field_name('post_id'); ?>" class="widefat">

				<?php 			

				foreach ($posts as $post) {
					echo '<option value="'.$post->ID.'" '.selected($post_id, $post->ID, false).'>'.$post->post_title.'</option>';
				}

				?>

			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('link_text'); ?>"><?php _e('Link text:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" name="<?php echo $this->get_field_name('link_text'); ?>" value="<?php echo $link_text; ?>" />
		</p>
		<?php
	}

	/**
	 * Handles updating settings for the current widget instance.
	 */
	public function update( $new_instance, $old_instance ) 
	{
		/* Store old instance as defaults */
		$instance = $old_instance;

		/* Store post id */
		$instance['post_id'] = $new_instance['post_id'];

		/* Store url and link-text */
		$instance['link_text'] = strip_tags( $new_instance['link_text'] );

		/* Return updated instance */
		return $instance;
	}
}