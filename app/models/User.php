<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author lallimyl
 */
class User extends BaseModel {
    
    public $id, $username;
    
    public static function authenticate($username, $password) {
        $query = DB::connection()->prepare('SELECT * FROM Registereduser WHERE username = :username AND password = :password LIMIT 1');
        $query->execute(array('username' => $username, 'password' => $password));
        
        $row = $query->fetch();
        
        if ($row) {
            $user = new User(array('username' => $row['username'],
                'id' => $row['id']));
            
            return $user;
        } else {
            return null;
        }
    }
    
    public static function find($id) {

        $query = DB::connection()->prepare('SELECT * FROM Registereduser WHERE id = :id LIMIT 1');

        $query->execute(array('id' => $id));

        $row = $query->fetch();

        if ($row) {
            $team = new User(array('id' => $row['id'],
                'username' => $row['username']));

            return $team;
        }

        return null;
    }
}
