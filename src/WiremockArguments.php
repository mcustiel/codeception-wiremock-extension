<?php
namespace Codeception\Extension;

class WiremockArguments
{
    private $map = [
        'root-dir' => true,
        'port' => true,
        'https_port' => true,
        'http_keystore' => true,
        'keystore-password' => true,
        'https-truststore' => true,
        'truststore-password' => true,
        'https-require-client-cert' => false,
        'verbose' => false,
        'record-mappings' => true,
        'match-headers' => true,
        'proxy-all' => true,
        'preserve-host-header' => false,
        'proxy-via' => true,
        'enable-browser-proxying' => true,
        'no-request-journal' => true,
        'container-threads' => true,
        'max-request-journal-entries' => true,
        'jetty-acceptor-threads' => true,
        'jetty-accept-queue-size' => true,
        'jetty-header-buffer-size' => true
    ];

    private $defaults = [
        'download-version' => '1.57',
        'logs-path' => '/tmp',
        'port' => '8080',
        'start-delay' => '1',
    ];

    public function generateArgumentsString(array $config)
    {
        $result = "";

        foreach ($config as $key => $value) {
            if (isset($this->map[$key])) {
                $result .= " --{$key}" . ($this->map[$key]? " $value" : '');
            }
        }

        return $result;
    }

    public function sanitize(array $config)
    {
        $return = array_merge($this->defaults, $config);
        if (!ctype_digit($return['port']) || $return['port'] == 0 || $return['port'] > 65535) {
            throw new \Exception("Invalid HTTP port");
        }
        if (isset($return['https-port']) && !ctype_digit($return['https-port'])
            || $return['https-port'] == 0 || $return['https-port'] > 65535) {
            throw new \Exception("Invalid HTTPS port");
        }
        if (!ctype_digit('' . $return['start-delay'])) {
            throw new \Exception("Invalid delay time specified");
        }
        return $return;
    }
}
