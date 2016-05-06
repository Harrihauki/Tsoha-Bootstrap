<?php

class MatchController extends BaseController {

    public static function show($id) {
        $match = Match::find($id);
        $teams = Team::find_by_match($match);

        View::make('match/match.html', array('match' => $match,
            'home' => $teams['home'],
            'away' => $teams['away']));
    }

    public static function create() {

        View::make('match/new.html');
    }

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

        $home_elo_after = $home_elo_before + Match::new_elo($home_result, $home_elo_before, $away_elo_before, $home_goals, $away_goals);
        $away_elo_after = $away_elo_before + Match::new_elo($away_result, $home_elo_before, $away_elo_before, $home_goals, $away_goals);

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
            Redirect::to('/match/new_match', array('errors' => $errors));
        }
    }
    
    public static function validate_date($date) {
        
        $errors = array();

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
            $errors[] = 'Päivämäärä väärin!';
        }

        return $errors;
    }

}
