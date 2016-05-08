<?php
/**
 * Joukkueen malli käsittelee joukkueita koskevat toiminnot tietokannan ja ohjelmiston
 * rajapinnassa
 *
 * @author lallimyl
 */
class Team extends BaseModel {

    public $id, $name, $league_id, $elo;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_name',
            'validate_existance');
    }

    /**
     * Palauttaa kaikki joukkueet tietokannasta.
     * @return \Team Lista joukkueista
     */
    public static function all() {

        $query = DB::connection()->prepare('SELECT * FROM Team');

        $query->execute();

        $rows = $query->fetchAll();
        $teams = array();

        foreach ($rows as $row) {
            $teams[] = new Team(array('id' => $row['id'],
                'name' => $row['name'],
                'elo' => $row['elo'],
                'league_id' => $row['league_id']));
        }

        return $teams;
    }

    /**
     * Asettaa kaikkien joukkueiden vertailuluvut parametrina saatua päiväystä
     * edeltävälle tasolle
     * @param type $date Päiväys, jota edeltävälle tasolle vertailuluvut halutaan
     */
    public static function prepare_elos($date) {

        $teams = Team::all();

        foreach ($teams as $team) {
            Team::reset_elo($team, $date);
        }
    }

    /**
     * Asettaa halutun joukkueen vertailuluvun parametrina saatua päiväystä 
     * edeltävälle tasolle
     * @param type $team Joukkue, jonka vertailulukua muutetaan.
     * @param type $date Päiväys, jota edeltävälle tasolle vertailuluku halutaan
     */
    public static function reset_elo($team, $date) {

        $match = Match::find_by_team_and_date($team, $date);

        $elo;

        if ($match == null) {
            $elo = 1000;
        } else if ($team->id == $match->home_id) {
            $elo = $match->home_elo_after;
        } else {
            $elo = $match->away_elo_after;
        }

        $updated_team = new Team(array('id' => $team->id,
            'name' => $team->name,
            'elo' => $elo,
            'league_id' => $team->league_id));
        $updated_team->update();
    }

    /**
     * Hakee kannasta joukkueen id:n perusteella
     * @param type $id Halutun joukkueen id
     * @return \Team Haluttu joukkue tai null, jos joukkuetta ei ole olemassa
     */
    public static function find($id) {

        $query = DB::connection()->prepare('SELECT * FROM Team WHERE id = :id LIMIT 1');

        $query->execute(array('id' => $id));

        $row = $query->fetch();

        if ($row) {
            $team = new Team(array('id' => $row['id'],
                'name' => $row['name'],
                'elo' => $row['elo'],
                'league_id' => $row['league_id']));

            return $team;
        }

        return null;
    }

    /**
     * Hakee ottelussa pelanneet joukkueet kannasta
     * @param type $match
     * @return type Joukkueessa pelanneet joukkueet
     */
    public static function find_by_match($match) {

        $teams = array('home' => Team::find($match->home_id),
            'away' => Team::find($match->away_id));

        return $teams;
    }

    /**
     * Kaikki joukkueet järjestettynä vertailuluvun perusteella
     * @return \Team Lista joukkueista.
     */
    public static function all_by_elo() {

        $query = DB::connection()->prepare('SELECT * FROM Team ORDER BY elo DESC');

        $query->execute();

        $rows = $query->fetchAll();
        $teams = array();

        foreach ($rows as $row) {
            $teams[] = new Team(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'elo' => $row['elo'],
                'league_id' => $row['league_id']));
        }

        return $teams;
    }

    /**
     * Tallentaa mallia vastaavan joukkueen tietokantaan.
     */
    public function save() {

        $query = DB::connection()->prepare('INSERT INTO Team (name, league_id, elo) VALUES (:name, :league_id, :elo) RETURNING id');

        $query->execute(array('name' => $this->name, 'league_id' => $this->league_id, 'elo' => $this->elo));

        $row = $query->fetch();

        $this->id = $row['id'];
    }

    /**
     * Päivittää mallia vastaavan joukkueen ajan tasalle tietokannassa
     */
    public function update() {

        $query = DB::connection()->prepare('UPDATE Team SET name = :name, league_id = :league_id, elo = :elo WHERE id = :id');

        $query->execute(array('name' => $this->name,
            'league_id' => $this->league_id,
            'elo' => $this->elo,
            'id' => $this->id));
    }

    /**
     * Poistaa mallia vastaavan joukkueen tietokannasta
     */
    public function destroy() {

        $query = DB::connection()->prepare('DELETE FROM Team WHERE id = :id');

        $query->execute(array('id' => $this->id));
    }

    /**
     * Tarkistaa, onko joukkueen nimi tarpeeksi pitkä
     * @return type Mahdolliset virheilmoitukset
     */
    public function validate_name() {
        $errors = $this->validate_string_length($this->name, 3);

        return $errors;
    }

    /**
     * Tarkistaa, onko joukkue jo tietokannassa.
     * @return string mahdolliset virheilmoitukset
     */
    public function validate_existance() {
        $team = $this->find_by_name($this->name);
        $errors = array();

        if ($team != NULL) {
            $errors[] = 'Joukkue on jo olemassa!';
        }

        return $errors;
    }

    /**
     * Etsii joukkueen tietokannasta nimen perusteella
     * @param type $name Halutun joukkueen nimi
     * @return \Team Haluttu joukkue tai null, jos joukkuetta ei ole
     */
    public function find_by_name($name) {
        $query = DB::connection()->prepare('SELECT * FROM Team WHERE name = :name LIMIT 1');

        $query->execute(array('name' => $name));

        $row = $query->fetch();

        if ($row) {
            $team = new Team(array('id' => $row['id'],
                'name' => $row['name'],
                'elo' => $row['elo'],
                'league_id' => $row['league_id']));

            return $team;
        }

        return null;
    }

    /**
     * Etsii halutun id:n joukkueen vertailuluvun
     * @param type $id halutun joukkueen id.
     * @return type Halutun joukkueen vertailuluku tai null, jos joukkuetta ei ole
     */
    public function find_elo($id) {
        $query = DB::connection()->prepare('SELECT elo FROM Team WHERE id = :id LIMIT 1');

        $query->execute(array('id' => $id));

        $row = $query->fetch();

        if ($row) {
            $elo = array('elo' => $row['elo']);

            return $elo;
        }

        return null;
    }

    /**
     * Etsii halutunnimisen joukkueen id:n
     * @param type $name Halutun joukkueen nimi.
     * @return type Halutun joukkueen id tai null, jos joukkuetta ei ole.
     */
    public function find_id_by_name($name) {

        $team = Team::find_by_name($name);

        if ($team) {
            return $team->id;
        }

        return null;
    }

}
