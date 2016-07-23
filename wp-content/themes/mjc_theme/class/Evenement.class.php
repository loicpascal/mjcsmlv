<?php

class Evenement {
	private $nom;
	private $lieu;
	private $date_heure;
	private $descriptif;
	private $intervenant;
	private $tarif;
	private $photo_video;
	private $logos_partenaires;
	private $debut_publication;
	private $fin_publication;

    /**
     * Gets the value of nom.
     *
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Sets the value of nom.
     *
     * @param mixed $nom the nom
     *
     * @return self
     */
    private function setNom($nom)
    {
        if (strlen($nom) == 0) {
        	throw new Exception("Le nom ne peut pas être vide");
        }
        elseif(strlen($nom) > 100) {
        	throw new Exception("Le nom ne doit pas dépasser 100 caractères");
        }

        $this->nom = $nom;

        return $this;
    }

    /**
     * Gets the value of lieu.
     *
     * @return mixed
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * Sets the value of lieu.
     *
     * @param mixed $lieu the lieu
     *
     * @return self
     */
    private function setLieu($lieu)
    {
    	if (strlen($lieu) == 0) {
        	throw new Exception("Le lieu ne peut pas être vide");
        }
        elseif(strlen($lieu) > 100) {
        	throw new Exception("Le lieu ne doit pas dépasser 100 caractères");
        }

        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Gets the value of date_heure.
     *
     * @return mixed
     */
    public function getDateHeure()
    {
        return $this->date_heure;
    }

    /**
     * Sets the value of date_heure.
     *
     * @param mixed $date_heure the date heure
     *
     * @return self
     */
    private function setDateHeure($date_heure)
    {
    	if (strlen($date_heure) == 0) {
        	throw new Exception("La date ne peut pas être vide");
        }
        elseif(strlen($date_heure) > 100) {
        	throw new Exception("La date ne doit pas dépasser 100 caractères");
        }

        $this->date_heure = $date_heure;

        return $this;
    }

    /**
     * Gets the value of descriptif.
     *
     * @return mixed
     */
    public function getDescriptif()
    {
        return $this->descriptif;
    }

    /**
     * Sets the value of descriptif.
     *
     * @param mixed $descriptif the descriptif
     *
     * @return self
     */
    private function setDescriptif($descriptif)
    {
    	if (strlen($descriptif) == 0) {
        	throw new Exception("Le descriptif ne peut pas être vide");
        }
        elseif(strlen($descriptif) > 2500) {
        	throw new Exception("Le descriptif ne doit pas dépasser 2500 caractères");
        }

        $this->descriptif = $descriptif;

        return $this;
    }

    /**
     * Gets the value of intervenant.
     *
     * @return mixed
     */
    public function getIntervenant()
    {
        return $this->intervenant;
    }

    /**
     * Sets the value of intervenant.
     *
     * @param mixed $intervenant the intervenant
     *
     * @return self
     */
    private function setIntervenant($intervenant)
    {

    	if (strlen($intervenant) == 0) {
        	throw new Exception("L'intervenant ne peut pas être vide");
        }
        elseif(strlen($intervenant) > 100) {
        	throw new Exception("L'intervenant ne doit pas dépasser 100 caractères");
        }

        $this->intervenant = $intervenant;

        return $this;
    }

    /**
     * Gets the value of tarif.
     *
     * @return mixed
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    /**
     * Sets the value of tarif.
     *
     * @param mixed $tarif the tarif
     *
     * @return self
     */
    private function setTarif($tarif)
    {
    	if (strlen($tarif) == 0) {
        	throw new Exception("Le tarif ne peut pas être vide");
        }
        elseif(strlen($tarif) > 50) {
        	throw new Exception("Le tarif ne doit pas dépasser 50 caractères");
        }

        $this->tarif = $tarif;

        return $this;
    }

    /**
     * Gets the value of photo_video.
     *
     * @return mixed
     */
    public function getPhotoVideo()
    {
        return $this->photo_video;
    }

    /**
     * Sets the value of photo_video.
     *
     * @param mixed $photo_video the photo video
     *
     * @return self
     */
    private function setPhotoVideo(file $photo_video)
    {
    	if (strlen($tarif) == 0) {
        	throw new Exception("La photos/vidéo ne peut pas être vide");
        }
        elseif(strlen($tarif) > 50) {
        	throw new Exception("Le tarif ne doit pas dépasser 50 caractères");
        }

        $this->photo_video = $photo_video;

        return $this;
    }

    /**
     * Gets the value of logos_partenaires.
     *
     * @return mixed
     */
    public function getLogosPartenaires()
    {
        return $this->logos_partenaires;
    }

    /**
     * Sets the value of logos_partenaires.
     *
     * @param mixed $logos_partenaires the logos partenaires
     *
     * @return self
     */
    private function setLogosPartenaires(file $logos_partenaires)
    {
        $this->logos_partenaires = $logos_partenaires;

        return $this;
    }

    /**
     * Gets the value of debut_publication.
     *
     * @return mixed
     */
    public function getDebutPublication()
    {
        return $this->debut_publication;
    }

    /**
     * Sets the value of debut_publication.
     *
     * @param mixed $debut_publication the debut publication
     *
     * @return self
     */
    private function setDebutPublication(datetime $debut_publication)
    {
        $this->debut_publication = $debut_publication;

        return $this;
    }

    /**
     * Gets the value of fin_publication.
     *
     * @return mixed
     */
    public function getFinPublication()
    {
        return $this->fin_publication;
    }

    /**
     * Sets the value of fin_publication.
     *
     * @param mixed $fin_publication the fin publication
     *
     * @return self
     */
    private function setFinPublication(datetime $fin_publication)
    {
        $this->fin_publication = $fin_publication;

        return $this;
    }
}