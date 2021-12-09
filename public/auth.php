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

function language(): array
{
    $lang = $_ENV['LANG'] ?? 'en';
    if (is_null($file = file_get_contents(__DIR__ . "/../language_${lang}.json"))) throw new Exception('Language pack not found.');
    return json_decode($file, true);
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $loader = new FilesystemLoader([__DIR__ . '/../templates', __DIR__ . '/../templates/auth']);
        $twig = new Environment($loader);
        echo $twig->render('auth.twig', ['lang' => language()]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), TRUE);
        $ap = filter_var($data['ap'], FILTER_VALIDATE_MAC, FILTER_NULL_ON_FAILURE);
        $mac = filter_var($data['mac'], FILTER_VALIDATE_MAC, FILTER_NULL_ON_FAILURE);
        $code = (int)filter_var(preg_replace('/\D/', '', $data['code']), FILTER_SANITIZE_NUMBER_INT);
        $now = new DateTime();
        $guestportal = new GuestPortal();
        try {
            if ($ap === NULL || $mac === NULL || $code === 0) throw new Exception('Invalid request', 400);
            $time = $now->diff(new DateTime('@' . filter_var($data['t'], 257, ['options' => ['default' => 0]])));
            if ($time->i > 5) throw new Exception('Login session expired. Rejoin the network', 412);
            if (($voucher = $guestportal->validateCode($code)) !== NULL) {
                if ($guestportal->useVoucher($voucher, $mac, $ap)) send(['status' => 'ok']);
                else throw new Exception('invalid voucher', 401);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $code = $e->getCode() ?? 500;
            error_log($msg);
            send(['error' => $msg], $code);
        }
        break;
}
