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
}
