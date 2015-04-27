<?php

namespace Radio\Readability\Client;

/**
 * Readability Shortener Client.
 *
 * @package   Radio\Readability
 * @author    Max Gopey <gopeyx@gmail.com>
 * @copyright 2015 Max Gopey
 * @license   http://opensource.org/licenses/MIT  MIT license
 */
class Shortener extends AbstractClient
{
    /** @var string Api instance path. */
    protected $apiPath = '/shortener/v1';

    /**
     * Create a new Shortened URL.
     * @see https://www.readability.com/developers/api/shortener#idm211482132000
     *
     * @param string $url The article URL to shorten.
     *
     * @return array
     */
    public function create($url)
    {
        $parameters = ['url' => $url];
        $rawResponse = $this->httpClient->post($this->getUrl('urls'), $parameters);
        return $this->buildResponse($rawResponse);
    }

    /**
     * Retrieve a single shortened URL.
     * @see https://www.readability.com/developers/api/shortener#idm211482121488
     *
     * @param string $urlId The shortened URL ID.
     *
     * @return array
     */
    public function get($urlId)
    {
        $rawResponse = $this->httpClient->get($this->getUrl(sprintf('urls/%s', $urlId)));
        return $this->buildResponse($rawResponse);
    }
}