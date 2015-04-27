<?php

namespace Radio\Readability;

/**
 * Consumer Class.
 *
 * @package   Radio\Readability
 * @author    Max Gopey <gopeyx@gmail.com>
 * @copyright 2015 Max Gopey
 * @license   http://opensource.org/licenses/MIT  MIT license
 */
class Consumer
{
    /** @var string Consumer key. */
    protected $key;

    /** @var string Consumer secret. */
    protected $secret;

    /** @var string Consumer token. */
    protected $token;

    /**
     * Set the key, secret and/or token.
     *
     * @param string      $key    Consumer key.
     * @param string      $secret Consumer secret.
     * @param string|null $token  Consumer token.
     */
    function __construct($key, $secret, $token = null)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->token = $token;
    }

    /**
     * Set the key.
     *
     * @param string $key Consumer key.
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the secret.
     *
     * @param string $secret Consumer secret.
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Get the secret.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set the token.
     *
     * @param string $token Consumer token.
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Get the token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}