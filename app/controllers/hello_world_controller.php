<?php

class HelloWorldController extends BaseController {

    public static function index() {
        // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
        View::make('suunnitelmat/etusivu.html');
    }

    public static function sandbox() {
        // Testaa koodiasi täällä
        $matches = Match::all();
        Kint::dump($matches);
    }

    public static function team_list() {
        $teams = Team::all_by_elo();
        
        View::make('teams/index.html', array('teams' => $teams));
    }
    
    public static function esittely() {
        View::make('suunnitelmat/esittely.html');
    }
    
    public static function ottelu() {
        View::make('suunnitelmat/ottelu.html');
    }

    public static function team_add() {
        View::make('suunnitelmat/team_add.html');
    }
    
    public static function match_add() {
        View::make('suunnitelmat/match_add.html');
    }
    
    public static function team_edit() {
        View::make('suunnitelmat/team_edit.html');
    }
    
    public static function match_edit() {
        View::make('suunnitelmat/match_edit.html');
    }
}
