<?php
/**
 * To authorize user and create a token run:
 * > php reader.php %username% %password%
 * This will save token into a file token.txt inside this directory.
 * Further executions will read the token from file and use it to authorize the requests.
 * Just run:
 * > php reader.php
 */

require __DIR__  . '/../vendor/autoload.php';

$consumer = include __DIR__ . '/consumer.php';

use Radio\Readability\Client;
use Radio\Readability\Consumer;
use Radio\Readability\Exceptions\ApiException;

// Consumer token is not needed for reader.
$reader = new Client\Reader(new Consumer($consumer['key'], $consumer['secret']));
$tokenFile = __DIR__ . '/token.txt';

try {
    if (is_readable($tokenFile)) {
        $token = unserialize(file_get_contents($tokenFile));
        $reader->setToken($token);

        $response = $reader->getBookmarks();
        print_r($response);

        // Try other methods here.

    } else {
        $username = $argv[1];
        $password = $argv[2];
        $token = $reader->authorize($username, $password);
        file_put_contents($tokenFile, serialize($token));
    }
} catch (ApiException $e) {
    echo 'Exception: ', $e->getCode(), ': ', $e->getMessage(), PHP_EOL;
}