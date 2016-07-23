<?php

class Activite {
	
	/**
	 * Attributs
	 */
	
	private $nom;
	private $nb_places;
	private $tranche_age;
	private $age;
	private $age_min;
	private $age_max;
	private $domaine;
	private $tarif;
	private $t1;
	private $t2;
	private $t3;
	private $t4;
	private $jour_heure;
	private $lieu;
	private $intervenant;
	private $photo;
	private $descriptif;
	private $lien;

    /**
     * Gets the nom.
     *
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Sets the nom.
     *
     * @param mixed $nom the nom
     *
     * @return self
     */
    private function setNom($nom)
    {
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
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Gets the value of jour_heure.
     *
     * @return mixed
     */
    public function getJourHeure()
    {
        return $this->jour_heure;
    }

    /**
     * Sets the value of jour_heure.
     *
     * @param mixed $jour_heure the jour heure
     *
     * @return self
     */
    private function setJourHeure($jour_heure)
    {
        $this->jour_heure = $jour_heure;

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
        $this->descriptif = $descriptif;

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
        $this->tarif = $tarif;

        return $this;
    }
}