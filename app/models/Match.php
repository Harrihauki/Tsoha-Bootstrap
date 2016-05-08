<?php

/**
 * Ottelun malli käsittelee otteluita koskevat toiminnot tietokannan ja ohjelmiston
 * rajapinnassa
 *
 * @author lallimyl
 */
class Match extends BaseModel {

    public $id, $home_id, $away_id, $date, $home_goals, $away_goals, $adder_id, $home_elo_before, $home_elo_after, $away_elo_before, $away_elo_after;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_team_existance',
            'validate_scores',
            'validate_date');
    }

    /**
     * Hakee kaikki ottelut tietokannasta
     * @return \Match Lista otteluista
     */
    public static function all() {

        $query = DB::connection()->prepare('SELECT * FROM Match');

        $query->execute();

        $rows = $query->fetchAll();
        $matches = array();

        foreach ($rows as $row) {
            $matches[] = new Match(array('id' => $row['id'],
                'home_id' => $row['home_id'],
                'away_id' => $row['away_id'],
                'date' => $row['date'],
                'home_goals' => $row['home_goals'],
                'away_goals' => $row['away_goals'],
                'adder_id' => $row['adder_id'],
                'home_elo_before' => $row['home_elo_before'],
                'away_elo_before' => $row['away_elo_before'],
                'home_elo_after' => $row['home_elo_after'],
                'away_elo_after' => $row['away_elo_after']));
        }

        return $matches;
    }

    /**
     * Etsii halutun ottelun tietokannasta id:n perusteella
     * @param type $id Halutun ottelun id.
     * @return \Match Haluttu ottelu tai null, jos ottelua ei ole.
     */
    public static function find($id) {

        $query = DB::connection()->prepare('SELECT * FROM Match WHERE id = :id LIMIT 1');

        $query->execute(array('id' => $id));

        $row = $query->fetch();

        if ($row) {
            $match = new Match(array('id' => $row['id'],
                'home_id' => $row['home_id'],
                'away_id' => $row['away_id'],
                'date' => $row['date'],
                'home_goals' => $row['home_goals'],
                'away_goals' => $row['away_goals'],
                'adder_id' => $row['adder_id'],
                'home_elo_before' => $row['home_elo_before'],
                'away_elo_before' => $row['away_elo_before'],
                'home_elo_after' => $row['home_elo_after'],
                'away_elo_after' => $row['away_elo_after']));

            return $match;
        }

        return null;
    }

    /**
     * Hakee kannasta kaikki ottelut, jossa haluttu joukkue on pelannut
     * @param type $teamid Joukkueen id.
     * @return \Match Lista otteluista, joissa haluttu joukkue on pelannut.
     */
    public static function find_by_team($teamid) {

        $query = DB::connection()->prepare('SELECT DISTINCT * FROM Match WHERE home_id = :id OR away_id = :id ORDER BY date DESC');

        $query->execute(array('id' => $teamid));

        $rows = $query->fetchAll();
        $matches = array();

        foreach ($rows as $row) {
            $matches[] = new Match(array('id' => $row['id'],
                'home_id' => $row['home_id'],
                'away_id' => $row['away_id'],
                'date' => $row['date'],
                'home_goals' => $row['home_goals'],
                'away_goals' => $row['away_goals'],
                'adder_id' => $row['adder_id'],
                'home_elo_before' => $row['home_elo_before'],
                'away_elo_before' => $row['away_elo_before'],
                'home_elo_after' => $row['home_elo_after'],
                'away_elo_after' => $row['away_elo_after']));
        }

        return $matches;
    }

    /**
     * Hakee ottelun, jossa haluttu joukkue on pelannut haluttuna päivänä
     * @param type $team haluttu joukkue
     * @param type $date haluttu päivä
     * @return \Match ottelu tai null, jos halutunlaista ottelua ei ole.
     */
    public static function find_by_team_and_date($team, $date) {

        $query = DB::connection()->prepare('SELECT DISTINCT * FROM Match WHERE (home_id = :id OR away_id = :id) AND date < :date ORDER BY date DESC LIMIT 1');

        $query->execute(array('id' => $team->id,
            'date' => $date));

        $row = $query->fetch();

        if ($row) {
            $match = new Match(array('id' => $row['id'],
                'home_id' => $row['home_id'],
                'away_id' => $row['away_id'],
                'date' => $row['date'],
                'home_goals' => $row['home_goals'],
                'away_goals' => $row['away_goals'],
                'adder_id' => $row['adder_id'],
                'home_elo_before' => $row['home_elo_before'],
                'away_elo_before' => $row['away_elo_before'],
                'home_elo_after' => $row['home_elo_after'],
                'away_elo_after' => $row['away_elo_after']));

            return $match;
        }

        return null;
    }

    /**
     * Tallentaa uuden ottelun teitokantaan.
     */
    public function save() {

        $query = DB::connection()->prepare('INSERT INTO Match (home_id, away_id, date, home_goals, away_goals, home_elo_before, home_elo_after, away_elo_before, away_elo_after) VALUES (:home_id, :away_id, :date, :home_goals, :away_goals, :home_elo_before, :home_elo_after, :away_elo_before, :away_elo_after) RETURNING id');

        $query->execute(array('home_id' => $this->home_id, 'away_id' => $this->away_id, 'date' => $this->date, 'home_goals' => $this->home_goals, 'away_goals' => $this->away_goals, 'home_elo_before' => $this->home_elo_before, 'home_elo_after' => $this->home_elo_before, 'away_elo_before' => $this->away_elo_before, 'away_elo_after' => $this->away_elo_after));

        $row = $query->fetch();

        $this->id = $row['id'];
    }

    /**
     * Validoi, onko ovatko kyseisen ottelun joukkueet olemassa.
     * @return string Mahdolliset virheilmoitukset
     */
    public function validate_team_existance() {
        $home_team = Team::find($this->home_id);
        $away_team = Team::find($this->away_id);
        $errors = array();

        if ($home_team == NULL) {
            $errors[] = 'Kotijoukkuetta ei ole olemassa!';
        }

        if ($away_team == NULL) {
            $errors[] = 'Vierasjoukkuetta ei ole olemassa!';
        }

        if ($home_team->id == $away_team->id) {
            $errors[] = 'Joukkue ei voi pelata itseään vastaan!';
        }

        return $errors;
    }

    /**
     * Tarkistaa, että maalimäärät ovat kelvolliset.
     * @return string Mahdolliset virheilmoitukset
     */
    public function validate_scores() {

        $errors = array();

        if ($this->home_goals < 0 OR $this->away_goals < 0) {
            $errors[] = 'Maalimäärä ei voi olla negatiivinen!';
        }

        return $errors;
    }

    /**
     * Tarkistaa, että päivämäärä on kelpo.
     * @return string Mahdolliset virheilmoitukset
     */
    public function validate_date() {

        $errors = array();

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->date)) {
            $errors[] = 'Päivämäärä väärin!';
        }

        return $errors;
    }

    /**
     * Päivittää kaikkien syötteenä tulevan ottelun jälkeen pelattujen otteluiden
     * vertailulukutiedot ja samalla ottelun jälkeen pelanneiden joukkueiden
     * vertailuluvut.
     * @param type $match Ottelu, jonka jälkeen pelatut ottelut päivitetään.
     */
    public function update_all_elos($match) {

        $query = DB::connection()->prepare('SELECT * FROM Match WHERE date >= :date ORDER BY date ASC');

        $query->execute(array('date' => $match->date));

        $rows = $query->fetch();


        foreach ($rows as $row) {

            $home_team = Team::find($row['home_id']);
            $away_team = Team::find($row['away_id']);

            if ($home_team != null && $away_team != null) {
                $home_goals = $row['home_goals'];
                $away_goals = $row['away_goals'];

                $results = MatchController::results($home_goals, $away_goals);
                $home_result = $results['home_result'];
                $away_result = $results['away_result'];

                $updated_match = new Match(array('id' => $row['id'],
                    'home_id' => $row['home_id'],
                    'away_id' => $row['away_id'],
                    'date' => $row['date'],
                    'home_goals' => $home_goals,
                    'away_goals' => $away_goals,
                    'adder_id' => $row['adder_id'],
                    'home_elo_before' => $home_team->elo,
                    'away_elo_before' => $away_team->elo,
                    'home_elo_after' => $home_team->elo + MatchController::new_elo($home_result, $home_team->elo + 100, $away_team->elo, $row['home_goals'], $row['away_goals']),
                    'away_elo_after' => $away_team->elo + MatchController::new_elo($away_result, $away_team->elo, $home_team->elo + 100, $row['home_goals'], $row['away_goals'])));

                $updated_match->update();

                $updated_home_team = new Team(array('id' => $home_team->id,
                    'name' => $home_team->name,
                    'league_id' => $home_team->league_id,
                    'elo' => $updated_match->home_elo_after));

                $updated_away_team = new Team(array('id' => $away_team->id,
                    'name' => $away_team->name,
                    'league_id' => $away_team->league_id,
                    'elo' => $updated_match->away_elo_after));

                $updated_home_team->update();
                $updated_away_team->update();
            }
        }
    }

    /**
     * Päivittää mallia tietokannassa vastaavan ottelun tiedot halutuiksi.
     */
    public function update() {

        $query = DB::connection()->prepare('UPDATE Match SET home_id = :home_id, away_id = :away_id, date = :date, home_goals = :home_goals, away_goals = :away_goals, home_elo_before = :home_elo_before, home_elo_after = :home_elo_after, away_elo_before = :away_elo_before, away_elo_after = :away_elo_after WHERE id = :id');

        $query->execute(array('home_id' => $this->home_id,
            'away_id' => $this->away_id,
            'date' => date('Y-m-d', $this->date),
            'home_goals' => $this->home_goals,
            'away_goals' => $this->away_goals,
            'home_elo_before' => $this->home_elo_before,
            'home_elo_after' => $this->home_elo_after,
            'away_elo_before' => $this->away_elo_before,
            'away_elo_after' => $this->away_elo_after,
            'id' => $this->id));
    }

    /**
     * Poistaa mallia vastaavan ottelun tietokannasta.
     */
    public function destroy() {

        $query = DB::connection()->prepare('DELETE FROM Match WHERE id = :id');

        $query->execute(array('id' => $this->id));
    }

}
