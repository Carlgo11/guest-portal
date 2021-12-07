<?php

namespace Carlgo11\Guest_Portal;

use DateTime;
use Exception;

class GuestPortal
{
    public function __construct()
    {
        require_once __DIR__ . '/../vendor/autoload.php';
    }

    public function validateCode(string|int $code): ?Voucher
    {
        require_once __DIR__ . '/MariaDB.php';
        $code = (int)filter_var(preg_replace('/\D/', '', $code), FILTER_SANITIZE_NUMBER_INT);
        $db = new MariaDB();
        if (strlen($code) !== 10) throw new Exception('Invalid code format', 400);
        return $db->fetchVoucher($code);
    }

    public function useVoucher(Voucher $voucher, string $mac, string $ap = NULL): bool
    {
        require_once __DIR__ . '/UniFi.php';
        $uniFi = new UniFi();
        if (!$uniFi->isOnline($mac)) throw new Exception('Client not connected to guest wifi', 412);
        if ($uniFi->authorizeGuest($mac, $voucher, $ap)) {
            $db = new MariaDB();
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
        require_once __DIR__ . '/MariaDB.php';
        require_once __DIR__ . '/Voucher.php';
        $voucher = new Voucher(null, $duration, $uses, $expiry);
        $db = new MariaDB();
        if ($db->uploadVoucher($voucher)) return $voucher->id;
        return null;
    }

}