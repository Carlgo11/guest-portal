<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use com\carlgo11\guestportal\GuestPortal;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require __DIR__ . '/../src/com/carlgo11/guest-portal/GuestPortal.php';

    $guestportal = new GuestPortal();
    if ($voucher = $guestportal->validateCode($_POST['code'])) {
        var_dump($voucher);
        $guestportal->useVoucher($voucher, $_POST['id'], $_POST['ap']);
    }
}

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
    'cache' => './.compilation_cache',
    'debug' => TRUE,
]);

$template = $twig->load('auth.twig');
echo $template->render();