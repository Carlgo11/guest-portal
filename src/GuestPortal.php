<?php

namespace Carlgo11\Guest_Portal;

use Carlgo11\Guest_Portal\Storage\Storage;
use DateTime;
use Exception;

class GuestPortal
{
    private string $site;

    public function __construct(string $site = 'default')
    {
        $this->site = $site;
        require_once __DIR__ . '/../vendor/autoload.php';
    }

    public function validateCode(string|int $code): ?Voucher
    {
        require_once __DIR__ . '/Storage/MariaDB.php';
        $code = (int)filter_var(preg_replace('/\D/', '', $code), FILTER_SANITIZE_NUMBER_INT);
        $db = new Storage();
        if (strlen($code) !== 10) throw new Exception('Invalid code format', 400);
        return $db->fetchVoucher($code);
    }

    public function useVoucher(Voucher $voucher, string $mac, string $ap = NULL): bool
    {
        require_once __DIR__ . '/UniFi.php';
        $uniFi = new UniFi($this->site);
        if (!$uniFi->isOnline($mac)) throw new Exception('Client not connected to WLAN', 412);
        if ($uniFi->authorizeGuest($mac, $voucher, $ap)) {
            $db = new Storage();
            if ($uses = $voucher->uses < 2) $db->removeVoucher($voucher);
            else $db->updateUses($voucher, $uses - 1);
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function createVoucher(int $uses, DateTime $expiry, DateTime $duration): ?string
    {
        require_once __DIR__ . '/Storage/MariaDB.php';
        require_once __DIR__ . '/Voucher.php';
        $voucher = new Voucher(null, $duration, $uses, $expiry);
        $db = new Storage();
        if ($db->uploadVoucher($voucher)) return $voucher->id;
        return null;
    }
}
