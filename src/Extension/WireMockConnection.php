<?php
namespace Codeception\Extension;

use WireMock\Client\WireMock as WireMockClient;

class WireMockConnection
{
    /**
     * @var \WireMock\Client\WireMock
     */
    static private $connection;


    /**
     * @param \WireMock\Client\WireMock $connection
     */
    public static function setConnection(WireMockClient $connection)
    {
        self::$connection = $connection;
    }

    /**
     * @return \WireMock\Client\WireMock
     */
    public static function get()
    {
        return self::$connection;
    }
}
