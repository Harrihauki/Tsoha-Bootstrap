<?php

/**
 * Ottelukonrtrolleri-luokka vastaa otteluita käsittelevistä käskyistä
 */
class MatchController extends BaseController {

    /**
     * Avaa oikean näkymän halutulle ottelulle.
     * @param type $id halutun ottelun id
     */
    public static function show($id) {
        $match = Match::find($id);
        $teams = Team::find_by_match($match);

        View::make('match/match.html', array('match' => $match,
            'home' => $teams['home'],
            'away' => $teams['away']));
    }

    /**
     * Avaa ottelun lisäyssivun
     */
    public static function create() {

        View::make('match/new.html');
    }

    /**
     * Luo parametrien mukaisen ottelun ja lisää sen tietokantaan tai antaa
     * virheilmoituksen
     */
    public static function store() {

        $params = $_POST;

        $date_errors = array();
        $date_errors = array_merge($date_errors, MatchController::validate_date($params['date']));

        if (count($date_errors) != 0) {
            Redirect::to('/match/new_match', array('errors' => $date_errors));
        }

        $home_id = Team::find_id_by_name($params['home_name']);
        $away_id = Team::find_id_by_name($params['away_name']);

        Team::prepare_elos($params['date']);
        $home_elo_before = Team::find($home_id)->elo;
        $away_elo_before = Team::find($away_id)->elo;
        $home_goals = $params['home_goals'];
        $away_goals = $params['away_goals'];
        $home_result;
        $away_result;

        if ($params['home_goals'] - $params['away_goals'] == 0) {
            $home_result = 0.5;
            $away_result = 0.5;
        } else if ($params['home_goals'] - $params['away_goals'] < 0) {
            $home_result = 0;
            $away_result = 1;
        } else {
            $home_result = 1;
            $away_result = 0;
        }

        $home_elo_after = $home_elo_before + MatchController::new_elo($home_result, $home_elo_before, $away_elo_before, $home_goals, $away_goals);
        $away_elo_after = $away_elo_before + MatchController::new_elo($away_result, $home_elo_before, $away_elo_before, $home_goals, $away_goals);

        $attributes = array(
            'home_id' => $home_id,
            'away_id' => $away_id,
            'home_goals' => $home_goals,
            'away_goals' => $away_goals,
            'date' => $params['date'],
            'home_elo_before' => $home_elo_before,
            'away_elo_before' => $away_elo_before,
            'home_elo_after' => $home_elo_after,
            'away_elo_after' => $away_elo_after
        );

        $match = new Match($attributes);

        $errors = $match->errors();
//        Kint::dump($errors);
//        die();
//        Kint::dump($params);
        if (count($errors) == 0) {
            $match->save();
            Match::update_all_elos($match);
            Redirect::to('/match/' . $match->id, array('messages' => 'Ottelu lisätty tietokantaan!'));
        } else {
            Match::update_all_elos($match);
            Redirect::to('/match/new_match', array('errors' => $errors, 'attributes' => $attributes, 'home_name' => $params['home_name'], 'away_name' => $params['away_name']));
        }
    }

    /**
     * Avaa ottelun muokkaussivun
     * @param type $id muokattavan ottelun id.
     */
    public static function edit($id) {
        $match = Match::find($id);
        $teams = Team::find_by_match($match);
        $home_team = $teams['home'];
        $away_team = $teams['away'];

        View::make('match/edit.html', array('attributes' => $team,
            'home_name' => $home_team->name,
            'away_name' => $away_team->name));
    }

    /**
     * Muokkaa halutun ottelun parametrien mukaiseksi tai antaa virheilmoituksen
     * @param type $id muokattavan ottelun id.
     */
    public static function update($id) {
        $params = $_POST;

        $date_errors = array();
        $date_errors = array_merge($date_errors, MatchController::validate_date($params['date']));

        if (count($date_errors) != 0) {
            $old_match = Match::find($id);
            Redirect::to('/match/edit.html', array('errors' => $errors, 'attributes' => $old_match,
                'home_name' => Team::find($old_match->home_id),
                'away_name' => Team::find($old_match->away_id)));
        }

        $home_id = Team::find_id_by_name($params['home_name']);
        $away_id = Team::find_id_by_name($params['away_name']);

        Team::prepare_elos($params['date']);
        $home_elo_before = Team::find($home_id)->elo;
        $away_elo_before = Team::find($away_id)->elo;
        $home_goals = $params['home_goals'];
        $away_goals = $params['away_goals'];
        
        $results = MatchController::results($home_goals, $away_goals);
        $home_result = $results['home_result'];
        $away_result = $results['away_result'];

        $home_elo_after = $home_elo_before + MatchController::new_elo($home_result, $home_elo_before + 100, $away_elo_before, $home_goals, $away_goals);
        $away_elo_after = $away_elo_before + MatchController::new_elo($away_result, $away_elo_before, $home_elo_before + 100, $home_goals, $away_goals);

        $attributes = array(
            'id' => $id,
            'home_id' => $home_id,
            'away_id' => $away_id,
            'home_goals' => $home_goals,
            'away_goals' => $away_goals,
            'date' => $params['date'],
            'home_elo_before' => $home_elo_before,
            'away_elo_before' => $away_elo_before,
            'home_elo_after' => $home_elo_after,
            'away_elo_after' => $away_elo_after
        );

        $match = new Match($attributes);

        $errors = $match->errors();
//        Kint::dump($errors);
//        die();
//        Kint::dump($params);
        if (count($errors) == 0) {
            $match->update();
            Match::update_all_elos($match);
            Redirect::to('/match/' . $match->id, array('messages' => 'Ottelun muokkaus onnistui!'));
        } else {
            Match::update_all_elos($match);
            $old_match = Match::find($id);
            Redirect::to('/match/edit.html', array('errors' => $errors, 'attributes' => $old_match,
                'home_name' => Team::find($old_match->home_id),
                'away_name' => Team::find($old_match->away_id)));
        }
    }

