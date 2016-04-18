<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of teams_controller
 *
 * @author lallimyl
 */
class TeamsController extends BaseController {

    public static function index() {
        $teams = Team::all_by_elo();

        View::make('teams/index.html', array('teams' => $teams));
    }

    public static function show($id) {
        $team = Team::find($id);
        $matches = Match::find_by_team($id);

        View::make('teams/team.html', array('team' => $team,
            'matches' => $matches));
    }

    public static function store() {
        
        $params = $_POST;
        
        $team = new Team(array(
            'name' => $params['name'],
            'league_id' => '1',
            'elo' => '1000'
        ));

//        Kint::dump($params);
        
        $team->save();
        
        Redirect::to('/team/' . $team->id, array('message' => 'Joukkue lisätty tietokantaan!'));
    }
    
    public static function create() {
        
        View::make('teams/new.html');
    }
}