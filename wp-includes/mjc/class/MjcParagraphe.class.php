<?php
/**
 * classe Paragraphe
 */

class MjcParagraphe {
	
	public static function secureInput($value) {
		return trim(addslashes($value));
	}

	/**
	 * Retourne la valeur proprement (sans antislashes, sans espaces en début/fin)
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public static function epure($value) {
		return trim(htmlspecialchars(stripslashes($value), ENT_QUOTES));
	}

    public static function getNomFichierPhoto($string){
        return stripslashes(strtr(str_replace(array(" ", "/", "'", "\""), array("", "", "", ""), $string),
            'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ', 
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));
    }
}