<?php
/**
 * To parse a URL run:
 * > php parser.php %url%
 */

require __DIR__  . '/../vendor/autoload.php';

$consumer = include __DIR__ . '/consumer.php';

use Radio\Readability\Client;
use Radio\Readability\Consumer;
use Radio\Readability\Exceptions\ApiException;

// Consumer key and secret are not needed for parser.
$parser = new Client\Parser(new Consumer(null, null, $consumer['token']));

try {
    $response = $parser->parse($argv[1]);
    print_r($response);
} catch (ApiException $e) {
    echo 'Exception: ', $e->getCode(), ': ', $e->getMessage();
}