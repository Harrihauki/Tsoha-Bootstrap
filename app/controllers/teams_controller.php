<?php

/**
 * Joukkuekontrolleri käsittelee joukkueita koskevat käskyt
 *
 * @author lallimyl
 */
class TeamsController extends BaseController {

    /**
     * Avaa joukkueiden listaussivun
     */
    public static function index() {
        $teams = Team::all_by_elo();
        View::make('teams/index.html', array('teams' => $teams));
    }

    /**
     * Avaa halutun joukkueen näkymän
     * @param type $id Näytettävän joukkueen id.
     */
    public static function show($id) {
        $team = Team::find($id);
        $matches = Match::find_by_team($id);

        View::make('teams/team.html', array('team' => $team,
            'matches' => $matches));
    }

    /**
     * Tallentaa parametrien mukaisen joukkueen tietokantaan tai antaa virhe-
     * ilmoituksen
     */
    public static function store() {
        
        $params = $_POST;
        
        $attributes = array(
            'name' => $params['name'],
            'league_id' => '1',
            'elo' => '1000'
        );
        
        $team = new Team($attributes);
        
        $errors = $team->errors();

        if(count($errors) == 0) {
            $team->save();
            Redirect::to('/team/' . $team->id, array('messages' => 'Joukkue lisätty tietokantaan!'));
        } else {
            Redirect::to('/teams/new', array('errors' => $errors, 'attributes' => $attributes['name']));
        }
    }
    
    /**
     * Avaa joukkueen lisäysnäkymän
     */
    public static function create() {
        
        View::make('teams/new.html');
    }
    
    /**
     * Avaa joukkueen muokkausnäkymän
     * @param type $id muokattavan joukkueen id.
     */
    public static function edit($id) {
        $team = Team::find($id);
        View::make('teams/edit.html', array('attributes' => $team));
    }
    
    /**
     * Päivittää joukkueen parametrien mukaiseksi tietokantaan tai antaa virhe-
     * ilmoityksen
     * @param type $id muokattavan joukkueen id.
     */
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
    
    /**
     * Poistaa halutun joukkueen tietokannasta.
     * @param type $id Poistettavan joukkueen id.
     */
    public static function destroy($id) {
        
        $team = new Team(array('id' => $id));
        
        $team->destroy();
        
        Redirect::to('/teams', array('messages' => 'Joukkue poistettu onnistuneesti!'));
    }
}