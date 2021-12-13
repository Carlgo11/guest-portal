<?php

namespace Carlgo11\Guest_Portal\Storage;

use Carlgo11\Guest_Portal\Voucher;
use Exception;

class Redis implements iStorage
{


    /**
     * Initiate database connection.
     *
     * @throws Exception Throws exception if unable to connect to database.
     */
    public function __construct()
    {
    }

    /**
     * Close database connection.
     */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    /**
     * Fetch voucher data from storage.
     *
     * @param int $code voucher code.
     * @return Voucher Voucher data as {@link Voucher} class
     */
    public function fetchVoucher(int $code): Voucher
    {
        // TODO: Implement fetchVoucher() method.
    }

    /**
     * Upload a {@link Voucher} to storage.
     *
     * @param Voucher $voucher Voucher to upload to storage.
     * @return bool Returns {@link TRUE} if successful, otherwise {@link FALSE}.
     */
    public function uploadVoucher(Voucher $voucher): bool
    {
        // TODO: Implement uploadVoucher() method.
    }

    /**
     * Remove a {@link Voucher} from storage.
     * Should be called after a voucher is used or expired.
     *
     * @param Voucher $voucher Voucher to delete from storage.
     * @return bool Returns {@link TRUE} if successful, otherwise {@link FALSE}.
     */
    public function removeVoucher(Voucher $voucher): bool
    {
        // TODO: Implement removeVoucher() method.
    }

    /**
     * @param Voucher $voucher Voucher to update.
     * @param int $newUses New amount of uses left on the voucher.
     * @return bool Returns {@link TRUE} if successful, otherwise {@link FALSE}.
     */
    public function updateUses(Voucher $voucher, int $newUses): bool
    {
        // TODO: Implement updateUses() method.
    }

    /**
     * Get password hash for a user.
     *
     * @param string $username
     * @return string|null Returns password hash as {@link PASSWORD_BCRYPT} if user is found, otherwise {@link NULL}.
     */
    public function getPassword(string $username): ?string
    {
        // TODO: Implement getPassword() method.
    }

    /**
     * Create a new user.
     *
     * @param string $username name of the user. Between 1-16 chars.
     * @param string $hash {@link PASSWORD_BCRYPT} hash of the user's password.
     * @return bool Returns {@link TRUE} if successful, otherwise {@link FALSE}.
     */
    public function createUser(string $username, string $hash): bool
    {
        // TODO: Implement createUser() method.
    }

    /**
     * Get amount of users as an integer.
     *
     * @return int Returns positive integer representing the amount of stored users.
     */
    public function userAmount(): int
    {
        // TODO: Implement userAmount() method.
    }
}