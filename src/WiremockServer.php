<?php
namespace Codeception\Extension;

class WiremockServer
{
    const LOG_FILE_NAME = 'wiremock.out';

    public function start($jarPath, $logsPath, $arguments)
    {
        $command = "java -jar {$jarPath}{$arguments}"
            . " | tee $logsPath/" . self::LOG_FILE_NAME
            . " > /dev/null 2>&1 &";
        echo "Running wiremock..." . PHP_EOL;
        exec($command);
    }

    public function stop($jarPath, $arguments)
    {
        $pid = $this->getRunningWiremockPid($jarPath, $arguments);

        if (!empty($pid)) {
            echo 'Shutting down wiremock... (PID: ' . $pid . ')' . PHP_EOL;
            exec('kill ' . $pid);
        }
    }

    private function getRunningWiremockPid($jarPath, $arguments)
    {
        $wiremockCommand = "java -jar {$jarPath}{$arguments}";
        $command = "ps -A -o pid,cmd | grep '$wiremockCommand' | grep -v grep |"
            . "sort -nr | head -n1 | awk '{print $1}'";
        $output = array();
        exec($command, $output);

        return trim(implode('', $output));
    }
}
