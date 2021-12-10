<?php

use Carlgo11\Guest_Portal\GuestPortal;
use Carlgo11\Guest_Portal\Storage\MariaDB;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/../vendor/autoload.php';
$db = new MariaDB();
function send($message, $code = 200)
{
    http_response_code($code);
    die(json_encode($message));
}

function language(): array
{
    $lang = $_ENV['LANG'] ?? 'en';
    return json_decode(file_get_contents(__DIR__ . "/../language_${lang}.json"), true);
}

session_start();
if (is_null($_SESSION['user'])) {
    header("Location: /auth?url=${_SERVER['REQUEST_URI']}");
    die();
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $loader = new FilesystemLoader([__DIR__ . '/../templates', __DIR__ . '/../templates/admin']);
        $twig = new Environment($loader);
        $template = $twig->load('admin.twig');
        echo $template->render();
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            switch (filter_var($data['type'])) {
                case 'voucher':
                    $gp = new GuestPortal();
                    $uses = filter_var($data['uses'], 257);
                    $expiry = new DateTime('@' . filter_var($data['expiry'], 257, FILTER_NULL_ON_FAILURE));
                    $duration = new DateTime('@' . filter_var($data['duration'], 257, FILTER_NULL_ON_FAILURE));
                    if ($voucher = $gp->createVoucher($uses, $expiry, $duration)) {
                        send(['voucher' => $voucher], 201);
                    } else throw new Exception('Unable to create voucher');
                case 'user':
                    $username = preg_replace('/\W/', '', $data['username']);
                    $hash = password_hash(filter_var($data['password'], 513), PASSWORD_BCRYPT);
                    $result = $db->createUser($username, $hash);
                    if ($result) send(null, 201);
                    throw new Exception('Unable to create user');
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $code = $e->getCode() ?? 500;
            error_log("${code}: ${msg}");
            send(['error' => $msg], $code);
        }
}
