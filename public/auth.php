<?php

use JetBrains\PhpStorm\NoReturn;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Carlgo11\Guest_Portal\GuestPortal;

require_once __DIR__ . '/../vendor/autoload.php';

#[NoReturn] function send($message, $code = 200)
{
    http_response_code($code);
    die(json_encode($message));
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $view = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING);
        $loader = new FilesystemLoader([__DIR__ . '/../templates', __DIR__ . '/../templates/auth']);
        $twig = new Environment($loader, ['cache' => '/tmp/.compilation_cache', 'debug' => true]);
        $twig->addExtension(new DebugExtension());
        $template = $twig->load('auth.twig');
        echo $template->render(['view' => $view]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), TRUE);
        $ap = filter_var($data['ap'], FILTER_VALIDATE_MAC, FILTER_NULL_ON_FAILURE);
        $mac = filter_var($data['mac'], FILTER_VALIDATE_MAC, FILTER_NULL_ON_FAILURE);
        $code = (int)filter_var(preg_replace('/\D/', '', $data['code']), FILTER_SANITIZE_NUMBER_INT);
        $now = new DateTime();
        $guestportal = new GuestPortal();
        try {
            if ($ap === NULL || $mac === NULL || $code === NULL) throw new Exception('Invalid request', 400);
            $time = $now->diff(new DateTime('@' . filter_var($data['t'], 257, ['options' => ['default' => 0]])));
            if ($time->i > 5) throw new Exception('Login session expired. Rejoin the network', 412);
            if (($voucher = $guestportal->validateCode($code)) !== NULL) {
                if ($guestportal->useVoucher($voucher, $mac, $ap)) send(['status' => 'ok']);
                else throw new Exception('invalid voucher', 401);
            }
        } catch (Exception $e) {
            send(['error' => $e->getMessage()], $e->getCode() ?? 500);
        }
        break;
}
