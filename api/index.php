<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
  require_once __DIR__ . "/src/$class.php";
});
header('Content-type: application/json; charset=UTF-8');
set_error_handler('ErrorHandler::handleError');
set_exception_handler('ErrorHandler::handleException');
$parts = explode('/', $_SERVER['REQUEST_URI']);
// print_r($parts);

$id = $parts[2] ?? null;

$db = new Database('localhost', 'product_db', 'sandor', 'pass');
$db->getConnection();
$productGateway = new ProductGateway($db);
$controller = new ProductController($productGateway);
$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);
