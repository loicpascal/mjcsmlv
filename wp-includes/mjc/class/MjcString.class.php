<?php

class MjcString {
	
	function getNomFichierPhoto($string){
		return strtr(str_replace(array(" ", "/"), array("", ""), $string),'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
		'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
	}
}