<?php


namespace com\carlgo11\guestportal;


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
            return new Voucher($code, $result['duration'], $result['uses'], new \DateTime($result['expiry']), $result['speed_limit']);
        } catch (Exception $e) {
            error_log($e);
            return NULL;
        }
    }

    public function listVouchers(int $code): array
    {
        $query = $this->mysql->prepare('SELECT `duration`, `uses`, `expiry`, `speed_limit`  FROM `vouchers`');
        $query->execute();
        $fetch = $query->get_result();
        $vouchers = [];
        require_once __DIR__ . '/Voucher.php';
        foreach ($fetch->fetch_assoc() as $result) {
            try {
                $vouchers[] = new Voucher($code, $result['duration'], $result['uses'], new \DateTime($result['expiry']), $result['speed_limit']);
            } catch (Exception $e) {
                error_log($e);
            }
        }
        return $vouchers;
    }


    public function uploadVoucher(Voucher $voucher): bool
    {
        $id = (int)$voucher->id;
        $uses = (int)$voucher->uses;
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

    public function removeVoucher(Voucher $voucher)
    {

    }

    public function updateUses(Voucher $voucher, $newUses)
    {

    }
}