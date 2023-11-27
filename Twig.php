<?php
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/vistas');
$twig = new \Twig\Environment($loader);

return $twig;