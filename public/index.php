<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$app = new \Slim\App;

$app->add(new \Slim\Middleware\Session([
  'name' => 'docsys',
  'autorefresh' => false,
  'lifetime' => '2 hour'
]));

$container = $app->getContainer();

$container['session'] = function ($c) {
  return new \SlimSession\Helper;
};

$app->add(new \Tuupola\Middleware\Cors([
    "origin" => ["*"],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS"],
    "headers.allow" => ["Authorization", "If-Match", "If-Unmodified-Since", "X-Requested-With"],
    "headers.expose" => ["Etag"],
    "credentials" => true,
    "cache" => 0
]));

$app->get('/auth', function (Request $request, Response $response) {
	$obj = new stdClass();
	//$this->session->destroy();
	if($this->session->logged_in) {
		$obj->status = "ok";
	} else {
		$obj->status = "failure";
	}
	$obj->lol = $this->session->logged_in;
	$obj->lll = $this->session->id();
	$obj->message = "Es ist niemand eingeloggt!";
	$obj->user = new stdClass();
	$obj->user->first_name = "Paul";
	$obj->user->last_name = "Pröll";

    return $response->withJson($obj);
});

$app->post('/auth', function (Request $request, Response $response) {
	$obj = new stdClass();
	$parsedBody = $request->getParsedBody();
	if($parsedBody["user"] == "proepau") {
		$this->session->logged_in = true;
		$obj->status = "ok";
	} else {
		$obj->status = "failure";
	}
	$obj->lol = $this->session->logged_in;
	$obj->lll = $this->session->id();
	$obj->message = "Zugangsdaten sind falsch!";
	$obj->user = new stdClass();
	$obj->user->first_name = "Paul";
	$obj->user->last_name = "Pröll";

    return $response->withJson($obj);
});
$app->run();
