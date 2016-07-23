<?php

class NewsletterWidget extends WP_Widget
{
	/**
	 * Initialise le nom du widget, sa description, etc...
	 */
	public function __construct() {
		$widget_options = array(
				'description' => 'Ce widget permet de récupérer les adresses mails des visiteurs.'
				);
		parent::__construct('mjc_newsletter', 'Newsletter', $widget_options);
	}

	/**
	 * Affiche le contenu du widget
	 */
	public function widget($args, $instance) 
	{
		echo $args['before_widget'];
		echo $args['before_title'];
		// echo apply_filters('widget_title',$instance['title'] );
		echo $args['after_title'];
		?>
		<h3 class="widget-title">Tenez-vous informés</h3>
		<p>Recevoir la Newsletter !</p>
		<form action="" method="post">
		    <p>
		        <input id="mjc_newsletter_email" name="mjc_newsletter_email" type="email" placeholder="Votre email" required/>
		    </p>
		    <input type="submit" value="Envoyer" />
		</form>
		<?php
		echo $args['after_widget'];
	}

	public function form($instance)
	{
	    $title = isset($instance['title']) ? $instance['title'] : '';
	    ?>
	    <p>
	        <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
	        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo  $title; ?>" />
	    </p>
	    <?php
	}
}