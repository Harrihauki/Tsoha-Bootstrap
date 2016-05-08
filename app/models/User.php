<?php

/**
 * Joukkueen malli käsittelee joukkueita koskevat toiminnot tietokannan ja ohjelmiston
 * rajapinnassa
 *
 * @author lallimyl
 */
class User extends BaseModel {
    
    public $id, $username;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_username',
            'validate_existance');
    }
    
    /**
     * Tallentaa käyttäjän tietokantaan
     * @param type $password Käyttäjän valitsema salasana
     */
    public function save($password) {
        $query = DB::connection()->prepare('INSERT INTO RegisteredUser (username, password) VALUES (:username, :password) RETURNING id');
        $query->execute(array('username' => $this->username,
            'passwprd' => $password));
        
        $row = $query->fetch();
        
        $this->id = $row['id'];
    }
    
    /**
     * Tarkistaa, että kirjautuva käyttäjä on olemassa ja antaa oikean salasanan
     * @param type $username Käyttäjän syöttämä käyttäjänimi
     * @param type $password Käyttäjän syöttämä salasana
     * @return \User Palauttaa käyttäjän tai null, jos käyttäjää ei ole olemassa
     */
    public static function authenticate($username, $password) {
        $query = DB::connection()->prepare('SELECT * FROM Registereduser WHERE username = :username AND password = :password LIMIT 1');
        $query->execute(array('username' => $username, 'password' => $password));
        
        $row = $query->fetch();
        
        if ($row) {
            $user = new User(array('username' => $row['username'],
                'id' => $row['id']));
            
            return $user;
        } else {
            return null;
        }
    }
    
    /**
     * Hakee kannasta käyttäjän id:n perusteella
     * @param type $id Haluttu id
     * @return \User Käyttäjä tai null, jos käyttäjää ei ole.
     */
    public static function find($id) {

        $query = DB::connection()->prepare('SELECT * FROM Registereduser WHERE id = :id LIMIT 1');

        $query->execute(array('id' => $id));

        $row = $query->fetch();

        if ($row) {
            $team = new User(array('id' => $row['id'],
                'username' => $row['username']));

            return $team;
        }

        return null;
    }
    
    /**
     * Tarkistaa, onko käyttäjän haluama käyttäjänimi tarpeeksi pitkä
     * @return type mahdolliset virheilmoitukset
     */
    public function validate_username() {
        $errors = $this->validate_string_length($this->username, 3);

        return $errors;
    }
    
    /**
     * Tarkistaa, onko käyttäjänimi varattu
     * @return string Mahdolliset virheilmoitukset
     */
    public function validate_existance() {
        $user = $this->find_by_username($this->username);
        $errors = array();

        if ($user != NULL) {
            $errors[] = 'Käyttäjätunnus on jo käytössä!';
        }

        return $errors;
    }
    
    /**
     * Hakee käyttäjän kannasta käyttäjänimen perusteella.
     * @param type $username Haluttu käyttäjänimi
     * @return \User Käyttäjä tai null, jos käyttäjää ei ole
     */
    public static function find_by_username($username) {
        $query = DB::connection()->prepare('SELECT * FROM RegisteredUser WHERE username = :username LIMIT 1');

        $query->execute(array('username' => $username));

        $row = $query->fetch();

        if ($row) {
            $user = new User(array('id' => $row['id'],
                'username' => $row['username']));

            return $user;
        }

        return null;
    }
}
