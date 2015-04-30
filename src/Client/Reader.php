<?php

namespace Radio\Readability\Client;

use Radio\Readability\Exceptions\ApiException;
use Radio\Readability\Consumer;
use Radio\Readability\Token;
use Radio\Readability\XauthClient;

/**
 * Readability Reader Client.
 *
 * @package   Radio\Readability
 * @author    Max Gopey <gopeyx@gmail.com>
 * @copyright 2015 Max Gopey
 * @license   http://opensource.org/licenses/MIT  MIT license
 */
class Reader extends AbstractClient
{
    /** @var string Api instance path. */
    protected $apiPath = '/rest/v1';

    /**
     * Set the Auth instance.
     *
     * @param Consumer $consumer Consumer instance.
     * @param Token    $token    Token instance.
     *
     * @throws ApiException
     */
    public function __construct(Consumer $consumer, Token $token = null)
    {
        $this->httpClient = new XauthClient($consumer, $token);
    }

    /**
     * Set auth token.
     *
     * @param Token $token Token instance.
     */
    public function setToken(Token $token)
    {
        $this->httpClient->setToken($token);
    }

    /**
     * Authorize a client with user credentials.
     *
     * @param string $username Username.
     * @param string $password Password.
     *
     * @throws ApiException
     *
     * @return \Radio\Readability\Token
     */
    public function authorize($username = null, $password = null)
    {
        $token = $this->httpClient->getToken($this->getUrl('oauth/access_token'), $username, $password);
        if (!$token) {
            throw new ApiException('Auth request was successful but token info was not found.');
        }
        return $token;
    }

    /**
     * Retrieve a single Article, including its content. Accessible by any authenticated user.
     * @see https://www.readability.com/developers/api/reader#idm301959958480
     *
     * @param string $articleId Article ID.
     *
     * @return array
     */
    public function getArticle($articleId)
    {
        $rawResponse = $this->httpClient->get($this->getUrl(sprintf('articles/%s', $articleId)));
        return $this->buildResponse($rawResponse);
    }

    /**
     * Retrieve the bookmarks collection. Automatically filtered to the current user.
     * @see https://www.readability.com/developers/api/reader#idm301959944144
     *
     * @param array $parameters Query parameters.
     *
     * @return array
     */
    public function getBookmarks($parameters = [])
    {
        $rawResponse = $this->httpClient->get($this->getUrl('bookmarks'), $parameters);
        return $this->buildResponse($rawResponse);
    }

    /**
     * Add a bookmark.
     * Does not guarantee that an article for the given url will be parsed successfully.
     * @see https://www.readability.com/developers/api/reader#idm301959945920
     *
     * @param string $url The URL of the article to associate the bookmark with.
     * @param bool $favorite Whether this article is favorited or not.
     * @param bool $archive Whether this article is archived or not.
     * @param bool $allowDuplicates Whether the bookmark should be recreated if it already exists.
     *
     * @return bool
     */
    public function addBookmark($url, $favorite = false, $archive = false, $allowDuplicates = false)
    {
        $parameters = [
            'url' => $url,
            'favorite' => (int) $favorite,
            'archive' => (int) $archive,
            'allow_duplicates' => (int) $allowDuplicates
        ];
        $this->httpClient->post($this->getUrl('bookmarks'), $parameters);
        return true;
    }

    /**
     * Retrieve a single bookmark.
     * @see https://www.readability.com/developers/api/reader#idm301959851200
     *
     * @param int $bookmarkId Bookmark ID.
     *
     * @return array
     */
    public function getBookmark($bookmarkId)
    {
        $rawResponse = $this->httpClient->get($this->getUrl(sprintf('bookmarks/%d', $bookmarkId)));
        return $this->buildResponse($rawResponse);
    }

