<?php

namespace Carlgo11\Guest_Portal;

use DateTime;
use Exception;

class Voucher
{
    private int $id;
    private int $uses;
    private DateTime $expiry;
    private DateTime $duration;
    private int $speed_limit;

    /**
     * @throws Exception if provided input is in an unexpected/invalid format.
     */
    public function __construct(?int $uuid, DateTime $duration, int $uses = 1, DateTime $expiry = NULL, int $speed_limit = 0)
    {
        // Validate UUID
        if ($uuid === NULL) $this->id = $this->generateUUID();
        else {
            if (strlen($uuid) === 10) $this->id = $uuid;
            else throw new Exception("UUID input invalid");
        }

        // Validate uses
        if ($uses >= 0 && $uses < 255) $this->uses = $uses;
        else throw new Exception("Uses input invalid");

        // Validate (voucher) expiry date
        if ($expiry !== NULL) {
            if ($expiry > new DateTime()) $this->expiry = $expiry;
            else throw new Exception("Voucher expiry date is in the past");
        } else $this->expiry = new DateTime('+1 day');

        // Validate (session) duration
        if ($duration > new DateTime()) $this->duration = $duration;
        else throw new Exception("Session expiry date is in the past");

        // Validate (download|upload) speed limit
        if ($speed_limit >= 0) $this->speed_limit = $speed_limit;
        else throw new Exception("Speed limit must be greater than 0");
    }

    /**
     * @throws Exception
     */
    public function __get(string $name)
    {
        if (isset($this->{$name})) return $this->{$name};
        return null;
    }

    /**
     * @throws Exception
     */
    private function generateUUID(): int
    {
        return (int)str_pad(random_int(min: 0, max: (int)9999999999), length: 10, pad_string: '0');
    }

    public function __toString()
    {
        return (string)$this->id;
    }
}