<?php
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');

$unifi_connection = new UniFi_API\Client($_ENV['hotspot_user'], $_ENV['hotspot_password'], $_ENV['unifi_url'], $_ENV['unifi_site'], $_ENV['unifi_version'], FALSE);
$login = $unifi_connection->login();
$clients = [];
foreach ($unifi_connection->list_clients() as $client) {
    $client = get_object_vars($client);
    if( $client['is_guest'] ) $clients[] = $client;
}
print(json_encode($clients));
