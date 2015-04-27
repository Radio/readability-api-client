<?php

namespace Radio\Readability\Client;

use Radio\Readability\Exceptions\ApiException;
use Radio\Readability\Consumer;

/**
 * Readability Parser Client.
 *
 * @package   Radio\Readability
 * @author    Max Gopey <gopeyx@gmail.com>
 * @copyright 2015 Max Gopey
 * @license   http://opensource.org/licenses/MIT  MIT license
 */
class Parser extends AbstractClient /* implements ClientInterface */
{
    /** @var string Api instance path. */
    protected $apiPath = '/content/v1';

    /** @var Consumer */
    protected $consumer;

    /**
     * Set the Consumer instance.
     *
     * @param Consumer $consumer Consumer instance.
     */
    public function __construct(Consumer $consumer)
    {
        $this->consumer = $consumer;
        parent::__construct();
    }

    /**
     * Retrieve the base API URI - information about subresources.
     * @see https://www.readability.com/developers/api/parser#idm301962226464
     *
     * @return array
     */
    public function getResources()
    {
        return parent::getResources($this->getAuthParams());
    }

    /**
     * Parse an article.
     * @see https://www.readability.com/developers/api/parser#idm386426514592
     *
     * @param string      $url       The URL of an article to return the content for.
     * @param string|null $articleId The ID of an article to return the content for.
     * @param int|null    $maxPages  The maximum number of pages to parse and combine.
     *
     * @return array
     */
    public function parse($url, $articleId = null, $maxPages = null)
    {
        $parameters = $this->getAuthParams();
        if ($url) {
            $parameters['url'] = $url;
        }
        if ($articleId) {
            $parameters['id'] = $articleId;
        }
        if ($maxPages) {
            $parameters['max_pages'] = $maxPages;
        }
        $rawResponse = $this->httpClient->get($this->getUrl('parser'), $parameters);
        return $this->buildResponse($rawResponse);
    }

    /**
     * Retrieve the Content Status of an article.
     * @see https://www.readability.com/developers/api/parser#idm386426487680
     *
     * @param string      $url       The URL of an article to check.
     * @param string|null $articleId The ID of an article to check.
     *
     * @throws \Radio\Readability\Exceptions\ApiException
     *
     * @return array
     */
    public function getStatus($url, $articleId = null)
    {
        $parameters = $this->getAuthParams();
        if ($url) {
            $parameters['url'] = $url;
        }
        if ($articleId) {
            $parameters['id'] = $articleId;
        }
        $rawHeaders = $this->httpClient->head($this->getUrl('parser'), $parameters);
        $headers = $this->httpClient->parseHeaders($rawHeaders);
        if (isset($headers['X-Article-Id']) && isset($headers['X-Article-Status'])) {
            return [
                'id' => $headers['X-Article-Id'],
                'status' => $headers['X-Article-Status']
            ];
        } else {
            throw new ApiException('Request was successful but status info is missing from headers.');
        }
    }

    /**
     * Detect the confidence with which Readability could parse a given URL.
     * @see https://www.readability.com/developers/api/parser#idm386426115952
     *
     * @param string $url The URL of an article to return the confidence for.
     *
     * @return array
     */
    public function getConfidence($url)
    {
        $parameters = ['url' => $url];
        $rawResponse = $this->httpClient->get($this->getUrl('confidence'), $parameters);
        return $this->buildResponse($rawResponse);
    }

    /**
     * Provide consumer token.
     *
     * @return array
     */
    protected function getAuthParams()
    {
        return ['token' => $this->consumer->getToken()];
    }
}