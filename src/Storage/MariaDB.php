<?php

namespace Carlgo11\Guest_Portal\Storage;

use Carlgo11\Guest_Portal\Voucher;
use DateTime;
use Exception;
use mysqli;

class MariaDB implements iStorage
{

    private mysqli $mysql;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (!function_exists('mysqli_connect')) throw new Exception("MySQLi not enabled on the server", 501);
        $this->mysql = mysqli_init();
        if ($this->mysql->real_connect($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DATABASE'], $_ENV['MYSQL_PORT']) === FALSE)
            throw new Exception("Could not connect to database.");
    }

    public function __destruct()
    {
        $this->mysql->close();
    }

    public function fetchVoucher(int $code): Voucher
    {
        $query = $this->mysql->prepare('SELECT `duration`, `uses`, `expiry`, `speed_limit`  FROM `vouchers` WHERE `id` = ?');
        $query->bind_param('s', $code);
        $query->execute();
        $fetch = $query->get_result();
        $result = $fetch->fetch_assoc();
        if ($result == NULL || sizeof($result) !== 4) throw new Exception('Code not found', 404);
        require_once __DIR__ . '/Voucher.php';
        $expiry = new DateTime();
        $expiry->setTimestamp($result['expiry']);
        $duration = new DateTime();
        $duration->setTimestamp($result['duration']);
        return new Voucher($code, $duration, $result['uses'], $expiry, $result['speed_limit']);
    }

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

    public function removeVoucher(Voucher $voucher): bool
    {
        $code = $voucher->id;
        $query = $this->mysql->prepare('DELETE FROM `vouchers` WHERE `id` = ?');
        $query->bind_param('s', $code);
        $result = $query->execute();
        $query->close();
        return $result;
    }

    public function updateUses(Voucher $voucher, int $newUses): bool
    {
        $code = $voucher->id;
        $query = $this->mysql->prepare('UPDATE `vouchers` SET `uses` = ? WHERE `id` = ?');
        $query->bind_param('is', $newUses, $code);
        $result = $query->execute();
        $query->close();
        return $result;
    }
}