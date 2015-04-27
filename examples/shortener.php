<?php
/**
 * To get a shortened URL run:
 * > php shortener.php %url%
 */

require __DIR__  . '/../vendor/autoload.php';

use Radio\Readability\Client;

// Consumer is not needed for Shortener.
$shortener = new Client\Shortener();

try {
    $response = $shortener->create($argv[1]);
    print_r($response);
} catch (\Radio\Readability\Exceptions\ApiException $e) {
    echo 'Exception: ', $e->getCode(), ': ', $e->getMessage();
}