<?php

namespace Radio\Readability;

/**
 * XAuth Http Client.
 *
 * @package   Radio\Readability
 * @author    Max Gopey <gopeyx@gmail.com>
 * @copyright 2015 Max Gopey
 * @license   http://opensource.org/licenses/MIT  MIT license
 */
class XauthClient extends HttpClient
{
    /** @var Consumer Consumer instance. */
    protected $consumer;

    /** @var Token Token instance. */
    protected $token;

    /** @var string Signature method used by hash_hmac() function. */
    protected $signatureMethod = 'sha1';

    /** @var string Authorization request method. */
    protected $authMethod = 'POST';

    /** @var array Default XAuth params. */
    protected $defaultParams = [
        'oauth_consumer_key' => null,
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp' => null,
        'oauth_nonce' => null,
        'oauth_token' => null,
        'oauth_version' => '1.0',
    ];

    /**
     * Set Consumer and Token instances.
     *
     * @param Consumer   $consumer
     * @param Token|null $token
     */
    public function __construct(Consumer $consumer, Token $token = null)
    {
        $this->consumer = $consumer;
        $this->token = $token ?: new Token();
    }

    /**
     * Add OAuth params before sending a request.
     *
     * @param string $url    Request URL.
     * @param string $method Request method.
     * @param array  $params Request params.
     *
     * @return mixed
     */
    public function request($url, $method = 'GET', $params = [])
    {
        $paramsWithAuth = $this->enrichParams($params, $url, $method);
        return parent::request($url, $method, $paramsWithAuth);
    }

    /**
     * Get a user token using username and password.
     *
     * @param string $authUrl  Authorization URL.
     * @param string $username Username.
     * @param string $password Password.
     *
     * @return Token
     */
    public function getToken($authUrl, $username = null, $password = null)
    {
        $params = $this->getXauthParams($username, $password);
        $response = $this->request($authUrl, $this->authMethod, $params);

        parse_str($response, $tokenInfo);
        if (isset($tokenInfo['oauth_token']) && isset($tokenInfo['oauth_token_secret'])) {
            $this->token = new Token($tokenInfo['oauth_token'], $tokenInfo['oauth_token_secret']);

            return $this->token;
        }
        return null;
    }

    /**
     * Set the token.
     *
     * @param \Radio\Readability\Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }

    /**
     * Enrich given params with OAuth params.
     *
     * @param array  $params Request params.
     * @param string $url    Request URL.
     * @param string $method Request method.
     *
     * @return array
     */
    protected function enrichParams(array $params, $url, $method)
    {
        $params = array_merge($this->defaultParams, $params);
        $params['oauth_consumer_key'] = $this->consumer->getKey();
        $params['oauth_timestamp'] = time();
        $params['oauth_nonce'] = $this->generateNonce();
        $params['oauth_token'] = $this->token->getValue();
        $params['oauth_signature'] = $this->getSignature($url, $method, $params);

        return $params;
    }

    /**
     * Get XAuth params.
     *
     * @param string $username Username.
     * @param string $password Password.
     *
     * @return array
     */
    protected function getXauthParams($username, $password)
    {
        return [
            'x_auth_username' => $username,
            'x_auth_password' => $password,
            'x_auth_mode' => 'client_auth'
        ];
    }

    /**
     * Get OAuth signature.
     *
     * @param string $url    Request URL.
     * @param string $method Request method.
     * @param array  $params Request params.
     *
     * @return string
     */
    protected function getSignature($url, $method, $params)
    {
        $baseString = $this->buildSignatureBaseString($url, $method, $params);
        $signingKey = $this->buildSigningKey();
        $signature = base64_encode(hash_hmac($this->signatureMethod, $baseString, $signingKey, true));

        return $signature;
    }

    /**
     * Build OAuth signature base string.
     *
     * @param string $url    Request URL.
     * @param string $method Request method.
     * @param array  $params Request params.
     *
     * @return string
     */
    protected function buildSignatureBaseString($url, $method, $params)
    {
        $string = strtoupper($method) . '&' . urlencode($url) . '&';

        $encodedParams = [];
        foreach ($params as $key => $value) {
            $encodedKey = urlencode($key);
            $encodedParams[$encodedKey] = $encodedKey . '=' . urlencode($value);
        }
        ksort($encodedParams);
        $string .= urlencode(implode('&', $encodedParams));

        return $string;
    }

    /**
     * Build OAuth signing key.
     *
     * @return string
     */
    protected function buildSigningKey()
    {
        return urlencode($this->consumer->getSecret()) . '&' . urlencode($this->token->getSecret());
    }

    /**
     * Generate random nonce.
     *
     * @return string
     */
    protected function generateNonce()
    {
        return md5(mt_rand());
    }
}