<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
    'cache' => './.compilation_cache',
    'debug' => TRUE,
]);

$template = $twig->load('auth.twig');
echo $template->render();