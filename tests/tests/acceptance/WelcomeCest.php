<?php

use Codeception\Extension\WiremockConnection;
use Codeception\Util\Debug;
use WireMock\Client\WireMock;

class WelcomeCest
{
    /**
     * @var \WireMock\Client\WireMock
     */
    //private $wiremock;

    public function _before(\AcceptanceTester $I)
    {
        // $this->wiremock = WiremockConnection::get();

    }

    public function _after(\AcceptanceTester $I)
    {
        $I->cleanAllPreviousRequests();
    }

    // tests
    public function tryToTest(\AcceptanceTester $I)
    {
        $I->expectRequest(
            WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Hello world!'))
        );

        $response = file_get_contents('http://localhost:18080/some/url');

        $I->receivedRequest(
            WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
        );
    }
}
