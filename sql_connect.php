<?php

$connexion = new Connexion('localhost', 'root', '', 'test');

$_SESSION['connexion'] = serialize($connexion);

// Chargement des classes
spl_autoload_register(function($class){
    require "classes/" . $class.'.php';
});