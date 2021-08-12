<?php


namespace Carlgo11\Guest_Portal;

use DateTime;
use Exception;
use mysqli;

class Database
{

    private mysqli $mysql;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (!function_exists('mysqli_connect')) throw new Exception("MySQLi not enabled on the server");
        $this->mysql = mysqli_init();
        $this->mysql->real_connect($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DATABASE'], $_ENV['MYSQL_PORT']);
    }

    public function __destruct()
    {
        $this->mysql->close();
    }

    public function fetchVoucher(int $code): ?Voucher
    {
        $query = $this->mysql->prepare('SELECT `duration`, `uses`, `expiry`, `speed_limit`  FROM `vouchers` WHERE `id` = ?');
        $query->bind_param('s', $code);
        $query->execute();
        $fetch = $query->get_result();
        $result = $fetch->fetch_assoc();
        if ($result == NULL || sizeof($result) !== 4) throw new Exception('Code not found');
        try {
            require_once __DIR__ . '/Voucher.php';
            $date = new DateTime();
            $date->setTimestamp($result['expiry']);
            return new Voucher($code, $result['duration'], $result['uses'], $date, $result['speed_limit']);
        } catch (Exception $e) {
            error_log($e);
            return NULL;
        }
    }

    public function uploadVoucher(Voucher $voucher): bool
    {
        $id = $voucher->id;
        $uses = $voucher->uses;
        /** @var DateTime $expiry */
        $expiry = $voucher->expiry;
        $expiry = $expiry->getTimestamp();
        $duration = (int)$voucher->duration;
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

    public function updateUses(Voucher $voucher, $newUses)
    {

    }
}