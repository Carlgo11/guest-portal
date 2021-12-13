<?php

namespace Carlgo11\Guest_Portal\Storage;

use Carlgo11\Guest_Portal\Voucher;

class Storage
{
    private Redis|MariaDB $database;

    public function __construct()
    {
        switch (strtolower($_ENV['DATABASE'])) {
            case 'mysql':
                include_once __DIR__ . '/MariaDB.php';
                $this->database = new MariaDB();
                break;
            case 'redis':
                include_once __DIR__ . '/Redis.php';
                $this->database = new Redis();
                break;
            default:
                throw new \Exception("No database specified");
        }
    }

    public function fetchVoucher(int $code): Voucher
    {
        return $this->database->fetchVoucher($code);
    }

    /**
     * @param Voucher $voucher Voucher to upload to storage.
     * @return bool Returns TRUE if successful, otherwise FALSE.
     */
    public function uploadVoucher(Voucher $voucher): bool
    {
        return $this->database->uploadVoucher($voucher);
    }

    /**
     * @param Voucher $voucher Voucher to delete from storage.
     * @return bool Returns TRUE if successful, otherwise FALSE.
     */
    public function removeVoucher(Voucher $voucher): bool
    {
        return $this->database->removeVoucher($voucher);
    }

    /**
     * @param Voucher $voucher Voucher to update.
     * @param int $newUses New amount of uses left on the voucher.
     * @return bool Returns TRUE if successful, otherwise FALSE.
     */
    public function updateUses(Voucher $voucher, int $newUses): bool
    {
        return $this->database->updateUses($voucher, $newUses);
    }

    public function getPassword(string $username): string
    {
        return $this->database->getPassword($username);
    }

    public function createUser(string $username, string $hash): bool
    {
        return $this->database->createUser($username, $hash);
    }

    public function userAmount(): int
    {
        return $this->database->userAmount();
    }
}