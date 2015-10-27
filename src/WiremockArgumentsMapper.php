<?php
namespace Codeception\Extension;

class WiremockArgumentsMapper
{
    private $map = [
        "root_dir" => "--root-dir",
        "port" => "--port",
        "https_port" => "--https-port",
        "http_keystore" => "--http-keystore",
    ];

    public function map(array $config)
    {
        $result = [];

        foreach ($config as $key => $value) {
            if (isset($this->map[$key])) {
                $result[$this->map[$key]] = $value;
            }
        }

        return $result;
    }
}
