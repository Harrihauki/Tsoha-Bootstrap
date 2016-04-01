<?php

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