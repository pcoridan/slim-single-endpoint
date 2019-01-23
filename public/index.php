<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Slim\Middleware\TokenAuthentication;

require __DIR__ . '/../vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['db']['host']   = "localhost";
$config['db']['user']   = "root";
$config['db']['pass']   = "root";
$config['db']['dbname'] = "slim_test";


$app = new App(["settings" => $config]);


$authenticator = function($request, TokenAuthentication $tokenAuth){

	$token = $tokenAuth->findToken($request);
	$auth = new Auth();
	$auth->getUserByToken($token);

};

$app->add(new TokenAuthentication([
	'path' =>   '/score',
	'authenticator' => $authenticator
]));

$container = $app->getContainer();

$container['db'] = function ($c) {
	$db = $c['settings']['db'];
	$pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
		$db['user'], $db['pass']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $pdo;
};



$app->post('/score/new', function (Request $request, Response $response) {

	$data = $request->getParsedBody();
	$new_score = [];
	$new_score['first_name'] = filter_var($data['first_name'], FILTER_SANITIZE_STRING);
	$new_score['last_name'] = filter_var($data['last_name'], FILTER_SANITIZE_STRING);
	$new_score['score'] = filter_var($data['score'], FILTER_SANITIZE_NUMBER_INT);

	$score = new ScoreEntity($new_score);
	$score_mapper = new ScoreMapper($this->db);
	$score_mapper->save($score);

	$response = $response->withJson($new_score);

	return $response;
});


$app->run();