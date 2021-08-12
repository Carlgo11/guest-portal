<?php

use JetBrains\PhpStorm\NoReturn;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Carlgo11\Guest_Portal\GuestPortal;

require_once __DIR__ . '/../vendor/autoload.php';

#[NoReturn] function send($message, $code = 200)
{
    http_response_code($code);
    die(json_encode($message));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), TRUE);
    $ap = filter_var($data['ap'], FILTER_VALIDATE_MAC, FILTER_NULL_ON_FAILURE);
    $mac = filter_var($data['mac'], FILTER_VALIDATE_MAC, FILTER_NULL_ON_FAILURE);
    $code = (int)filter_var(preg_replace('/\D/', '', $data['code']), FILTER_SANITIZE_NUMBER_INT);

    if ($ap === NULL || $mac === NULL || $code === NULL) send(['error' => 'missing variables'], 400);

    $guestportal = new GuestPortal();
    try {
        if (($voucher = $guestportal->validateCode($code)) !== NULL) {
            if ($guestportal->useVoucher($voucher, $mac, $ap)) send(['status' => 'ok']);
            else send(['error' => 'invalid voucher'], 400);
        }
    } catch (Exception $e) {
        send(['error' => $e->getMessage()], 500);
    }
}

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, ['cache' => '/tmp/.compilation_cache']);
$template = $twig->load('auth.twig');
echo $template->render();