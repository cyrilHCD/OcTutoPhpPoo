<?php


class PersonnageManager
{
    private $_db;

    /**
     * PersonnageManager constructor.
     * @param $_db
     */
    public function __construct($_db)
    {
        $this->setDb($_db);
    }


    public function add(Personnage $perso)
    {
        $query = $this->_db->prepare("INSERT INTO personnages_v2(nom, type) VALUES(:nom, :type)");
        $query->bindValue(':nom', $perso->getNom());
        $query->bindValue(':type', $perso->getType());
        $query->execute();

        $perso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'nom' => $perso->getNom(),
            'degats' => 0,
            'atout' => 0,
            'timeEndormi' => 0,
        ]);
    }

    public function count()
    {
        return $this->_db->query('SELECT COUNT(*) FROM personnages_v2')->fetchColumn();
    }

    public function read($info)
    {
        if (is_int($info)) {
            $q = $this->_db->query('SELECT id, nom, degats, type FROM personnages_v2 WHERE id = '.$info);
            $donnees = $q->fetch(PDO::FETCH_ASSOC);

            if ($donnees["type"] == "magicien") {
                return new Magicien($donnees);
            } elseif ($donnees["type"] == "guerrier") {
                return new Guerrier($donnees);
            }

        } else {
            $q = $this->_db->prepare('SELECT id, nom, degats, type FROM personnages_v2 WHERE nom = :nom');
            $q->execute([':nom' => $info]);
            $donnees = $q->fetch(PDO::FETCH_ASSOC);

            if ($donnees["type"] == "magicien") {
                return new Magicien($donnees);
            } elseif ($donnees["type"] == "guerrier") {
                return new Guerrier($donnees);
            }
        }
    }

    public function getList($nom)
    {
        $persos = [];

        $q = $this->_db->prepare('SELECT id, nom, degats, type FROM personnages_v2 WHERE nom <> :nom ORDER BY nom');
        $q->execute([':nom' => $nom]);

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            if ($donnees["type"] == "magicien") {
                $persos[] = new Magicien($donnees);
            } elseif ($donnees["type"] == "guerrier") {
                $persos[] = new Guerrier($donnees);
            }
        }

        return $persos;
    }

    public function update(Personnage $perso)
    {
        $q = $this->_db->prepare('UPDATE personnages_v2 SET degats = :degats, timeEndormi = :timeEndormi, atout = :atout WHERE id = :id');

        $q->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
        $q->bindValue(':id', $perso->getId(), PDO::PARAM_INT);
        $q->bindValue(':timeEndormi', $perso->getTimeEndormi(), PDO::PARAM_INT);
        $q->bindValue(':atout', $perso->getAtout(), PDO::PARAM_INT);

        $q->execute();
    }

    public function delete(Personnage $perso)
    {
        $this->_db->exec('DELETE FROM personnages_v2 WHERE id = '.$perso->id());
    }

    public function exists($info)
    {
        if (is_int($info)) {// On veut voir si tel personnage ayant pour id $info existe.
            return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages_v2 WHERE id = '.$info)->fetchColumn();
        }

        // Sinon, c'est qu'on veut vérifier que le nom existe ou pas.

        $q = $this->_db->prepare('SELECT COUNT(*) FROM personnages_v2 WHERE nom = :nom');
        $q->execute([':nom' => $info]);

        return (bool) $q->fetchColumn();
    }

    /**
     * @return mixed
     */
    public function getDb() : PDO
    {
        return $this->_db;
    }


    /**
     * @param mixed $db
     */
    public function setDb($db)
    {
        $this->_db = $db;
    }



}