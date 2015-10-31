<?php
namespace Codeception\Extension;

/**
 * Manages the current running WireMock process.
 */
class WireMockProcess
{
    /**
     * WireMock server log.
     *
     * @var string
     */
    const LOG_FILE_NAME = 'wiremock.out';

    /**
     * @var resource
     */
    private $process;
    /**
     * @var resource[]
     */
    private $pipes;

    /**
     * Starts a wiremock process.
     *
     * @param string $jarPath
     * @param string $logsPath
     * @param string $arguments
     *
     * @throws \Exception
     */
    public function start($jarPath, $logsPath, $arguments)
    {
        $this->checkIfProcessIsRunning();

        $this->process = proc_open(
            $this->getCommandPrefix() . "java -jar {$jarPath}{$arguments}",
            $this->createProcessDescriptors($logsPath),
            $this->pipes,
            null,
            null,
            ['bypass_shell' => true]
        );
        $this->checkProcessIsRunning();
    }

    /**
     * @param string $logsPath
     *
     * @return array[]
     */
    private function createProcessDescriptors($logsPath)
    {
        $logFile = $logsPath . DIRECTORY_SEPARATOR . self::LOG_FILE_NAME;
        $descriptors = [
            ['file', $logFile, 'w'],
            ['file', $logFile, 'a'],
        ];
        return $descriptors;
    }

    /**
     * @throws \Exception
     */
    private function checkIfProcessIsRunning()
    {
        if ($this->process !== null) {
            throw new \Exception('The server is already running');
        }
    }

    /**
     * @return boolean
     */
    public function isRunning()
    {
        return isset($this->process) && is_resource($this->process);
    }

    /**
     * @throws \Exception
     */
    private function checkProcessIsRunning()
    {
        if (!$this->isRunning()) {
            throw new \Exception('Could not start local wiremock server');
        }
    }

    /**
     * Stops the process.
     */
    public function stop()
    {
        if (is_resource($this->process)) {
            foreach ($this->pipes AS $pipe) {
                if (is_resource($pipe)) {
                    fflush($pipe);
                    fclose($pipe);
                }
            }
            proc_close($this->process);
            unset($this->process);
        }
    }

    /**
     * @return string
     */
    private function getCommandPrefix()
    {
        if (PHP_OS == 'WIN32' || PHP_OS == 'WINNT' || PHP_OS == 'Windows') {
            return 'exec ';
        }
        return '';
    }
}
