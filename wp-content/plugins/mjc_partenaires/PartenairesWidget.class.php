<?php

class PartenairesWidget extends WP_Widget
{
	/**
	 * Initialise le nom du widget, sa description, etc...
	 */
	public function __construct() {
		$widget_options = array(
				'description' => 'Ce widget permet l\'affichage des logos des partenaires financiers.'
				);
		parent::__construct('mjc_partenaires', 'Partenaires', $widget_options);
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
		<h3 class="widget-title">Nos partenaires financiers</h3>
		<img src="wp-content/plugins/mjc_partenaires/img/logo_caf.png" alt="CAF">
		<img src="wp-content/plugins/mjc_partenaires/img/mairie.png" alt="CAF">
		<img src="wp-content/plugins/mjc_partenaires/img/logo_premier_ministre.png" alt="CAF">
		<img src="wp-content/plugins/mjc_partenaires/img/mjc_rhone_alpes.png" alt="CAF">
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