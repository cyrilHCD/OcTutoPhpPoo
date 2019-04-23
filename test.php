<?php
class MaClasse
{
    private $attributs = [];
    private $unAttributPrive;

    public function __get($nom)
    {
        if (isset($this->attributs[$nom]))
        {
            return $this->attributs[$nom];
        }
    }

    public function __set($nom, $valeur)
    {
        $this->attributs[$nom] = $valeur;
    }

    public function afficherAttributs()
    {
        echo '<pre>', print_r($this->attributs, true), '</pre>';
    }
}

$obj = new MaClasse;

$obj->attribut = 'Simple test';
$obj->unAttributPrive = 'Autre simple test';

echo $obj->attribut;
echo $obj->unAttributPrive;