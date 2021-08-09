<?php

use com\carlgo11\guestportal\GuestPortal;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $uses = filter_var($data['uses'], FILTER_SANITIZE_NUMBER_INT);
        $expiry = new DateTime(filter_var($data['expiry']));
        $duration = filter_var($data['duration'], FILTER_SANITIZE_NUMBER_INT);
        require_once __DIR__ . '/../src/com/carlgo11/guest-portal/GuestPortal.php';
        $gp = new GuestPortal();
        if ($voucher = $gp->createVoucher($uses, $expiry, $duration)) {
            http_response_code(200);
            die(json_encode(['voucher' => $voucher]));
        } else throw new Exception("Unable to create voucher");
    } catch (Exception $e) {
        $error = ['error' => $e->getMessage()];
        http_response_code(500);
        die(json_encode($error));
    }
}

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
    'cache' => './.compilation_cache',
    'debug' => TRUE,
]);
$vars = ['vouchers' => $gp->listVouchers];
$template = $twig->load('admin.twig');
echo $template->render();
