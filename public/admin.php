<?php

use com\carlgo11\guestportal\GuestPortal;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/../vendor/autoload.php';

if (isset($_POST['uses'])) {
    $uses = filter_input(INPUT_POST, 'uses', FILTER_SANITIZE_NUMBER_INT);
    $expiry = filter_input(INPUT_POST, 'expiry');
    $duration = filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_NUMBER_INT);
    require_once __DIR__ . '/../src/com/carlgo11/guest-portal/GuestPortal.php';
    $gp = new GuestPortal();
} else {
    $loader = new FilesystemLoader(__DIR__ . '/../templates');
    $twig = new Environment($loader, [
        'cache' => './.compilation_cache',
        'debug' => TRUE,
    ]);

    $template = $twig->load('admin.twig');
    echo $template->render();
}