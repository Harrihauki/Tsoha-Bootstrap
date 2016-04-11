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

}
