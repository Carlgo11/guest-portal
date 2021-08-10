<?php


namespace com\carlgo11\guestportal;


use DateTime;
use Exception;

class GuestPortal
{
    public function __construct()
    {
        require_once __DIR__ . '/../../../../vendor/autoload.php';
    }

    public function validateCode(string|int $code): ?Voucher
    {
        require_once __DIR__ . '/Database.php';
        $code = (int)filter_var(preg_replace('/\D/', '', $code), FILTER_SANITIZE_NUMBER_INT);
        $db = new Database();
        if (strlen($code) !== 10) throw new Exception('Invalid code format');
        return $db->fetchVoucher($code);
    }

    public function useVoucher(Voucher $voucher, string $mac, string $ap = NULL): bool
    {
        require_once __DIR__ . '/UniFi.php';
        $uniFi = new UniFi();
        if (!$uniFi->isOnline($mac)) throw new Exception('Client not connected to guest wifi');
            if ($uniFi->authorizeGuest($mac, $voucher, $ap)) {
                $db = new Database();
                if ($uses = $voucher->uses < 2) $db->removeVoucher($voucher);
                else $db->updateUses($voucher, $uses - 1);
                return true;
            }
        return false;
    }

    /**
     * @throws Exception
     */
    public function createVoucher(int $uses, DateTime $expiry, int $duration): ?string
    {
        require_once __DIR__ . '/Database.php';
        require_once __DIR__ . '/Voucher.php';
        $voucher = new Voucher(null, $duration, $uses, $expiry);
        $db = new Database();
        if ($db->uploadVoucher($voucher))
            return $voucher->id;
        return null;
    }

}