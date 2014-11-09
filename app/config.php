<?php

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Configure the database and boot Eloquent
 */
$capsule = new Capsule;

$capsule->addConnection(array(
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'catholicapp',
    'username'  => 'rotimi',
    'password'  => 'rotimisdp',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => ''
));

$capsule->setAsGlobal();

$capsule->bootEloquent();

// set timezone for timestamps etc
date_default_timezone_set('UTC');

error_reporting(E_ALL);

ini_set('display_errors', 1);


function sendUnknownError($app, $e) {

    echoJSONResponse($app, 10, "Oppps! Something went wrong. Please try again. [" . $e->getMessage() . "]" );
}

function getRequestBodyAsArray($app) {

    try {

        $jsonString = $app->request->getBody();


    } catch(Exception $e) {

        $jsonString = null;
    }

    return json_decode($jsonString);
}

function echoJSONResponse($app, $status, $message, $data = null) {

    $json_output['meta']["status"] = $status;
    $json_output['meta']["message"] = $message;

    if($data != null) {
        $json_output['data'] = $data;
    }

    $app->response->header('Content-Type', 'application/json');
    $app->response->header('Access-Control-Allow-Origin','*');
    $app->response->header('Access-Control-Allow-Methods','GET, POST, DELETE, PUT');
    $app->response->header('Access-Control-Allow-Headers','X-Requested-With');
    $app->response->header('Access-Control-Max-Age','172800');
    echo json_encode($json_output);
}

function nowTimeStamp() {

    return date('Y-m-d h:i:s');
}