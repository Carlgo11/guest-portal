<?php


namespace com\carlgo11\guestportal;


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
        if (strlen($code) !== 10) throw new \Exception('Invalid code format');
        var_dump($code);
        return $db->fetchVoucher($code);
    }

    public function useVoucher(Voucher $voucher, string $mac, string $ap = NULL)
    {
        require_once __DIR__ . '/UniFi.php';
        $uniFi = new UniFi();
        if (in_array($mac, $uniFi->listClients()))
            if ($uniFi->authorizeGuest($mac, $voucher)) {
                $db = new Database();
                if ($uses = $voucher->uses <= 1) $db->removeVoucher($voucher);
                else $db->updateUses($voucher, $uses - 1);
            }
    }

}