<?php
namespace Codeception\Extension;

class WireMockServer
{
    const LOG_FILE_NAME = 'wiremock.out';

    /**
     * @var resource
     */
    private $process;
    /**
     * @var resource[]
     */
    private $pipes;

    public function start($jarPath, $logsPath, $arguments)
    {
        if ($this->process !== null) {
            throw new \Exception('The server is already running');
        }
        $logFile = rtrim($logsPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . self::LOG_FILE_NAME;
        $descriptors = [
            ['pipe', 'r'],
            ['file', $logFile, 'w'],
            ['file', $logFile, 'a'],
        ];
        $this->process = proc_open(
            $this->getCommandPrefix() . "java -jar {$jarPath}{$arguments}",
            $descriptors,
            $this->pipes
        );
        $this->checkProcessIsRunning();
    }

    private function checkProcessIsRunning()
    {
        if (!is_resource($this->process)) {
            throw new \Exception('Could not start local wiremock server');
        }
    }

    public function stop()
    {
        if ($this->process !== null) {
            foreach ($this->pipes AS $pipe) {
                if (is_resource($pipe)) {
                    fclose($pipe);
                }
            }
            proc_terminate($this->process, SIGINT);
        }
    }

    private function getCommandPrefix()
    {
        if (PHP_OS == 'WIN32' || PHP_OS == 'WINNT' || PHP_OS == 'Windows') {
            return 'exec ';
        }
        return '';
    }
}
