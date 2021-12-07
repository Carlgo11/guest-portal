<?php

namespace Carlgo11\Guest_Portal\Storage;

use Carlgo11\Guest_Portal\Voucher;
use Exception;

interface iStorage
{

    /**
     * @throws Exception Throws exception if unable to connect to storage
     */
    public function __construct();

    public function __destruct();

    public function fetchVoucher(int $code): Voucher;

    /**
     * @param Voucher $voucher Voucher to upload to storage.
     * @return bool Returns TRUE if successful, otherwise FALSE.
     */
    public function uploadVoucher(Voucher $voucher): bool;

    /**
     * @param Voucher $voucher Voucher to delete from storage.
     * @return bool Returns TRUE if successful, otherwise FALSE.
     */
    public function removeVoucher(Voucher $voucher):bool;

    /**
     * @param Voucher $voucher Voucher to update.
     * @param int $newUses New amount of uses left on the voucher.
     * @return bool Returns TRUE if successful, otherwise FALSE.
     */
    public function updateUses(Voucher $voucher, int $newUses): bool;
}