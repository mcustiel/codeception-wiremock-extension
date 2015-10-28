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
            ['file', $logFile, 'w'],
            ['file', $logFile, 'a'],
        ];
        echo "Command - " . $this->getCommandPrefix() . "java -jar {$jarPath}{$arguments}";
        $this->process = proc_open(
            $this->getCommandPrefix() . "java -jar {$jarPath}{$arguments}",
            $descriptors,
            $this->pipes,
            null,
            null,
            ['bypass_shell' => true]
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
        if (is_resource($this->process)) {
            foreach ($this->pipes AS $pipe) {
                if (is_resource($pipe)) {
                    fflush($pipe);
                    fclose($pipe);
                }
            }
            proc_terminate($this->process, SIGKILL);
            var_export(proc_get_status($this->process));
            proc_close($this->process, SIGKILL);
            unset($this->process);
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
