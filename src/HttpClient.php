<?php

namespace Radio\Readability;

use Radio\Readability\Exceptions\ApiException;

/**
 * Simple Http Client using curl.
 *
 * @package   Radio\Readability
 * @author    Max Gopey <gopeyx@gmail.com>
 * @copyright 2015 Max Gopey
 * @license   http://opensource.org/licenses/MIT  MIT license
 */
class HttpClient
{
    /** @var resource Connection resource. */
    protected $connection;

    /**
     * Close any connection on destruction.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Send a GET request.
     *
     * @param string $url    Request URL.
     * @param array  $params Request params.
     *
     * @return mixed
     */
    public function get($url, $params = [])
    {
        return $this->request($url, 'GET', $params);
    }

    /**
     * Send a POST request.
     *
     * @param string $url    Request URL.
     * @param array  $params Request params.
     *
     * @return mixed
     */
    public function post($url, $params = [])
    {
        return $this->request($url, 'POST', $params);
    }

    /**
     * Send a DELETE request.
     *
     * @param string $url    Request URL.
     * @param array  $params Request params.
     *
     * @return mixed
     */
    public function delete($url, $params = [])
    {
        return $this->request($url, 'DELETE', $params);
    }

    /**
     * Send a PUT request.
     *
     * @param string $url    Request URL.
     * @param array  $params Request params.
     *
     * @return mixed
     */
    public function put($url, $params = [])
    {
        return $this->request($url, 'PUT', $params);
    }

    /**
     * Send a HEAD request.
     *
     * @param string $url    Request URL.
     * @param array  $params Request params.
     *
     * @return mixed
     */
    public function head($url, $params = [])
    {
        return $this->request($url, 'HEAD', $params);
    }

    /**
     * Send a request.
     *
     * @param string $url    Request URL.
     * @param string $method Request method.
     * @param array  $params Request params.
     *
     * @throws ApiException
     *
     * @return mixed
     */
    public function request($url, $method = 'GET', $params = [])
    {
        $this->connect();

        $query = http_build_query($params);
        if (in_array($method, ['GET', 'DELETE', 'HEAD'])) {
            $url .= '?' . $query;
        }
        curl_setopt($this->connection, CURLOPT_URL, $url);
        curl_setopt($this->connection, CURLOPT_HEADER, ($method === 'HEAD'));
        curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->connection, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if ($method === 'POST') {
            curl_setopt($this->connection, CURLOPT_POSTFIELDS, $query);
            curl_setopt($this->connection, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        }

        $response = curl_exec($this->connection);
        $httpCode = curl_getinfo($this->connection, CURLINFO_HTTP_CODE);

        $this->disconnect();

        if (in_array($httpCode, [200, 201, 202, 203, 204])) {
            return $response;
        } else {
            throw new ApiException($response, $httpCode);
        }
    }

    /**
     * Parse raw response headers into array.
     *
     * @param string $rawHeaders Raw headers.
     *
     * @return array
     */
    public function parseHeaders($rawHeaders)
    {
        if (function_exists('http_parse_headers')) {
            return http_parse_headers($rawHeaders);
        } else {
            $headers = array();
            $key = '';

            foreach(explode("\n", $rawHeaders) as $headerLine) {
                $header = explode(':', $headerLine, 2);
                if (isset($header[1])) {
                    if (!isset($headers[$header[0]])) {
                        $headers[$header[0]] = trim($header[1]);
                    } elseif (is_array($headers[$header[0]])) {
                        $headers[$header[0]] = array_merge($headers[$header[0]], array(trim($header[1])));
                    } else {
                        $headers[$header[0]] = array_merge(array($headers[$header[0]]), array(trim($header[1])));
                    }
                    $key = $header[0];
                } else {
                    if (substr($header[0], 0, 1) == "\t") {
                        $headers[$key] .= "\r\n\t" . trim($header[0]);
                    } elseif (!$key) {
                        $headers[0] = trim($header[0]);
                        trim($header[0]);
                    }
                }
            }

            return $headers;
        }
    }

    /**
     * Open curl connection.
     */
    protected function connect()
    {
        if (!$this->connection) {
            $this->connection = curl_init();
        }
    }

    /**
     * Close curl connection.
     */
    protected function disconnect()
    {
        if ($this->connection) {
            curl_close($this->connection);
            $this->connection = null;
        }
    }
}