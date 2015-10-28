<?php
namespace Codeception\Extension;

class WireMockDownloader
{
    const DESTINATION_PATH = '/tmp/codeceptionWireMock/jars/';

    public function downloadAndGetLocalJarPath($version)
    {
        if (!is_dir(self::DESTINATION_PATH)) {
            mkdir(self::DESTINATION_PATH, 0777, true);
        }
        $filePath = self::DESTINATION_PATH . "wiremock-{$version}.jar";
        if (!file_exists($filePath)) {
            $this->downloadToPath($version, $filePath);
        }
        return $filePath;
    }

    private function buildUrlForVersion($version)
    {
        return "http://repo1.maven.org/maven2/com/github/tomakehurst/"
            . "wiremock/{$version}/wiremock-{$version}-standalone.jar";
    }

    private function downloadToPath($version, $filePath)
    {
        $url = $this->buildUrlForVersion($version);
        if (!file_put_contents(
            $filePath,
            file_get_contents($url)
        )) {
            throw new \Exception('Could not download the specified version ' . $version);
        };
    }
}
