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
            'title' => '',
            'text' => '',
            'link_text' => '',
        )));

		/* Run $text through filter */
		$text = apply_filters( 'widget_text', $text, $instance, $this );

		/* Get archive of contactadvertenties */
		$url = get_post_type_archive_link( EJO_Contactads::$post_type );
		?>

		<?php echo $args['before_widget']; ?>

		<?php echo $args['before_title']; ?><a href="<?php echo $url; ?>"><?php echo $title; ?></a><?php echo $args['after_title']; ?>

		<div class="textwidget">
			<?php echo wpautop($text); ?>
		</div>

		<?php

		/* Get contactadvertenties categories */
		$categories = get_terms( 
			'contactadvertenties_category',
			array(
			    'orderby' => 'name',
			    'order'   => 'ASC',
			)
		);

		?>
	    
	    <div class="contactadvertenties-categories">

		    <?php foreach( $categories as $category ) : // Loop through each contactadvertenties category ?>

		    	<?php

				/* Get Contactadvertenties ategory url */
				$category_url = esc_url( get_term_link( $category ) );

				/* Fabricate contactadvertenties category link */
			    $category_link = sprintf( '<a href="%s" alt="%s">%s</a>',
			        $category_url,
			        esc_attr( sprintf( 'View all posts in %s', $category->name ) ),
			        esc_html( $category->name )
			    );

				?>

		    	<h4 <?php hybrid_attr( 'category-title' ); ?>><a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" title="<?php echo esc_attr( 'Bekijk alle '. $category->name .' artikelen' ); ?>" rel="bookmark" itemprop="url"><?php echo $category->name; ?></a></h4>


			<?php endforeach; // END foreach category loop ?>

		</div>

		<?php if (!empty($link_text)) : ?>

			<a href="<?php echo $url; ?>" class="read-more"><?php echo $link_text; ?></a>

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
            'title' => '',
            'text' => '',
            'link_text' => '',
        )));

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:') ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
			<?php //wp_editor( $text, $this->get_field_id('text'), array(	'textarea_name' => $this->get_field_name('text') ) ); ?>
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

		/* Store new title */
		$instance['title'] = strip_tags( $new_instance['title'] );

		/* Store text */
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = wp_kses_post( stripslashes( $new_instance['text'] ) );

		/* Store url and link-text */
		$instance['link-text'] = strip_tags( $new_instance['link-text'] );

		/* Return updated instance */
		return $instance;
	}
}