<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['db']['host']   = "localhost";
$config['db']['user']   = "root";
$config['db']['pass']   = "root";
$config['db']['dbname'] = "slim_test";


$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer();

//$container['view'] = new \Slim\Views\PhpRenderer("../templates/");

//$container['logger'] = function($c) {
//	$logger = new \Monolog\Logger('my_logger');
//	$file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
//	$logger->pushHandler($file_handler);
//	return $logger;
//};

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