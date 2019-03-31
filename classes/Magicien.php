<?php


final class Magicien extends Personnage
{
    private $_magie;// Indique la puissance du magicien sur 100, sa capacité à produire de la magie.

    public function lancerUnSort(Personnage $perso)
    {
        $perso->recevoirDegats($this->_magie); // On va dire que la magie du magicien représente sa force.
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