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
namespace Codeception\Extension;

use Codeception\Platform\Extension as CodeceptionExtension;
use WireMock\Client\WireMock as WireMockClient;

/**
 * Codeception Extension for WireMock
 */
class WireMock extends CodeceptionExtension
{
    /**
     *
     * @var WireMockDownloader
     */
    private $downloader;
    /**
     *
     * @var WireMockProcess
     */
    private $process;
    /**
     *
     * @var WireMockArguments
     */
    private $argumentsManager;

    /**
     * Class constructor.
     *
     * @param array              $config
     * @param array              $options
     * @param WireMockDownloader $downloader       optional WireMockDownloader object
     * @param WireMockProcess    $process          optional WireMockProcess object
     * @param WireMockArguments  $argumentsManager optional WireMockArguments object
     */
    public function __construct(
        array $config,
        array $options,
        WireMockDownloader $downloader = null,
        WireMockProcess $process = null,
        WireMockArguments $argumentsManager = null
    ) {
        parent::__construct($config, $options);

        $this->initWireMockDownloader($downloader);
        $this->initWireMockProcess($process);
        $this->initWireMockArgumentsManager($argumentsManager);

        $this->config = $this->argumentsManager->sanitize($this->config);

        echo "Starting local wiremock" . PHP_EOL;
        $this->process->start(
            $this->getJarPath(),
            $this->config['logs-path'],
            $this->mapConfigToWireMockArguments($this->config)
        );
        sleep($this->config['start-delay']);
    }

    private function initWireMockProcess($process)
    {
        if ($process === null) {
            $this->process = new WireMockProcess();
        } else {
            $this->process = $process;
        }
    }

    private function initWireMockDownloader($downloader)
    {
        if ($downloader === null) {
            $this->downloader = new WireMockDownloader();
        } else {
            $this->downloader = $downloader;
        }
    }

    private function initWireMockArgumentsManager($argumentsManager)
    {
        if ($argumentsManager === null) {
            $this->argumentsManager = new WireMockArguments();
        } else {
            $this->argumentsManager = $argumentsManager;
        }
    }

    /**
     * Class destructor.
     */
    public function __destruct()
    {
        $connection = WireMockClient::create('localhost', $this->config['port']);
        if ($connection->isAlive()) {
            $connection->shutdownServer();
        }
        $this->process->stop();
    }

    private function getJarPath()
    {
        if (!empty($this->config['jar-path'])) {
            $this->checkJarExists($this->config['jar-path']);
            $jarPath = $this->config['jar-path'];
        } elseif (!empty($this->config['download-version'])) {
            $jarPath = $this->downloader->downloadAndGetLocalJarPath($this->config['download-version']);
        } else {
            throw new \Exception("Bad configuration");
        }
        return $jarPath;
    }

    private function checkJarExists($jar)
    {
        if (!file_exists($jar)) {
            throw \Exception("File $jar does not exist");
        }
    }

    private function mapConfigToWireMockArguments($config)
    {
        return $this->argumentsManager->generateArgumentsString($config);
    }
}
