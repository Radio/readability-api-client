# readability-api-client
The PHP clients library for the [Readability API](https://www.readability.com/developers/api).

## License

MIT. [Read the LICENSE.md](LICENSE.md).

## Requirements

* PHP >= 5.4

## Usage

See sources in [examples](examples) folder for more details.

### Reader

```php
use Radio\Readability\Client;
use Radio\Readability\Consumer;
use Radio\Readability\Exceptions\ApiException;

$consumerKey = 'Your consumer key';
$consumerSecret = 'Your consumer secret';

$reader = new Client\Reader(new Consumer($consumerKey, $consumerSecret));

try {
    $username = $argv[1];
    $password = $argv[2];
    $token = $reader->authorize($username, $password);

    $response = $reader->getBookmarks();
    print_r($response);

} catch (ApiException $e) {
    echo 'Exception: ', $e->getCode(), ': ', $e->getMessage(), PHP_EOL;
}
```

### Parser

```php
use Radio\Readability\Client;
use Radio\Readability\Consumer;
use Radio\Readability\Exceptions\ApiException;

$consumerToken = 'Your consumer token';

$parser = new Client\Parser(new Consumer(null, null, $consumerToken));

try {
    $url = $argv[1];
    $response = $parser->parse($url);
    print_r($response);

} catch (ApiException $e) {
    echo 'Exception: ', $e->getCode(), ': ', $e->getMessage(), PHP_EOL;
}
```

### Shortener

```php
use Radio\Readability\Client;

// Consumer is not needed for Shortener.
$shortener = new Client\Shortener();

try {
    $url = $argv[1];
    $response = $shortener->create($url);
    print_r($response);
} catch (\Radio\Readability\Exceptions\ApiException $e) {
    echo 'Exception: ', $e->getCode(), ': ', $e->getMessage(), PHP_EOL;
}
```