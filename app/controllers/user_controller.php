<?php

/**
 * Käyttäjäkontrolleri käsittelee käyttäjiä koskevat käskyt
 *
 * @author lallimyl
 */
class UserController extends BaseController {
    
    /**
     * Avaa kirjautumisnäkymän
     */
    public static function login() {
        View::make('user/login.html');
    }
    
    /**
     * Tarkistaa kirjautumisen oikeellisuuden ja kirjaa käyttäjän sisään tai
     * antaa virheilmoituksen
     */
    public static function handle_login() {
        $params = $_POST;
        
        $user = User::authenticate($params['username'], $params['password']);
        
        if (!$user) {
            View::make('user/login.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'username' => $params['username']));
        } else {
            $_SESSION['user'] = $user->id;
            Redirect::to('/', array('messages' => 'Tervetuloa ' . $user->username . '!'));
        }
    }
    
    /**
     * Avaa rekisteröitymissivun
     */
    public static function register() {
        View::make('user/register.html');
    }
    
    /**
     * Käsittelee rekisteröitymisen ja lisää käyttäjän tietokantaan tai antaa
     * virheilmoituksen
     */
    public static function handle_register() {
        $params = $_POST;
        
        $user = new User(array('username' => params['username']));
        
        $errors = $user->errors();
        if (strcmp($params['password'], $params['password_again']) !== 0) {
            $errors[] = 'Salasanat eivät tästmää!';
        } else if (strlen($params['passwprd'] < 5)) {
            $errors[] = 'Salasanan pitää olla vähintään 5 merkkiä pitkä!';
        }
        
        if(count($errors) != 0) {
            $user->save($params['password']);
            Redirect::to('/register', array('errors' => $errors, 'username' => params['username']));
        } else {
            Redirect::to('/login', array('messages' => 'Rekisteröityminen onnistui! Nyt voit kirjautua sisään alla:'));
        }
    }
}
