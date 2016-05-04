<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Match
 *
 * @author lallimyl
 */
class Match extends BaseModel {

    public $id, $home_id, $away_id, $date, $home_goals, $away_goals, $adder_id, $home_elo_before, $home_elo_after, $away_elo_before, $away_elo_after;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

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
    
    public static function find_by_team_and_date($team, $date) {
        
        $query = DB::connection()->prepare('SELECT * FROM Match WHERE (home_id = :id OR away_id = :id) AND date < :date ORDER BY date DESC LIMIT 1');
        
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
    
    public static function new_elo($result, $home_elo_before, $away_elo_before, $home_goals, $away_goals) {
        
        $difference = Match::number_of_goals($home_goals, $away_goals);
        
        return round(30*$difference*($result - Match::expected_result($home_elo_before, $away_elo_before)));
    }
    
    private static function number_of_goals($home_goals, $away_goals) {
        
        $difference = abs($home_goals - $away_goals);
        
        if ($difference <= 1) {
            return 1;
        } else if ($difference == 2) {
            return 1.5;
        } else {
            return (11 + $difference)/8;
        }
    }
    
    private static function expected_result($home_elo_before, $away_elo_before) {
        
        $difference = abs($home_elo_before+100 - $away_elo_before);
        
        return 1/(pow(10, -$difference/400)+1);
    }

}
