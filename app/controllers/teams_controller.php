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
        
        $attributes = array(
            'name' => $params['name'],
            'league_id' => '1',
            'elo' => '1000'
        );
        
        $team = new Team($attributes);
        
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
    
    public static function create() {
        
        View::make('teams/new.html');
    }
    
    public static function edit($id) {
        $team = Team::find($id);
        View::make('teams/edit.html', array('attributes' => $team));
    }
    
    public static function update($id) {
        $params = $_POST;
        $team = Team::find($id);
        $attributes = array('id' => $team->id,
            'name' => $params['name'],
            'league_id' => $team->league_id,
            'elo' => $team->elo);
        $team = new Team($attributes);
        $errors = $team->errors();
        
        if(count($errors) > 0) {
            View::make('teams/edit.html', array('errors' => $errors, 'attributes' => $attributes));
        } else {
            $team->update();
            
            Redirect::to('/team/' . $team->id, array('messages' => 'Joukkueen muokkaus onnistui!'));
        }
    }
    
    public static function destroy($id) {
        
        $team = new Team(array('id' => $id));
        
        $team->destroy();
        
        Redirect::to('/teams', array('messages' => 'Joukkue poistettu onnistuneesti!'));
    }
}