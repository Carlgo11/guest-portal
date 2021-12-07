<?php

namespace Carlgo11\Guest_Portal;

use DateTime;
use UniFi_API\Client as UniFi_API;

require_once __DIR__ . '/../vendor/autoload.php';

class UniFi
{

    private UniFi_API $unifi_connection;

    public function __construct()
    {
        $this->unifi_connection = new UniFi_API($_ENV['UNIFI_USER'], $_ENV['UNIFI_PASSWORD'], $_ENV['UNIFI_URL'], $_ENV['UNIFI_SITE'], $_ENV['UNIFI_VERSION'], $_ENV['UNIFI_VERIFY_CERT']);
        if (!($login = $this->unifi_connection->login())) throw new \Exception("Unable to access Unifi system.", 503);
        return $login;
    }

    public function __destruct()
    {
        $this->unifi_connection->logout();
    }

    public function authorizeGuest(string $MACAddress, Voucher $voucher, string $ap = NULL): bool
    {
        $now = new DateTime();
        $duration = $voucher->duration;
        if (($diff = $now->diff($duration)) === NULL) throw new \Exception("Unable to process session duration.", 500);
        $speed_limit = $voucher->speed_limit * 1024;

        return $this->unifi_connection->authorize_guest(mac: $MACAddress, minutes: $diff->i, up: $speed_limit, down: $speed_limit, megabytes: null, ap_mac: $ap);
    }

    public function isOnline($mac): bool
    {
        $resp = $this->unifi_connection->list_clients($mac);
        return ($resp !== FALSE && sizeof($resp) > 0);
    }

}