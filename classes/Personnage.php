<?php


class Personnage
{
    private $_id;
    private $_nom;
    private $_degats;

    const PERSO_TUE = 1;
    const PERSO_FRAPPE = 2;
    const PERSO_IDENTIQUE = 3;
    /**
     * Personnage constructor.
     * @param $_id
     */
    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }

    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function frapper(Personnage $perso)
    {
        if ($perso->getId() == $this->getId()) {
            return self::PERSO_IDENTIQUE;
        }

        return $perso->recevoirDegats();

    }

    public function recevoirDegats()
    {
        $this->setDegats($this->getDegats() + 5);
        if ($this->getDegats() >= 100) {
            return self::PERSO_TUE;
        } else {
            return self::PERSO_FRAPPE;
        }
    }

    public function nomValide()
    {
        return !empty($this->_nom);
    }

    /**
     * @return mixed
     */
    public function getId() : int
    {
        return $this->_id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $id = (int) $id;
        if ($id > 0) {
            $this->_id = $id;
        }
    }

    /**
     * @return mixed
     */
    public function getNom() : string
    {
        return $this->_nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        if (is_string($nom)) {
            $this->_nom = $nom;
        }
    }

    /**
     * @return mixed
     */
    public function getDegats() : int
    {
        return $this->_degats;
    }

    /**
     * @param mixed $degats
     */
    public function setDegats($degats)
    {
        $degats = (int) $degats;
        if ($degats >= 0 && $degats <= 100) {
            $this->_degats = $degats;
        }
    }



}