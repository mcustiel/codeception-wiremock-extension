<?php
namespace Codeception\Extension;

class WiremockDownloader
{
    const DESTINATION_PATH = '/tmp/codeceptionWiremock/jars/';

    public function downloadAndGetLocalJarPath($version)
    {
        $filePath = self::DESTINATION_PATH . "wiremock-{$version}.jar";
        if (!file_exists($filePath)) {
            $this->downloadToPath($version, $filePath);
        }
        return $filePath;
    }

    private function buildUrlForVersion($version)
    {
        return "http://repo1.maven.org/maven2/com/github/tomakehurst/"
            . "wiremock/{$version}/wiremock-{$version}.jar";
    }

    private function downloadAndReturnFilePath($version, $filePath)
    {
        $url = $this->buildUrlForVersion($version);
        file_put_contents(
            $filePath,
            file_get_contents($url)
        );
    }
}
