<?php
include_once plugin_dir_path( __FILE__ ).'/RechercheActiviteWidget.class.php';

class RechercheActivite
{
    public function __construct()
    {
    	// add_action( 'widgets_init' , function() {register_widget( 'RechercheActiviteWidget' );});

    	add_action( 'widgets_init', function(){
			register_widget( 'RechercheActiviteWidget' );
		});
    }
}