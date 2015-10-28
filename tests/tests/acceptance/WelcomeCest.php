<?php

use Codeception\Extension\WiremockConnection;
use Codeception\Util\Debug;

class WelcomeCest
{
    /**
     * @var \WireMock\Client\WireMock
     */
    private $wiremock;

    public function _before(\AcceptanceTester $I)
    {
        $this->wiremock = WiremockConnection::get();
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(\AcceptanceTester $I)
    {
        Debug::debug(var_export($this->wiremock, true));

    }
}
