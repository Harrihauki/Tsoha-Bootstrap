<?php

function check_logged_in() {
    BaseController::check_logged_in();
}

$routes->get('/', function() {
    HelloWorldController::index();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

$routes->get('/suunnitelmat/team', function() {
    HelloWorldController::team_list();
});

$routes->get('/suunnitelmat/esittely', function() {
    HelloWorldController::esittely();
});

$routes->get('/suunnitelmat/ottelu', function() {
    HelloWorldController::ottelu();
});

$routes->get('/suunnitelmat/team_add', function() {
    HelloWorldController::team_add();
});

$routes->get('/suunnitelmat/match_add', function() {
    HelloWorldController::match_add();
});

$routes->get('/suunnitelmat/team_edit', function() {
    HelloWorldController::team_edit();
});

$routes->get('/suunnitelmat/match_edit', function() {
    HelloWorldController::match_edit();
});

$routes->get('/match/new_match', 'check_logged_in', function() {
    MatchController::create();
});

$routes->get('/match/:id', function($id) {
    MatchController::show($id);
});

$routes->post('/match', 'check_logged_in', function() {
    MatchController::store();
});

$routes->get('/match/:id/edit', 'check_logged_in', function($id) {
    MatchController::edit($id);
});

$routes->post('/match/:id/edit', function($id) {
    MatchController::update($id);
});

$routes->post('/match/:id/destroy', 'check_logged_in', function($id) {
    MatchController::destroy($id);
});

$routes->get('/teams', function() {
    TeamsController::index();
});

$routes->get('/teams/new', 'check_logged_in', function() {
    TeamsController::create();
});

$routes->post('/team', 'check_logged_in', function() {
    TeamsController::store();
});

$routes->get('/team/:id', function($id) {
    TeamsController::show($id);
});

$routes->get('/teams/:id/edit', 'check_logged_in', function($id) {
    TeamsController::edit($id);
});

$routes->post('/teams/:id/edit', function($id) {
    TeamsController::update($id);
});

$routes->post('/team/:id/destroy', 'check_logged_in', function($id) {
    TeamsController::destroy($id);
});

$routes->get('/login', function() {
    UserController::login();
});

$routes->post('/login', function() {
    UserController::handle_login();
});

$routes->post('/logout', function() {
    UserController::logout();
});

$routes->get('/register', function() {
    UserController::register();
});

$routes->post('/register', function() {
    UserController::handle_register();
});