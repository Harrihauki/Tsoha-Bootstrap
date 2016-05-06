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
        $this->validators = array('validate_team_existance',
            'validate_scores',
            'validate_date');
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

        return round(30 * $difference * ($result - Match::expected_result($home_elo_before, $away_elo_before)));
    }

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

    private static function expected_result($home_elo_before, $away_elo_before) {

        $difference = abs($home_elo_before + 100 - $away_elo_before);

        return 1 / (pow(10, -$difference / 400) + 1);
    }

    public function save() {

        $query = DB::connection()->prepare('INSERT INTO Match (home_id, away_id, date, home_goals, away_goals, home_elo_before, home_elo_after, away_elo_before, away_elo_after) VALUES (:home_id, :away_id, :date, :home_goals, :away_goals, :home_elo_before, :home_elo_after, :away_elo_before, :away_elo_after) RETURNING id');

        $query->execute(array('home_id' => $this->home_id, 'away_id' => $this->away_id, 'date' => $this->date, 'home_goals' => $this->home_goals, 'away_goals' => $this->away_goals, 'home_elo_before' => $this->home_elo_before, 'home_elo_after' => $this->home_elo_before, 'away_elo_before' => $this->away_elo_before, 'away_elo_after' => $this->away_elo_after));

        $row = $query->fetch();

        $this->id = $row['id'];
    }

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

    public function validate_scores() {

        $errors = array();

        if ($this->home_goals < 0 OR $this->away_goals < 0) {
            $errors[] = 'Maalimäärä ei voi olla negatiivinen!';
        }

        return $errors;
    }

    public function validate_date() {

        $errors = array();

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->date)) {
            $errors[] = 'Päivämäärä väärin!';
        }

        return $errors;
    }

    public function update_all_elos($match) {

        $query = DB::connection()->prepare('SELECT * FROM Match WHERE date >= :date ORDER BY date ASC');

        $query->execute(array('date' => $match->date));

        $rows = $query->fetch();


        foreach ($rows as $row) {

            $home_team = Team::find($row['home_id']);
            $away_team = Team::find($row['away_id']);

            if ($home_team != null & $away_team != null) {
                $home_result;
                $away_result;

                if ($row['home_goals'] - $row['away_goals'] == 0) {
                    $home_result = 0.5;
                    $away_result = 0.5;
                } else if ($row['home_goals'] - $row['away_goals'] < 0) {
                    $home_result = 0;
                    $away_result = 1;
                } else {
                    $home_result = 1;
                    $away_result = 0;
                }

                $updated_match = new Match(array('id' => $row['id'],
                    'home_id' => $row['home_id'],
                    'away_id' => $row['away_id'],
                    'date' => $row['date'],
                    'home_goals' => $row['home_goals'],
                    'away_goals' => $row['away_goals'],
                    'adder_id' => $row['adder_id'],
                    'home_elo_before' => $home_team->elo,
                    'away_elo_before' => $away_team->elo,
                    'home_elo_after' => $home_team->elo + Match::new_elo($home_result, $home_team->elo, $away_team->elo, $row['home_goals'], $row['away_goals']),
                    'away_elo_after' => $away_team->elo + Match::new_elo($away_result, $home_team->elo, $away_team->elo, $row['home_goals'], $row['away_goals'])));

                $updated_match->update();

                $updated_home_team = new Team(array('id' => $home_team->id,
                    'league_id' => $home_team->league_id,
                    'name' => $home_team->name,
                    'elo' => $updated_match->home_elo_after));

                $updated_away_team = new Team(array('id' => $away_team->id,
                    'league_id' => $away_team->league_id,
                    'name' => $away_team->name,
                    'elo' => $updated_match->away_elo_after));

                $updated_home_team->update();
                $updated_away_team->update();
            }
        }
    }

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

}
