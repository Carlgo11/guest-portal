<?php

namespace Carlgo11\Guest_Portal\Storage;

use Carlgo11\Guest_Portal\Voucher;
use DateTime;
use Exception;
use mysqli;

class MariaDB implements iStorage
{
    private int $users = 0;
    private mysqli $mysql;

    /**
     * Initiate database connection.
     *
     * @throws Exception Throws exception if unable to connect to database.
     */
    public function __construct()
    {
        if (!function_exists('mysqli_connect')) throw new Exception("MySQLi not enabled on the server", 501);
        $this->mysql = mysqli_init();
        if ($this->mysql->real_connect($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DATABASE'], $_ENV['MYSQL_PORT']) === FALSE)
            throw new Exception("Could not connect to database.");
    }

    /**
     * Close database connection.
     */
    public function __destruct()
    {
        $this->mysql->close();
    }

    /**
     * Fetch voucher data from storage.
     *
     * @param int $code voucher code.
     * @return Voucher Voucher data as {@link Voucher} class
     * @throws Exception
     */
    public function fetchVoucher(int $code): Voucher
    {
        $query = $this->mysql->prepare('SELECT `duration`, `uses`, `expiry`, `speed_limit`  FROM `vouchers` WHERE `id` = ?');
        $query->bind_param('s', $code);
        $query->execute();
        $fetch = $query->get_result();
        $result = $fetch->fetch_assoc();
        if ($result == NULL || sizeof($result) !== 4) throw new Exception('Code not found', 404);
        require_once __DIR__ . '/../Voucher.php';
        $expiry = new DateTime();
        $expiry->setTimestamp($result['expiry']);
        $duration = new DateTime();
        $duration->setTimestamp($result['duration']);
        return new Voucher($code, $duration, $result['uses'], $expiry, $result['speed_limit']);
    }

    /**
     * Upload a {@link Voucher} to storage.
     *
     * @param Voucher $voucher Voucher to upload to storage.
     * @return bool Returns {@link TRUE} if successful, otherwise {@link FALSE}.
     */
    public function uploadVoucher(Voucher $voucher): bool
    {
        $id = $voucher->id;
        $uses = $voucher->uses;
        $expiry = ($voucher->expiry)->getTimestamp();
        $duration = ($voucher->duration)->getTimestamp();
        $speed_limit = $voucher->speed_limit;
        $query = $this->mysql->prepare('INSERT INTO `vouchers` (`id`, `uses`, `expiry`, `duration`, `speed_limit`) VALUES (?, ?, ?, ?, ?)');
        $query->bind_param('siiii', $id, $uses, $expiry, $duration, $speed_limit);
        $result = $query->execute();
        $query->close();
        return $result;
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
        $code = $voucher->id;
        $query = $this->mysql->prepare('DELETE FROM `vouchers` WHERE `id` = ?');
        $query->bind_param('s', $code);
        $result = $query->execute();
        $query->close();
        return $result;
    }

    /**
     * @param Voucher $voucher Voucher to update.
     * @param int $newUses New amount of uses left on the voucher.
     * @return bool Returns {@link TRUE} if successful, otherwise {@link FALSE}.
     */
    public function updateUses(Voucher $voucher, int $newUses): bool
    {
        $code = $voucher->id;
        $query = $this->mysql->prepare('UPDATE `vouchers` SET `uses` = ? WHERE `id` = ?');
        $query->bind_param('is', $newUses, $code);
        $result = $query->execute();
        $query->close();
        return $result;
    }

    /**
     * Get password hash for a user.
     *
     * @param string $username
     * @return string|null Returns password hash as {@link PASSWORD_BCRYPT} if user is found, otherwise {@link NULL}.
     */
    public function getPassword(string $username): ?string
    {
        $query = $this->mysql->prepare('SELECT `password` FROM `users` WHERE `username` = ?');
        $query->bind_param('s', $username);
        $query->execute();
        $fetch = $query->get_result();
        $result = $fetch->fetch_assoc();
        if (is_null($result)) return null;
        return $result['password'];
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
        $query = $this->mysql->prepare('INSERT INTO `users` (`username`, `password`) VALUES (?,?)');
        $query->bind_param('ss', $username, $hash);
        $result = $query->execute();
        $query->close();
        return $result;
    }

    /**
     * Get amount of users as an integer.
     *
     * @return int Returns positive integer representing the amount of stored users.
     */
    public function userAmount(): int
    {
        if ($this->users) return $this->users;
        $query = $this->mysql->prepare('SELECT COUNT(*) FROM `users`');
        $query->execute();
        $fetch = $query->get_result();
        $result = $fetch->fetch_assoc();
        $this->users = $result['COUNT(*)'];
        $query->close();
        return $result['COUNT(*)'];
    }
}