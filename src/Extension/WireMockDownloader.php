<?php
/**
 * This file is part of codeception-wiremock-extension.
 *
 * codeception-wiremock-extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * codeception-wiremock-extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with codeception-wiremock-extension.  If not, see <http://www.gnu.org/licenses/>.
 */
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
        return "https://repo1.maven.org/maven2/com/github/tomakehurst/"
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
