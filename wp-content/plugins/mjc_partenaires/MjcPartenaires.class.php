<?php
/*
Plugin Name: MJC - Partenaires
Description: Plugin qui affiche les logos des partenaires financiers
Version: 0.1
Author: Loïc PASCAL
Author URI: http://loic-pascal.fr
*/

class MjcPartenaires
{
	public function __construct()
  	{
	    //...
	    register_activation_hook(__FILE__, array('Partenaires', 'install'));
	    register_uninstall_hook(__FILE__, array('Partenaires', 'uninstall'));

	    

	    include_once plugin_dir_path( __FILE__ ).'/Partenaires.class.php';
	    new Partenaires();

	    //...  
    }
}

new MjcPartenaires();