<?php

class MjcFolderFile {
	
    public static function createActiviteFolder(){
        if(!file_exists(ABSPATH . 'wp-content/uploads/activites/' . date("Y"))) {
            mkdir(ABSPATH . 'wp-content/uploads/activites/' . date("Y"), 0777);
        }

        if(!file_exists(ABSPATH . 'wp-content/uploads/activites/' . date("Y") . "/" . date("m"))) {
            mkdir(ABSPATH . 'wp-content/uploads/activites/' . date("Y") . "/" . date("m"), 0777);
        }

        return ABSPATH . 'wp-content/uploads/activites/' . date("Y") . "/" . date("m");
    }

	public function createActuFolder(){
		if(!file_exists(ABSPATH . 'wp-content/uploads/actus/' . date("Y"))) {
            mkdir(ABSPATH . 'wp-content/uploads/actus/' . date("Y"), 0777);
        }

        if(!file_exists(ABSPATH . 'wp-content/uploads/actus/' . date("Y") . "/" . date("m"))) {
            mkdir(ABSPATH . 'wp-content/uploads/actus/' . date("Y") . "/" . date("m"), 0777);
        }

        return ABSPATH . 'wp-content/uploads/actus/' . date("Y") . "/" . date("m");
	}

    public function getActuFolderPath(){
        return ABSPATH . 'wp-content/uploads/actus/';
    }
}