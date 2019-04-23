<?php
include("sql_connect.php");
session_start();

if (isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: .');
    exit();
}

// Si la session perso existe, on restaure l'objet.
if (isset($_SESSION['perso'])) {
    $perso = $_SESSION['perso'];
}


$manager = new PersonnageManager($connexion);

if (isset($_SESSION["perso"])) {
    $perso = $_SESSION["perso"];
}

// Si on a voulu créer un personnage
if (isset($_POST['creer']) && isset($_POST['nom']) && isset($_POST["type"])) {
    if ($_POST["type"] == "magicien") {
        $perso =  new Magicien($_POST);
    } elseif ($_POST["type"] == "guerrier") {
        $perso =  new Guerrier($_POST);
    } else {
        $message = 'Le type du personnage est invalide.';
    }

    if (isset($perso)) {
        if (!$perso->nomValide()) {
            $message = 'Le nom choisi est invalide.';
            unset($perso);
        } elseif ($manager->exists($perso->getNom())) {
            $message = 'Le nom du personnage est déjà pris.';
            unset($perso);
        } else {
            $manager->add($perso);
        }
    }
} elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) { // Si on a voulu utiliser un personnage.

    if ($manager->exists($_POST['nom'])) { // Si celui-ci existe.
        $perso = $manager->read($_POST['nom']);
    } else {
        $message = 'Ce personnage n\'existe pas !'; // S'il n'existe pas, on affichera ce message.
    }
} elseif (isset($_GET['frapper'])) { // Si on a cliqué sur un personnage pour le frapper.

    if (!isset($perso)) {
        $message = 'Merci de créer un personnage ou de vous identifier.';
    } else {

        if (!$manager->exists((int) $_GET['frapper'])) {
            $message = 'Le personnage que vous voulez frapper n\'existe pas !';
        } else {
            $persoAFrapper = $manager->read((int) $_GET['frapper']);

            $retour = $perso->frapper($persoAFrapper); // On stocke dans $retour les éventuelles erreurs ou messages que renvoie la méthode frapper.

            switch ($retour) {
                case Personnage::PERSO_IDENTIQUE :
                    $message = 'Mais... pourquoi voulez-vous vous frapper ???';
                    break;

                case Personnage::PERSO_FRAPPE :
                    $message = 'Le personnage a bien été frappé !';

                    $manager->update($perso);
                    $manager->update($persoAFrapper);

                    break;

                case Personnage::PERSO_TUE :
                    $message = 'Vous avez tué ce personnage !';

                    $manager->update($perso);
                    $manager->delete($persoAFrapper);

                    break;
                case Personnage::PERSO_ENDORMI:
                    $message = 'Vous êtes endormi, vous ne pouvez pas frapper de personnage !';
                    break;
            }
        }
    }
} elseif (isset($_GET['ensorceler'])) {
    if (!isset($perso)) {
        $message = 'Merci de créer un personnage ou de vous identifier.';
    } else {
        // Il faut bien vérifier que le personnage est un magicien.
        if ($perso->getType() != 'magicien') {
            $message = 'Seuls les magiciens peuvent ensorceler des personnages !';
        } else {

            if (!$manager->exists((int) $_GET['ensorceler'])) {
                $message = 'Le personnage que vous voulez frapper n\'existe pas !';
            } else {
                $persoAEnsorceler = $manager->read((int) $_GET['ensorceler']);
                var_dump($_GET['ensorceler']);
                $retour = $perso->lancerUnSort($persoAEnsorceler);
                var_dump($retour);
                switch ($retour)
                {
                    case Personnage::PERSO_IDENTIQUE :
                        $message = 'Mais... pourquoi voulez-vous vous ensorceler ???';
                        break;

                    case Personnage::PERSO_ENSORCELE :
                        $message = 'Le personnage a bien été ensorcelé !';

                        $manager->update($perso);
                        $manager->update($persoAEnsorceler);

                        break;

                    case Personnage::PAS_DE_MAGIE :
                        $message = 'Vous n\'avez pas de magie !';
                        break;

                    case Personnage::PERSO_ENDORMI :
                        $message = 'Vous êtes endormi, vous ne pouvez pas lancer de sort !';
                        break;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>TP : Mini jeu de combat</title>

    <meta charset="utf-8" />
</head>
<body>
<p>Nombre de personnages créés : <?= $manager->count() ?></p>
<?php
if (isset($message)) { // On a un message à afficher ?
    echo '<p>', $message, '</p>'; // Si oui, on l'affiche.
}

if (isset($perso)) { // Si on utilise un personnage (nouveau ou pas).
    ?>
    <p><a href="?deconnexion=1">Déconnexion</a></p>

    <fieldset>
        <legend>Mes informations</legend>
        <p>
            Type : <?= $perso->getType() ?><br>
            Nom : <?= htmlspecialchars($perso->getNom()) ?><br>
            Dégâts : <?= $perso->getDegats() ?><br>
        <?php if ($perso->getType() == "magicien") {?>
            Magie : <?= $perso->getAtout() ?><br>
        <?php } else {?>
            Protection : <?= $perso->getAtout() ?><br>
        <?php } ?>


        </p>
    </fieldset>

    <fieldset>
        <legend>Qui attaquer ?</legend>
        <p>
            <?php
            $adversaires = $manager->getList($perso->getNom());

            if (empty($adversaires)) {
                echo 'Personne à frapper !';
            } else {
                foreach ($adversaires as $unAdversaire) {
                    $lancerSort = "";
                    if ($perso->getType() == "magicien") {
                        $lancerSort = " | <a href='?ensorceler=".$unAdversaire->getId()."'>Lancer un sort</a>";
                    }
                    echo '<a href="?frapper=' . $unAdversaire->getId() . '">' .
                        htmlspecialchars($unAdversaire->getNom()) . '</a> (dégâts : ' . $unAdversaire->getDegats() . ' | type : ' .
                        $unAdversaire->getType() . ')' . $lancerSort . '<br />';
                }
            }
            ?>
        </p>
    </fieldset>
    <?php
} else {
    ?>
    <form action="" method="post">
        <p>
            <label for="nom">Nom : <input type="text" name="nom" id="nom" maxlength="50" />
                <input type="submit" value="Utiliser ce personnage" name="utiliser" />
            </label><br>

            <label for="type">Type :
                <select id="type" name="type">
                    <option value="magicien">Magicien</option>
                    <option value="guerrier">Guerrier</option>
                </select>
            </label>
            <input type="submit" value="Créer ce personnage" name="creer" />
        </p>
    </form>
    <?php
}
?>
</body>
</html>

<?php
if (isset($perso)) { // Si on a créé un personnage, on le stocke dans une variable session afin d'économiser une requête SQL.
    $_SESSION['perso'] = $perso;
}