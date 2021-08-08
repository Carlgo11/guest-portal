<?php

namespace com\carlgo11\guestportal;

use UniFi_API\Client as UniFi_API;

require_once __DIR__ . '/../../../../vendor/autoload.php';

class UniFi
{

    private UniFi_API $unifi_connection;

    public function __construct()
    {
        $this->unifi_connection = new UniFi_API($_ENV['UNIFI_USER'], $_ENV['UNIFI_PASSWORD'], $_ENV['UNIFI_URL'], $_ENV['UNIFI_SITE'], $_ENV['UNIFI_VERSION'], $_ENV['UNIFI_VERIFY_CERT']);
        return $this->unifi_connection->login();
    }

    public function authorizeGuest(string $MACAddress, Voucher $voucher, string $ap = NULL): bool
    {
        return $this->unifi_connection->authorize_guest($MACAddress, $voucher->duration, $voucher->speed_limit, $voucher->speed_limit, $ap);
    }

    public function listClients(): array
    {
        $clients = [];
        foreach ($this->unifi_connection->list_clients() as $client) {
            $client = get_object_vars($client);
            if ($client['is_guest']) $clients[] = $client;
        }
        return $clients;
    }

}