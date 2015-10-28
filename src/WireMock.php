<?php
namespace Codeception\Extension;

class WireMock extends \Codeception\Platform\Extension
{
    const DEFAULT_LOGS_PATH = '/tmp/codeceptionWireMock/logs/';

    /**
     *
     * @var WireMockDownloader
     */
    private $downloader;
    /**
     *
     * @var WireMockServer
     */
    private $server;
    /**
     *
     * @var WireMockArguments
     */
    private $argumentsManager;

    public function __construct(
        $config,
        $options,
        WireMockDownloader $downloader = null,
        WireMockServer $server = null,
        WireMockArguments $argumentsManager = null
    ) {
        parent::__construct($config, $options);

        $this->initWireMockDownloader($downloader);
        $this->initWireMockServer($server);
        $this->initWireMockArgumentsManager($argumentsManager);

        $this->config = $this->argumentsManager->sanitize($this->config);

        if (! empty($this->config['host'])) {
            $host = $this->config['host'];
        } else {
            $this->server->start(
                $this->getJarPath(),
                $this->getLogsPath(),
                $this->mapConfigToWireMockArguments($this->config)
            );
            $host = 'localhost';
            sleep($this->config['start-delay']);
        }
        WireMockConnection::setConnection(\WireMock\Client\WireMock::create($host, $this->config['port']));
    }

    private function initWireMockServer($server)
    {
        if ($server === null) {
            $this->server = new WireMockServer();
        } else {
            $this->server = $server;
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

    public function __destruct()
    {
        $this->server->stop();
    }

    private function getJarPath()
    {
        if (! empty($this->config['jar-path'])) {
            $this->checkJarExists($this->config['jar-path']);
            $jarPath = $this->config['jar-path'];
        } elseif (!empty($this->config['download-version'])) {
            $jarPath = $this->downloader->downloadAndGetLocalJarPath($this->config['download-version']);
        } else {
            throw new \Exception("Bad configuration");
        }
        return $jarPath;
    }

    private function getLogsPath()
    {
        if (! empty($this->config['logs-path'])) {
            $logsPath = $this->config['logs-path'];
            $this->checkLogsPath($logsPath);
        } else {
            $logsPath = self::DEFAULT_LOGS_PATH;
            if (! is_dir($logsPath)) {
                mkdir($logsPath, 0777, true);
            }
        }
        return $logsPath;
    }

    private function checkLogsPath($logsPath)
    {
        if (! is_dir($logsPath) || ! is_writable($logsPath)) {
            throw \Exception("Directory $logsPath does not exist");
        }
    }

    private function checkJarExists($jar)
    {
        if (! file_exists($jar)) {
            throw \Exception("File $jar does not exist");
        }
    }

    private function mapConfigToWireMockArguments($config)
    {
        return $this->argumentsManager->generateArgumentsString($config);
    }
}
