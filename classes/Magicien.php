<?php


final class Magicien extends Personnage
{
    private $_magie;// Indique la puissance du magicien sur 100, sa capacité à produire de la magie.

    public function lancerUnSort(Personnage $perso)
    {
        if ($this->getDegats() >= 0 && $this->getDegats() <= 25) {
            $this->atout = 4;
        } elseif ($this->getDegats() > 25 && $this->getDegats() <= 50) {
            $this->atout = 3;
        } elseif ($this->getDegats() > 50 && $this->getDegats() <= 75) {
            $this->atout = 2;
        } elseif ($this->getDegats() > 75 && $this->getDegats() <= 90) {
            $this->setAtout(1);
        } else {
            $this->setAtout(0);
        }

        if ($perso->getId() == $this->getId()) {
            return self::PERSO_IDENTIQUE;
        }

        if ($this->getAtout() == 0) {
            return self::PAS_DE_MAGIE;
        }

        if ($this->estEndormi()) {
            return self::PERSO_ENDORMI;
        }

        $perso->setTimeEndormi(time() + ($this->atout * 6) * 3600);

        return self::PERSO_ENSORCELE;
    }

    public function gagnerExperience()
    {
        // On appelle la méthode gagnerExperience() de la classe parente
        parent::gagnerExperience();

        if ($this->_magie < 100) {
            $this->_magie += 10;
        }
    }

    public function endormir(Personnage $perso)
    {

    }
}