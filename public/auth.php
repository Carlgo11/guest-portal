<?php

use Carlgo11\Guest_Portal\Storage\MariaDB;
use JetBrains\PhpStorm\NoReturn;
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
    if (is_null($file = file_get_contents(__DIR__ . "/../language_${lang}.json"))) throw new Exception('Language pack not found.');
    return json_decode($file, true);
}

#[NoReturn] function start_session(string $username)
{
    session_start();
    $_SESSION['user'] = $username;
    session_commit();
    send(null, 204);
}

$db = new MariaDB();
$first_login = !$db->userAmount();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $loader = new FilesystemLoader([__DIR__ . '/../templates', __DIR__ . '/../templates/auth']);
        $twig = new Environment($loader);
        echo $twig->render('auth.twig', ['lang' => language(), 'first_login' => $first_login]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $username = preg_replace('/\W/', '', $data['username']);
            if ($first_login) {
                $hash = password_hash(filter_var($data['password'], 513), PASSWORD_BCRYPT);
                $result = $db->createUser($username, $hash);
                if ($result) start_session($username);

                throw new Exception('Unable to create user');
            } else {
                $password = filter_var($data['password']);
                if (password_verify($password, $db->getPassword($username))) start_session($username);

                throw new Exception("Invalid username or password", 400);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $code = $e->getCode() ?? 500;
            error_log($msg);
            send(['error' => $msg], $code);
        }
}
