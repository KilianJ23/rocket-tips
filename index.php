<?php

// charge l'autoload de composer
require "vendor/autoload.php";

// charge le contenu du .env dans $_ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$route = null;
if (isset($_GET['route'])) {
    $route = $_GET['route'];
}
$router = new Router();
$router->handleRequest($route);