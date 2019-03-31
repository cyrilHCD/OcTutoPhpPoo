<?php

$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');
// On émet une alerte à chaque fois qu'une requête a échoué.
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

// Chargement des classes
function chargerClasse($classname) {
    require "classes/" . $classname.'.php';
}

spl_autoload_register('chargerClasse');