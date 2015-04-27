<?php

namespace Radio\Readability\Client;

use Radio\Readability\HttpClient;
use Radio\Readability\Exceptions\ApiException;

/**
 * Abstract Readability Client.
 *
 * @package   Radio\Readability
 * @author    Max Gopey <gopeyx@gmail.com>
 * @copyright 2015 Max Gopey
 * @license   http://opensource.org/licenses/MIT  MIT license
 */
abstract class AbstractClient
{
    /** @var HttpClient Http client. */
    protected $httpClient;

    /** @var string Api base url. */
    protected $apiUrl = 'https://www.readability.com/api';

    /** @var string Api instance path. */
    protected $apiPath;

    /**
     * Set the HTTP client instance.
     */
    public function __construct()
    {
        $this->httpClient = new HttpClient();
    }

    /**
     * Retrieve the base API URI - information about subresources.
     * @see https://www.readability.com/developers/api/reader#idm301962226464
     * @see https://www.readability.com/developers/api/parser#idm386426548416
     * @see https://www.readability.com/developers/api/shortener#idm211482166416
     *
     * @param array $parameters Request parameters.
     *
     * @return array
     */
    public function getResources($parameters = [])
    {
        $rawResponse = $this->httpClient->get($this->getUrl(''), $parameters);
        return $this->buildResponse($rawResponse);
    }

    /**
     * Get the full url to a resource.
     *
     * @param string $resource Resource sub-path.
     *
     * @return string
     */
    protected function getUrl($resource = '')
    {
        return $this->apiUrl . $this->apiPath . '/' . $resource;
    }

    /**
     * Build a response.
     *
     * @param string $rawResponse Raw json-encoded response.
     *
     * @throws ApiException
     *
     * @return array
     */
    protected function buildResponse($rawResponse)
    {
        $responseData = $this->decode($rawResponse);
        if ($responseData) {
            return $responseData;
        } else {
            throw new ApiException('Request was successful but response is malformed.');
        }
    }

    /**
     * Decode a json-encoded response.
     *
     * @param string $response Json-encoded response.
     *
     * @return array|null
     */
    protected function decode($response)
    {
        return json_decode($response, true);
    }
}