<?php
/**
 * This file is part of codeception-wiremock-extension.
 *
 * codeception-wiremock-extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * codeception-wiremock-extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with codeception-wiremock-extension.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Codeception\Module;

use Codeception\Module as CodeceptionModule;
use Codeception\TestCase;
use Codeception\Util\Debug;
use WireMock\Client\WireMock as WireMockClient;
use WireMock\Client\MappingBuilder;
use WireMock\Client\RequestPatternBuilder;

class WireMock extends CodeceptionModule
{
    /**
     * @var \WireMock\Client\WireMock
     */
    private $wireMock;

    protected $config = [
        'host' => 'localhost',
        'port' => '8080'
    ];

    /**
     * {@inheritDoc}
     * @see \Codeception\Module::_beforeSuite()
     */
    public function _beforeSuite($settings = [])
    {
        $this->config = array_merge($this->config, $settings);
        Debug::debug(
            "Connecting to WireMock in: host {$this->config['host']} and port {$this->config['port']}"
        );
        $this->wireMock = WireMockClient::create($this->config['host'], $this->config['port']);
    }

    public function cleanAllPreviousRequestsToWireMock()
    {
        $this->wireMock->reset();
    }

    /**
     * @param \WireMock\Client\MappingBuilder $builder
     */
    public function expectRequestToWireMock(MappingBuilder $builder)
    {
        $this->wireMock->stubFor($builder);
    }

    /**
     * @param \WireMock\Client\RequestPatternBuilder|integer $builderOrCount
     * @param \WireMock\Client\RequestPatternBuilder         $builder
     */
    public function receivedRequestToWireMock($builderOrCount, RequestPatternBuilder $builder = null)
    {
        $this->wireMock->verify($builderOrCount, $builder);
    }
}
