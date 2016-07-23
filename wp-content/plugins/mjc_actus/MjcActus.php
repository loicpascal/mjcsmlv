<?php

/*
Plugin Name: MJC - Actus
Description: Plugin qui permet la modifications des actus présentes sur le site
Version: 0.1
Author: Loïc PASCAL
Author URI: http://loic-pascal.fr
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class MjcActus
{
	public function __construct()
  	{
	    //...
	    register_activation_hook(__FILE__, array('Actus', 'install'));
	    register_uninstall_hook(__FILE__, array('Actus', 'uninstall'));

	    

	    include_once plugin_dir_path( __FILE__ ).'/Actus.class.php';
	    new Actus();

	    //...  
    }
}

new MjcActus();