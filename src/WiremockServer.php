<?php
namespace Codeception\Extension;

class WiremockServer
{
    const LOG_FILE_NAME = 'wiremock.out';

    public function startWiremock($jarPath, $logsPath, $arguments)
    {
        echo exec(
            "(java -jar {$jarPath}"
            . $this->getArgumentsForCommandLine($arguments)
            . " | tee $logsPath/"
            . self::LOG_FILE_NAME . " > /dev/null 2>&1 &) && echo $!"
        );
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