    /**
     * Poistaa ottelun tietokannasta
     * @param type $id poistettavan ottelun id.
     */
    public static function destroy($id) {

        $match = Match::find($id);

        Team::prepare_elos($match->date);
        $match->destroy();
        Match::update_all_elos($match);

        Redirect::to('/teams', array('messages' => 'Ottelu poistettu onnistuneest!'));
    }

    /**
     * Tarkistaa annetun päivämäärän oikean muodon
     * @param type $date päivämäärä
     * @return string mahdolliset virheilmoitukset
     */
    public static function validate_date($date) {

        $errors = array();

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            $errors[] = 'Päivämäärä väärin!';
        }

        return $errors;
    }

    /**
     * Laskee vertailuluvun muutoksen ottelun jälkeen. Kotijoukkueen eloon lisä-
     * tään kutsuttaessa aina 100.
     * 
     * @param type $result ottelun tulos joukkueen kannalta (voitto(1.0), tasapeli(0.5), tappio(0))
     * @param type $first_team_elo Sen joukkueen vertailuluku, jolle muutosta lasketaan
     * @param type $second_team_elo Toisen joukkueen elo
     * @param type $home_goals kotijoukkueen maalimäärä ottelussa
     * @param type $away_goals vierasjoukkueen maalimäärä ottelussa
     * @return int vertailuluvun muutos 
     */
    public static function new_elo($result, $first_team_elo, $second_team_elo, $home_goals, $away_goals) {

        $difference = Match::number_of_goals($home_goals, $away_goals);
        $expected_result = Match::expected_result($first_team_elo, $second_team_elo);

        return round(30 * $difference * ($result - $expected_result));
    }

    /**
     * Laskee ottelun maalieron perusteella kertoimen uutta vertailulukua varten
     * @param type $home_goals kotijoukkueen ottelussa tekemät maalit
     * @param type $away_goals vierasjoukkueen ottelussa tekemät maalit
     * @return real|int kerroin
     */
    private static function number_of_goals($home_goals, $away_goals) {

        $difference = abs($home_goals - $away_goals);

        if ($difference <= 1) {
            return 1;
        } else if ($difference == 2) {
            return 1.5;
        } else {
            return (11 + $difference) / 8;
        }
    }

    /**
     * Laskee vanhojen vertailulukujen perusteella odotetun lopputuloksen. 0.5
     * tarkoittaa tasapeliä, pienempi luku tappiota ja isompi voittoa
     * @param type $first_team_elo Sen joukkueen vertailuluku, jolle muutosta lasketaan
     * @param type $second_team_elo Toisen joukkueen elo
     * @return float Ottelun odotustulos ensimmäiselle joukkueelle
     */
    private static function expected_result($first_team_elo, $second_team_elo) {

        $difference = $first_team_elo - $second_team_elo;

        return (1 / (pow(10, -($difference / 400)) + 1));
    }

    /**
     * Palauttaa ottelun lopputuloksen. 1 = voitto, 0.5 = tasapeli ja 0 = tappio
     * @param int $home_goals Kotijoukkueen maalimäärä ottelussa
     * @param int $away_goals Vierasjoukkueen maalimäärä ottelussa
     * @return array Ottelun lopputulokset kummallekin joukkueelle
     */
    public static function results($home_goals, $away_goals) {
        $home_result;
        $away_result;

        if ($home_goals - $away_goals == 0) {
            $home_result = 0.5;
            $away_result = 0.5;
        } else if ($home_goals - $away_goals < 0) {
            $home_result = 0;
            $away_result = 1;
        } else {
            $home_result = 1;
            $away_result = 0;
        }
        
        $results = array('home_result' => $home_result,
            'away_result' => $away_result);
        
        return results;
    }

}
