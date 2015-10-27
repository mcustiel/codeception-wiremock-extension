<?php
namespace Codeception\Extension;

class WiremockConnection
{
    /**
     * @var \WireMock\Client\WireMock
     */
    static private $connection;


    public function setConnection($connection)
    {
        self::$connection = $connection;
    }

    public function getConnection()
    {
        return self::$connection;
    }
}
