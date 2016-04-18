<?php

class Team extends BaseModel {

    public $id, $name, $league_id, $elo;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public static function all() {

        $query = DB::connection()->prepare('SELECT * FROM Team');

        $query->execute();

        $rows = $query->fetchAll();
        $teams = array();

        foreach ($rows as $row) {
            $teams[] = new Team(array('id' => $row['id'],
                'name' => $row['name'],
                'elo' => $row['elo'],
                'league_id' => $row['league_id']));
        }

        return $teams;
    }

    public static function find($id) {

        $query = DB::connection()->prepare('SELECT * FROM Team WHERE id = :id LIMIT 1');

        $query->execute(array('id' => $id));

        $row = $query->fetch();

        if ($row) {
            $team = new Team(array('id' => $row['id'],
                'name' => $row['name'],
                'elo' => $row['elo'],
                'league_id' => $row['league_id']));

            return $team;
        }

        return null;
    }

    public static function all_by_elo() {

        $query = DB::connection()->prepare('SELECT * FROM Team ORDER BY elo DESC');

        $query->execute();

        $rows = $query->fetchAll();
        $teams = array();

        foreach ($rows as $row) {
            $teams[] = new Team(array('id' => $row['id'],
                'name' => $row['name'],
                'elo' => $row['elo'],
                'league_id' => $row['league_id']));
        }

        return $teams;
    }

    public function save() {

        $query = DB::connection()->prepare('INSERT INTO Team (name, league_id, elo) VALUES (:name, :league_id, :elo) RETURNING id');

        $query->execute(array('name' => $this->name, 'league_id' => $this->league_id, 'elo' => $this->elo));

        $row = $query->fetch();

        Kint::trace();
        Kint::dump($row);

        $this->id = $row['id'];
    }

}