    /**
     * Update a bookmark.
     * @see https://www.readability.com/developers/api/reader#idm301959838128
     *
     * @param int   $bookmarkId Bookmark ID.
     * @param array $parameters Bookmark parameters.
     *
     * @return bool
     */
    public function updateBookmark($bookmarkId, $parameters)
    {
        if (isset($parameters['favorite'])) {
            $parameters['favorite'] = (int) $parameters['favorite'];
        }
        if (isset($parameters['archive'])) {
            $parameters['archive'] = (int) $parameters['archive'];
        }
        if (isset($parameters['read_percent'])) {
            $parameters['read_percent'] = (float) $parameters['read_percent'];
        }
        $rawResponse = $this->httpClient->post($this->getUrl(sprintf('bookmarks/%d', $bookmarkId)), $parameters);
        return $this->buildResponse($rawResponse);
    }

    /**
     * Delete a bookmark.
     * @see https://www.readability.com/developers/api/reader#idm301959812544
     *
     * @param int $bookmarkId Bookmark ID.
     *
     * @return bool
     */
    public function deleteBookmark($bookmarkId)
    {
        $this->httpClient->delete($this->getUrl(sprintf('bookmarks/%d', $bookmarkId)));
        return true;
    }

    /**
     * Retrieve all Tags attached to the Bookmark.
     * @see https://www.readability.com/developers/api/reader#idm301959798976
     *
     * @param int $bookmarkId Bookmark ID.
     *
     * @return array
     */
    public function getBookmarkTags($bookmarkId)
    {
        $rawResponse = $this->httpClient->get($this->getUrl(sprintf('bookmarks/%d/tags', $bookmarkId)));
        return $this->buildResponse($rawResponse);
    }

    /**
     * Add Tags to a bookmark.
     * @see https://www.readability.com/developers/api/reader#idm301959786688
     *
     * @param int $bookmarkId Bookmark ID.
     * @param array $tags List of tags to be applied to the bookmark.
     *
     * @return array
     */
    public function addBookmarkTags($bookmarkId, $tags)
    {
        $parameters = ['tags' => implode(',', $tags)];
        $rawResponse = $this->httpClient->post($this->getUrl(sprintf('bookmarks/%d/tags', $bookmarkId)), $parameters);
        return $this->buildResponse($rawResponse);
    }

    /**
     * Delete a Tag from a bookmark.
     * @see https://www.readability.com/developers/api/reader#idm301959757728
     *
     * @param int $bookmarkId Bookmark ID.
     * @param int $tagId Tag ID.
     *
     * @return bool
     */
    public function deleteBookmarkTag($bookmarkId, $tagId)
    {
        $this->httpClient->delete($this->getUrl(sprintf('bookmarks/%d/tags/%d', $bookmarkId, $tagId)));
        return true;
    }

    /**
     * Retrieve all Tags for the current user.
     * @see https://www.readability.com/developers/api/reader#idm301959754416
     *
     * @return array
     */
    public function getTags()
    {
        $rawResponse = $this->httpClient->get($this->getUrl('tags'));
        return $this->buildResponse($rawResponse);
    }

    /**
     * Retrieve data on a single Tag.
     * @see https://www.readability.com/developers/api/reader#idm301959736672
     *
     * @param int $tagId Tag ID.
     *
     * @return array
     */
    public function getTag($tagId)
    {
        $rawResponse = $this->httpClient->get($this->getUrl(sprintf('tags/%d', $tagId)));
        return $this->buildResponse($rawResponse);
    }

    /**
     * Delete an entire Tag for a user.
     * @see https://www.readability.com/developers/api/reader#idm301959719696
     *
     * @param int $tagId Tag ID.
     *
     * @return bool
     */
    public function deleteTag($tagId)
    {
        $this->httpClient->delete($this->getUrl(sprintf('tags/%d', $tagId)));
        return true;
    }

    /**
     * Retrieve the current user's information.
     * @see https://www.readability.com/developers/api/reader#idm301959713952
     *
     * @return array
     */
    public function getUserInfo()
    {
        $rawResponse = $this->httpClient->get($this->getUrl('users/_current'));
        return $this->buildResponse($rawResponse);
    }
}