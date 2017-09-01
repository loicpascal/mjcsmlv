<?php

class RechercheActiviteWidget extends WP_Widget
{
	/**
	 * Initialise le nom du widget, sa description, etc...
	 */

	private $domaines;

	public function __construct() {
		$widget_options = array(
				'description' => 'Ce widget permet aux visiteurs de rechercher une activité.'
				);
		parent::__construct('mjc_recherche_activite', 'RechercheActivite', $widget_options);

		global $wpdb;
		$this->domaines = $wpdb->get_results( "SELECT * FROM mjc_activites_domaines ORDER BY domaine_nom" );
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
		<h3 class="widget-title">Recherchez</h3>
		<p>Trouvez votre activité !</p>
		<form action="<?php echo get_permalink(20); ?>" method="post">
		    <p>
		        <input id="mjc_recherche_activite_mot_cle" name="recherche_activites_mots" type="text" placeholder="Hip-hop, fitness, anglais, couture …" required/>
		    </p>
		    <input type="submit" value="Trouver"/>
		</form>
		<form action="<?php echo get_permalink(20); ?>" method="post">
		    <p>
		    	<label for="mjc_recherche_activite_age">Age</label>
		        <select id="mjc_recherche_activite_age" name="mjc_recherche_activite_age">
					<?php
					for ($i=0; $i <= 99; $i++) {
						# code...
						echo '<option value="' . $i . '" ' . ((isset($_POST['mjc_recherche_activite_age']) && $_POST['mjc_recherche_activite_age'] == $i ) ? 'selected="selected"' : '') . '>' . $i . '</option>';
					}
					?>
		        </select>
		   		<label for="mjc_recherche_activite_domaine">Domaine</label>
		    	<select id="mjc_recherche_activite_domaine" name="mjc_recherche_activite_domaine">
					<?php
		            foreach ( $this->domaines as $domaine )
		            {
		                echo '<option value="' . $domaine->domaine_id . '" ' . ((isset($_POST['mjc_recherche_activite_domaine']) && $_POST['mjc_recherche_activite_domaine'] == $domaine->domaine_id ) ? 'selected="selected"' : '') . '>' . $domaine->domaine_nom . '</option>';
		            }
		            ?>
		        </select>
		    </p>
		    <input type="submit" value="Trouver"/>
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