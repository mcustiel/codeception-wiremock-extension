<?php
namespace Codeception\Extension;

class WireMockConnection
{
    /**
     * @var \WireMock\Client\WireMock
     */
    static private $connection;


    public static function setConnection($connection)
    {
        self::$connection = $connection;
    }

    public static function get()
    {
        return self::$connection;
    }
}
