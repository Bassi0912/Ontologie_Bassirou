<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

use App\Core\Router;

$router = new Router();

// Routes
$router->get('/', 'HomeController@index');
$router->get('/upload', 'HomeController@upload');
$router->post('/upload', 'OntologyController@upload');
$router->get('/ontology', 'OntologyController@index');
$router->get('/api/hierarchy', 'ApiController@hierarchy');
$router->get('/api/properties', 'ApiController@properties');
$router->get('/api/concept', 'ApiController@concept');
$router->get('/api/radial', 'ApiController@radial');
$router->get('/api/progressive', 'ApiController@progressive');

$router->dispatch();
