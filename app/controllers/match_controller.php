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
        
        $home_id = Team::find_id_by_name($params['home_name']);
        $away_id = Team::find_id_by_name($params['away_name']);
        
        $attributes = array(
            'home_id' => $home_id,
            'away_id' => $away_id,
            'home_goals' => $params['home_goals'],
            'away_goals' => $params['away_goals']
        );
        
        $match = new Match($attributes);
        
        //PALAA TEKEMÄÄN TÄMÄ LOPPUUN!!!
        
        $errors = $team->errors();
//        Kint::dump($errors);
//        die();
//        Kint::dump($params);
        if(count($errors) == 0) {
            $team->save();
            Redirect::to('/team/' . $team->id, array('messages' => 'Joukkue lisätty tietokantaan!'));
        } else {
            Redirect::to('/teams/new', array('errors' => $errors, 'attributes' => $attributes['name']));
        }
    }
}
