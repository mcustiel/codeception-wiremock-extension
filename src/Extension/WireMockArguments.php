<?php
namespace Codeception\Extension;

/**
 * Utility class to work with config parameters and wiremock command line arguments.
 */
class WireMockArguments
{
    /**
     * Allowed wiremock arguments.
     *
     * @var array
     */
    private $allowedWiremockArguments = [
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
    /**
     * Default values for minimum needed parameters.
     *
     * @var array
     */
    private $defaults = [
        'download-version' => '1.57',
        'port' => '8080',
        'start-delay' => '1',
    ];

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->defaults['logs-path'] = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR . 'codeceptionWireMock'
            . DIRECTORY_SEPARATOR . 'logs';
    }

    /**
     * Converts the wiremock arguments array to a cli arguments string.
     *
     * @param array $config
     *
     * @return string
     */
    public function generateArgumentsString(array $config)
    {
        $result = "";

        foreach ($config as $key => $value) {
            if (isset($this->allowedWiremockArguments[$key])) {
                $result .= $this->evaluateValueConfig($key, $this->allowedWiremockArguments[$key], $value);
            }
        }

        return $result;
    }

    /**
     * Generates an argument string with or without parameters.
     *
     * @param string  $key
     * @param boolean $withValue
     * @param string  $value
     *
     * @return string
     */
    private function evaluateValueConfig($key, $withValue, $value)
    {
        if ($withValue) {
            return " --{$key} {$value}";
        }
        if ($value) {
            return " --{$key}";
        }
        return '';
    }

    /**
     * @param array $config
     * @return array
     *
     * @throws \Exception
     */
    public function sanitize(array $config)
    {
        $return = array_merge($this->defaults, $config);
        $this->checkLogsPath($return);

        if (!ctype_digit('' . $return['port']) || $return['port'] == 0 || $return['port'] > 65535) {
            throw new \Exception("Invalid HTTP port");
        }
        if (isset($return['https-port']) && (!ctype_digit($return['https-port'])
            || $return['https-port'] == 0 || $return['https-port'] > 65535)) {
            throw new \Exception("Invalid HTTPS port");
        }
        if (!ctype_digit('' . $return['start-delay'])) {
            throw new \Exception("Invalid delay time specified");
        }
        return $return;
    }

    /**
     * @param array $config
     */
    private function checkLogsPath($config)
    {
        if (isset($config['logs-path'])) {
            $config['logs-path'] = rtrim($config['logs-path'], DIRECTORY_SEPARATOR);
            if (!is_dir($config['logs-path'])) {
                mkdir($config['logs-path'], 0777, true);
            }
            if (!is_writable($config['logs-path'])) {
                throw \Exception("Logs directory ({$config['logs-path']}) is not writable");
            }
        }
    }
}
