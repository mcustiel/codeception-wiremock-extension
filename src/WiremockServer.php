<?php
namespace Codeception\Extension;

class WiremockServer
{
    const LOG_FILE_NAME = 'wiremock.out';

    public function start($jarPath, $logsPath, $arguments)
    {
        $command = "(java -jar {$jarPath}"
            . $this->getArgumentsForCommandLine($arguments)
            . " | tee $logsPath/"
            . self::LOG_FILE_NAME . " > /dev/null 2>&1 &)";
        echo $command . PHP_EOL;
        echo exec($command);
        sleep(1);
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
        $wiremockCommand = "java -jar {$jarPath}" . $this->getArgumentsForCommandLine($arguments);
        $command = "ps -A -o pid,cmd | grep '$wiremockCommand' | grep -v grep |"
            . "sort -nr | head -n1 | awk '{print $1}'";
        echo 'Running command ' . $command . PHP_EOL;
        $output = array();
        $error = 0;
        exec($command, $output, $error);

        return trim(implode('', $output));
    }


    private function getArgumentsForCommandLine($arguments)
    {
        $argString = '';
        foreach ($arguments as $argument => $value) {
            $argString .= " $argument $value";
        }
        return $argString;
    }
}
