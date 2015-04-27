<?php

namespace Radio\Readability;

/**
 * Token Class.
 *
 * @package   Radio\Readability
 * @author    Max Gopey <gopeyx@gmail.com>
 * @copyright 2015 Max Gopey
 * @license   http://opensource.org/licenses/MIT  MIT license
 */
class Token
{
    /** @var string Token value. */
    protected $value;

    /** @var string Token secret. */
    protected $secret;

    /**
     * Set the token value and secret.
     *
     * @param string|null $value  Token value.
     * @param string|null $secret Token secret.
     */
    function __construct($value = null, $secret = null)
    {
        $this->secret = $secret;
        $this->value = $value;
    }

    /**
     * Get token secret.
     *
     * @return string
     */
    public function getSecret()
    {
        return (string) $this->secret;
    }

    /**
     * Get token value.
     *
     * @return string
     */
    public function getValue()
    {
        return (string) $this->value;
    }

    /**
     * Get a token value on converting to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * Configure serialization.
     *
     * @return array
     */
    public function __sleep()
    {
        return ['value', 'secret'];
    }
}