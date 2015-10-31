<?php
namespace Codeception\Extension;

/**
 * Utility class to download WireMock.
 */
class WireMockDownloader
{
    /**
     * Downloads WireMock if needed and returns the path to local file.
     *
     * @param string $version
     *
     * @return string
     */
    public function downloadAndGetLocalJarPath($version)
    {
        $destinationPath = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR . 'codeceptionWireMock'
            . DIRECTORY_SEPARATOR . 'jars';
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        $filePath = $destinationPath . DIRECTORY_SEPARATOR . "wiremock-{$version}.jar";
        if (!file_exists($filePath)) {
            $this->downloadToPath($version, $filePath);
        }
        return $filePath;
    }

    /**
     * Generates url to download WireMock.
     *
     * @param string $version
     *
     * @return string
     */
    private function buildUrlForVersion($version)
    {
        return "http://repo1.maven.org/maven2/com/github/tomakehurst/"
            . "wiremock/{$version}/wiremock-{$version}-standalone.jar";
    }

    /**
     * Downloads WireMock.
     *
     * @param string $version
     * @param string $filePath
     *
     * @throws \Exception
     */
    private function downloadToPath($version, $filePath)
    {
        $url = $this->buildUrlForVersion($version);
        echo "Downloading wiremock version {$version}..." . PHP_EOL;
        if (!file_put_contents(
            $filePath,
            file_get_contents($url)
        )) {
            throw new \Exception('Could not download the specified version ' . $version);
        };
    }
}
