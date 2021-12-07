<?php

namespace Carlgo11\Guest_Portal\Storage;

use Carlgo11\Guest_Portal\Voucher;
use Exception;

class Redis implements iStorage
{


    /**
     * @throws Exception Throws exception if unable to connect to storage
     */
    public function __construct()
    {
        $reddis = new \Redis();
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function fetchVoucher(int $code): Voucher
    {
        // TODO: Implement fetchVoucher() method.
    }

    /**
     * @param Voucher $voucher Voucher to upload to storage.
     * @return bool Returns TRUE if successful, otherwise FALSE.
     */
    public function uploadVoucher(Voucher $voucher): bool
    {
        // TODO: Implement uploadVoucher() method.
    }

    /**
     * @param Voucher $voucher Voucher to delete from storage.
     * @return bool Returns TRUE if successful, otherwise FALSE.
     */
    public function removeVoucher(Voucher $voucher): bool
    {
        // TODO: Implement removeVoucher() method.
    }

    /**
     * @param Voucher $voucher Voucher to update.
     * @param int $newUses New amount of uses left on the voucher.
     * @return bool Returns TRUE if successful, otherwise FALSE.
     */
    public function updateUses(Voucher $voucher, int $newUses): bool
    {
        // TODO: Implement updateUses() method.
    }
}