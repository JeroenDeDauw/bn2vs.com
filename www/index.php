<?php

ini_set('display_errors', 0);

$hostParts = explode( '.', $_SERVER['HTTP_HOST'] );
if ( array_shift( $hostParts ) === 'blog' ) {
	header( 'Location: http://www.bn2vs.com/blog' . $_SERVER['REQUEST_URI'] );
	die();
}

require_once __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/controllers.php';
$app->run();
