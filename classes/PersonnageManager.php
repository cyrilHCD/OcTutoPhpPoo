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
        $query = $this->_db->prepare("INSERT INTO personnages(nom) VALUES(:nom)");
        $query->bindValue(':nom', $perso->getNom());
        $query->execute();

        $perso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'degats' => 0,
        ]);
    }

    public function count()
    {
        return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
    }

    public function read($info)
    {
        if (is_int($info)) {
            $q = $this->_db->query('SELECT id, nom, degats FROM personnages WHERE id = '.$info);
            $donnees = $q->fetch(PDO::FETCH_ASSOC);

            return new Personnage($donnees);
        } else {
            $q = $this->_db->prepare('SELECT id, nom, degats FROM personnages WHERE nom = :nom');
            $q->execute([':nom' => $info]);

            return new Personnage($q->fetch(PDO::FETCH_ASSOC));
        }
    }

    public function getList($nom)
    {
        $persos = [];

        $q = $this->_db->prepare('SELECT id, nom, degats FROM personnages WHERE nom <> :nom ORDER BY nom');
        $q->execute([':nom' => $nom]);

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $persos[] = new Personnage($donnees);
        }

        return $persos;
    }

    public function update(Personnage $perso)
    {
        $q = $this->_db->prepare('UPDATE personnages SET degats = :degats WHERE id = :id');

        $q->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
        $q->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

        $q->execute();
    }

    public function delete(Personnage $perso)
    {
        $this->_db->exec('DELETE FROM personnages WHERE id = '.$perso->id());
    }

    public function exists($info)
    {
        if (is_int($info)) {// On veut voir si tel personnage ayant pour id $info existe.
            return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages WHERE id = '.$info)->fetchColumn();
        }

        // Sinon, c'est qu'on veut vérifier que le nom existe ou pas.

        $q = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
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