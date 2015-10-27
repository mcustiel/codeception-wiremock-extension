<?php
namespace Codeception\Extension;

use WireMock\Client\WireMock;
class Wiremock extends \Codeception\Platform\Extension
{
    const DEFAULT_LOGS_PATH = '/tmp/codeceptionWiremock/logs/';

    /**
     * @var WiremockDownloader
     */
    private $downloader;
    /**
     * @var WiremockServer
     */
    private $server;

    public function __construct($config, $options)
    {
        parent::__construct($config, $options);

        if (!empty($this->config['host'])) {
            $host = $this->config['host'];
        } else {
            $this->server->start(
                $this->getJarPath(),
                $this->getLogsPath(),
                $this->mapConfigToWiremockArguments($this->config)
            );
            $host = 'localhost';
        }
        (new WiremockConnection())->setConnection(WireMock::create($host, $this->config['port']));
    }

    private function getJarPath()
    {
        if (!empty($this->config['jar_path'])) {
            $this->checkJarExists($this->config['jar_path']);
            $jarPath = $this->config['jar_path'];
        } elseif (!empty($this->config['download_version'])) {
            $jarPath = $this->downloader->downloadAndGetLocalJarPath($this->config['download_version']);
        }
        return $jarPath;
    }

    private function getLogsPath()
    {
        if (!empty($this->config['logs_path'])) {
            $logsPath = $this->config['logs_path'];
            $this->checkLogsPath($logsPath);
        } else {
            $logsPath = self::DEFAULT_LOGS_PATH;
            if (!is_dir($logsPath)) {
                mkdir($logsPath, 0777, true);
            }
        }
        return $logsPath;
    }

    private function checkLogsPath($logsPath)
    {
        if (!is_dir($logsPath) || !is_writable($logsPath)) {
            throw \ Exception("Directory $logsPath does not exist");
        }
    }

    private function checkJarExists($jar)
    {
        if (!file_exists($jar)) {
            throw \Exception("File $jar does not exist");
        }
    }

    private function mapConfigToWiremockArguments($config)
    {
        return (new WiremockArgumentsMapper())->map($config);
    }
}
