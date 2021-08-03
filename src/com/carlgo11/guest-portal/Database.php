<?php


namespace com\carlgo11\guestportal;


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

    public function validateVoucher(Voucher $voucher)
    {

    }

    public function uploadVoucher(Voucher $voucher): bool
    {
        $id = $voucher->id;
        $uses = $voucher->uses;
        $exiry = $voucher->expiry;
        $duration = $voucher->duration;
        $speed_limit = $voucher->speed_limit;
        $query = $this->mysql->prepare('INSERT INTO `vouchers` (`id`, `uses`, `expiry`, `duration`, `speed_limit`) VALUES (?, ?, ?, ?, ?)');
        $query->bind_param('siiii', $id, $uses, $exiry, $duration, $speed_limit);
        $result = $query->execute();
        $query->close();
        return $result;
    }

    public function removeVoucher(Voucher $voucher)
    {

    }

    public function updateUses(Voucher $voucher, $newUses)
    {

    }
}