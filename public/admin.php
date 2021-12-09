<?php

use Carlgo11\Guest_Portal\GuestPortal;
use Carlgo11\Guest_Portal\Storage\MariaDB;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/../vendor/autoload.php';

#[NoReturn] function send($message, $code = 200)
{
    http_response_code($code);
    die(json_encode($message));
}

function language(): array
{
    $lang = $_ENV['LANG'] ?? 'en';
    return json_decode(file_get_contents(__DIR__ . "/../language_${lang}.json"), true);
}

function authenticated($user): bool
{
    if (session_status() === PHP_SESSION_ACTIVE && $_SESSION['user'] === $user) return true;
    return false;
}

function authenticate()
{
    $user = $_SERVER['PHP_AUTH_USER'];
    if (isset($user)) {
        if (authenticated($user)) return true;
        if (($hash = (new MariaDB())->getUser($user)) !== NULL)
            if (password_verify($_SERVER['PHP_AUTH_PW'], $hash)) {
                session_start();
                $_SESSION['user'] = $user;
                return true;
            }
    }
    header('HTTP/1.0 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="guest-portal"');
    die();
}

authenticate();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $loader = new FilesystemLoader([__DIR__ . '/../templates', __DIR__ . '/../templates/admin']);
        $twig = new Environment($loader);
        $template = $twig->load('admin.twig');
        echo $template->render();
        break;
    case 'POST':
        $gp = new GuestPortal();
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $uses = filter_var($data['uses'], 257);
            $expiry = new DateTime('@' . filter_var($data['expiry'], 257, FILTER_NULL_ON_FAILURE));
            $duration = new DateTime('@' . filter_var($data['duration'], 257, FILTER_NULL_ON_FAILURE));
            if ($voucher = $gp->createVoucher($uses, $expiry, $duration)) {
                send(['voucher' => $voucher], 201);
            } else throw new Exception('Unable to create voucher');
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $code = $e->getCode() ?? 500;
            error_log("${code}: ${$msg}");
            send($msg, $code);
        }
}
