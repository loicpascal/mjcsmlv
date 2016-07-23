<?php

class MjcFolderFile {
	
    function createActiviteFolder(){
        if(!file_exists(ABSPATH . 'wp-content/uploads/activites/' . date("Y"))) {
            mkdir(ABSPATH . 'wp-content/uploads/activites/' . date("Y"), 0777);
        }

        if(!file_exists(ABSPATH . 'wp-content/uploads/activites/' . date("Y") . "/" . date("m"))) {
            mkdir(ABSPATH . 'wp-content/uploads/activites/' . date("Y") . "/" . date("m"), 0777);
        }

        return ABSPATH . 'wp-content/uploads/activites/' . date("Y") . "/" . date("m");
    }

	function createActuFolder(){
		if(!file_exists(ABSPATH . 'wp-content/uploads/actus/' . date("Y"))) {
            mkdir(ABSPATH . 'wp-content/uploads/actus/' . date("Y"), 0777);
        }

        if(!file_exists(ABSPATH . 'wp-content/uploads/actus/' . date("Y") . "/" . date("m"))) {
            mkdir(ABSPATH . 'wp-content/uploads/actus/' . date("Y") . "/" . date("m"), 0777);
        }

        return ABSPATH . 'wp-content/uploads/actus/' . date("Y") . "/" . date("m");
	}

    function getActuFolderPath(){
        return ABSPATH . 'wp-content/uploads/actus/';
    }
}