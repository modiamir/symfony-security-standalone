<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/vendor/autoload.php';

$request = Request::createFromGlobals();

$kernel = new Kernel();

/** @var \Symfony\Component\HttpFoundation\Response $response */
$response = $kernel->handle($request);

$response->send();
