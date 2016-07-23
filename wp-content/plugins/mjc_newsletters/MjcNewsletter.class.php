<?php
/*
Plugin Name: MJC - Newsletters
Description: Plugin qui permet la récupération des mails des visiteurs
Version: 0.1
Author: Loïc PASCAL
Author URI: http://loic-pascal.fr
*/

class MjcNewsletter
{
	public function __construct()
  	{
	    //...
	    register_activation_hook(__FILE__, array('Newsletter', 'install'));
	    register_uninstall_hook(__FILE__, array('Newsletter', 'uninstall'));

	    

	    include_once plugin_dir_path( __FILE__ ).'/Newsletter.class.php';
	    new Newsletter();

	    //...  
    }
}

new MjcNewsletter();