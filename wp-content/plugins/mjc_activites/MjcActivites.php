<?php

/*
Plugin Name: MJC - Activités
Description: Plugin qui permet la modifications des activités présentes sur le site
Version: 0.1
Author: Loïc PASCAL
Author URI: http://loic-pascal.fr
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class MjcActivites
{
	public function __construct()
  	{
	    //...
	    register_activation_hook(__FILE__, array('Activites', 'install'));
	    register_uninstall_hook(__FILE__, array('Activites', 'uninstall'));

	    

	    include_once plugin_dir_path( __FILE__ ).'/Activites.class.php';
	    new Activites();

	    //...  
    }
}

new MjcActivites();