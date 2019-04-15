<?php


abstract class Personnage
{
    protected $id;
    protected $nom;
    protected $degats;
    protected $timeEndormi;
    protected $atout;
    protected $experience;
    protected $type;

    const PERSO_TUE = 1;
    const PERSO_FRAPPE = 2;
    const PERSO_IDENTIQUE = 3;
    const PERSO_ENSORCELE = 4;// Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on a bien ensorcelé un personnage.
    const PAS_DE_MAGIE = 5; // Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on veut jeter un sort alors que la magie du magicien est à 0.
    const PERSO_ENDORMI = 6; // Constante renvoyée par la méthode `frapper` si le personnage qui veut frapper est endormi.
    /**
     * Personnage constructor.
     * @param $id
     */
    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
        $this->type = strtolower(static::class);
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
        if ($this->estEndormi()) {
            return self::PERSO_ENDORMI;
        }

        // On indique au personnage qu'il doit recevoir des dégâts.
        // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE.
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

    public function estEndormi()
    {
        return $this->getTimeEndormi() > time();
    }

    public function reveil()
    {
        $secondes = $this->getTimeEndormi();
        $secondes -= time();

        $heures = floor($secondes / 3600);
        $secondes -= $heures * 3600;
        $minutes = floor($secondes / 60);
        $secondes -= $minutes * 60;

        $heures .= $heures <= 1 ? ' heure' : ' heures';
        $minutes .= $minutes <= 1 ? ' minute' : ' minutes';
        $secondes .= $secondes <= 1 ? ' seconde' : ' secondes';

        return $heures . ', ' . $minutes . ' et ' . $secondes;
    }

    public function gagnerExperience()
    {
        // On ajoute 1 à notre attribut $experience.
        $this->_experience++;
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

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTimeEndormi() : int
    {
        return $this->timeEndormi;
    }

    /**
     * @param mixed $timeEndormi
     */
    public function setTimeEndormi($timeEndormi)
    {
        $this->timeEndormi = $timeEndormi;
    }

    /**
     * @return mixed
     */
    public function getAtout() : int
    {
        return $this->atout;
    }

    /**
     * @param mixed $atout
     */
    public function setAtout($atout)
    {
        $this->atout = $atout;
    }



}