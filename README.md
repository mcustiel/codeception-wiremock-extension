# codeception-wiremock-extension

This Codeception Extension allows developers and testers to use WireMock to mock external services when running codeception tests.

codeception-wiremock-extension connects to an already running instance of WireMock or can also run automatically a local standalone one. And, it is able to download the version of wiremock you preffer and run it too.
After the tests are finished it will close the connection and turn wiremock service off (when it started it).

## See also

* WireMock PHP library: https://github.com/rowanhill/wiremock-php
* Stubbing using WireMock: http://wiremock.org/stubbing.html
* Verifying using WireMock: http://wiremock.org/verifying.html

## Installation

### Composer:

This project is published in packagist, so you just need to add it as a dependency in your composer.json:

```javascript
    "require": {
        // ...
        "mcustiel/php-simple-request": "*"
    }
```

If you want to access directly to this repo, adding this to your composer.json should be enough:

```javascript  
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mcustiel/php-simple-request"
        }
    ],
    "require": {
        "mcustiel/php-simple-request": "dev-master"
    }
}
```

Or just download the release and include it in your path.

## Configuration Examples

### Module

```yaml
# acceptance.suite.yml
modules:
    enabled:
        - WireMock
```

### Extension

#### Default configuration 

This configuration will download WireMock version 1.57 and run it on port 8080, writing logs to `/tmp/codeceptionWireMock/logs`

```yaml
# codeception.yml
extensions:
    enabled:
        - Codeception\Extension\WireMock    
```


#### Connect to a running WireMock instance

```yaml
# codeception.yml
extensions:
    enabled:
        - Codeception\Extension\WireMock
    config:     
        Codeception\Extension\WireMock:
            host: my.wiremock.server
            port: 8080
```

#### Start a local WireMock instance and run it with given command line arguments

```yaml
# codeception.yml
extensions:
    enabled:
        - Codeception\Extension\WireMock
    config:     
        Codeception\Extension\WireMock:
            jar-path: /opt/wiremock/bin/wiremock-standalone.jar
            port: 18080
            https-port: 18443
            verbose: true
            root-dir: /opt/wiremock/root
            
```

#### Download a WireMock instance and run it with given command line arguments

```yaml
# codeception.yml
extensions:
    enabled:
        - Codeception\Extension\WireMock
    config:     
        Codeception\Extension\WireMock:
            download-version: 1.57
            port: 18080
            https-port: 18443
            verbose: true
            root-dir: /opt/wiremock/root
            logs-path: /var/log/wiremock 
```

## How to use

### Prepare your application

First of all, configure your application so when it is being tested it will replace its external services with WireMock.
For instance, if you make some requests to a REST service located under http://your.rest.interface, replace that url in configuration with the url where WireMock runs, for instance: http://localhost:8080/rest_interface.

### Write your tests

```php
// YourCest.php
class YourCest extends \Codeception\TestCase\Test
{
    public function _after(\AcceptanceTester $I)
    {
        $I->cleanAllPreviousRequestsToWireMock();
    }

    // tests
    public function tryToTest(\AcceptanceTester $I)
    {
        $I->expectRequestToWireMock(
            WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->willReturn(WireMock::aResponse()
                ->withHeader('Content-Type', 'text/plain')
                ->withBody('Hello world!'))
        );
        // Here you should execute your application in a way it requests wiremock. I do this directly to show it. 
        $response = file_get_contents('http://localhost:18080/some/url');
        
        $I->assertEquals('Hello world!', $response);
        $I->receivedRequestInWireMock(
            WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
        );
    }
    
    // Also, you can access wiremock-php library directly
    public function moreComplexTest()
    {
        
    }
}
```
