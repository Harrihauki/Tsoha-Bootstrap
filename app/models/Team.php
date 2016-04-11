<?php

class Team extends BaseModel {
    
    public $id, $name, $league_id;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
    }
    
    public static function all() {
        
        $query = DB::connection()->prepare('SELECT * FROM Team');
        
        $query->execute();
        
        $rows = $query->fetchAll();
        $teams = array();
        
        foreach($rows as $row) {
            $Team[] = new Team(array('id' => $row['id'],
                'name' => $row['name'],
                'league_id' => $row['league_id']));
        }
        
        return $teams;
    }
    
    
}
