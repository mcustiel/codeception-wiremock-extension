<?php
namespace Codeception\Extension;

class Wiremock extends \Codeception\Platform\Extension
{
    const DEFAULT_LOGS_PATH = '/tmp/codeceptionWiremock/logs/';

    /**
     *
     * @var WiremockDownloader
     */
    private $downloader;
    /**
     *
     * @var WiremockServer
     */
    private $server;
    /**
     *
     * @var WiremockArguments
     */
    private $argumentsManager;

    public function __construct(
        $config,
        $options,
        WiremockDownloader $downloader = null,
        WiremockServer $server = null,
        WiremockArguments $argumentsManager = null
    ) {
        parent::__construct($config, $options);

        $this->initWiremockDownloader($downloader);
        $this->initWiremockServer($server);
        $this->initWiremockArgumentsManager($argumentsManager);

        $this->config = $this->argumentsManager->sanitize($this->config);

        if (! empty($this->config['host'])) {
            $host = $this->config['host'];
        } else {
            $this->server->start(
                $this->getJarPath(),
                $this->getLogsPath(),
                $this->mapConfigToWiremockArguments($this->config)
            );
            $host = 'localhost';
            sleep($this->config['start-delay']);
        }
        WiremockConnection::setConnection(\WireMock\Client\WireMock::create($host, $this->config['port']));
    }

    private function initWiremockServer($server)
    {
        if ($server === null) {
            $this->server = new WiremockServer();
        } else {
            $this->server = $server;
        }
    }

    private function initWiremockDownloader($downloader)
    {
        if ($downloader === null) {
            $this->downloader = new WiremockDownloader();
        } else {
            $this->downloader = $downloader;
        }
    }

    private function initWiremockArgumentsManager($argumentsManager)
    {
        if ($argumentsManager === null) {
            $this->argumentsManager = new WiremockArguments();
        } else {
            $this->argumentsManager = $argumentsManager;
        }
    }

    public function __destruct()
    {
        $this->server->stop($this->getJarPath(), $this->mapConfigToWiremockArguments($this->config));
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

    private function mapConfigToWiremockArguments($config)
    {
        return $this->argumentsManager->generateArgumentsString($config);
    }
}
