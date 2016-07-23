<?php
/*
Plugin Name: MJC - Recherche Activités
Description: Plugin qui permet au visiteur de rechercher une activité
Version: 0.1
Author: Loïc PASCAL
Author URI: http://loic-pascal.fr
*/

class MjcRechercheActivite
{
	public function __construct()
  	{
	    //...
	    include_once plugin_dir_path( __FILE__ ).'/RechercheActivite.class.php';
	    new RechercheActivite();

	    //...  
    }
}

new MjcRechercheActivite();