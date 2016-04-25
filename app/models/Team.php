<?php

class Team extends BaseModel {

    public $id, $name, $league_id, $elo;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_name',
            'validate_existance');
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

        $this->id = $row['id'];
    }
    
    public function update() {

        $query = DB::connection()->prepare('UPDATE Team SET name = :name, league_id = :league_id, elo = :elo WHERE id = :id');

        $query->execute(array('name' => $this->name, 'league_id' => $this->league_id, 'elo' => $this->elo, 'id' => $this->id));
    }
    
    public function destroy() {
        
        $query = DB::connection()->prepare('DELETE FROM Team WHERE id = :id');
        
        $query->execute(array('id' => $this->id));
    }
    
    public function validate_name() {
        $errors = $this->validate_string_length($this->name, 3);
        
        return $errors;
    }
    
    public function validate_existance() {
        $team = $this->find_by_name($this->name);
        $errors = array();
        
        if($team != NULL) {
            $errors[] = 'Joukkue on jo olemassa!';
        }
        
        return $errors;
    }

    public function find_by_name($name) {
        $query = DB::connection()->prepare('SELECT * FROM Team WHERE name = :name LIMIT 1');

        $query->execute(array('name' => $name));

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
    
    public function find_elo($id) {
        $query = DB::connection()->prepare('SELECT elo FROM Team WHERE id = :id LIMIT 1');
        
        $query->execute(array('id' => $id));
        
        $row = $query->fetch();
        
        if ($row) {
            $elo = array('elo' => $row['elo']);
            
            return $elo;
        }
        
        return null;
    }

}
