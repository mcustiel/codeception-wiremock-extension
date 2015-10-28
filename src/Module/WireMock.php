<?php
namespace Codeception\Module;

use Codeception\Module as CodeceptionModule;
use Codeception\TestCase;
use Codeception\Extension\WireMockConnection;
use WireMock\Client\MappingBuilder;
use WireMock\Client\RequestPatternBuilder;

class WireMock extends CodeceptionModule
{
    /**
     * @var \WireMock\Client\WireMock
     */
    private $wireMock;

    /**
     * {@inheritDoc}
     * @see \Codeception\Module::_before()
     */
    public function _before(TestCase $testCase)
    {
        parent::_before($testCase);
        $this->wireMock = WireMockConnection::get();
    }

    public function ensureThereAreNoRequests()
    {
        $this->wireMock->reset();
    }

    /**
     * @param \WireMock\Client\MappingBuilder $builder
     */
    public function expectRequest(MappingBuilder $builder)
    {
        $this->wireMock->stubFor($builder);
    }

    /**
     * @param \WireMock\Client\RequestPatternBuilder|integer $builderOrCount
     * @param \WireMock\Client\RequestPatternBuilder         $builder
     */
    public function receivedRequest($builderOrCount, RequestPatternBuilder $builder = null)
    {
        $this->wireMock->verify($builderOrCount, $builder);
    }

    /**
     * {@inheritDoc}
     * @see \Codeception\Module::_after()
     */
    public function _after(TestCase $testCase)
    {
        parent::_after($testCase);
    }
}
