<?php

/**
 * ownCloud - App Framework
 *
 * @author Bernhard Posselt, Thomas Tanghus, Bart Visscher
 * @copyright 2012 Bernhard Posselt nukeawhale@gmail.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


namespace OCA\AppFramework\Http;


/**
 * Baseclass for responses. Also used to just send headers
 */
class Response {

	const STATUS_FOUND = 304;
	const STATUS_NOT_MODIFIED = 304;
	const STATUS_TEMPORARY_REDIRECT = 307;
	const STATUS_FORBIDDEN = 403;
	const STATUS_NOT_FOUND = 404;

	/**
	 * @var array
	 */
	private $headers;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var Request;
	 */
	protected $request;

	/**
	 * @param Request $request Optional Request object required for methods querying server variables.
	 */
	public function __construct($request = null) {
		$this->headers = array();
		$this->request = $request;
	}

	/**
	 * Adds a new header to the response that will be called before the render
	 * function
	 * @param string $name The name of the HTTP header
	 * @param string $value The value
	 */
	public function addHeader($name, $value) {
		$this->headers[$name] = $value;
	}


	/**
	 * By default renders no output
	 * @return null
	 */
	public function render() {
		return null;
	}


	/**
	 * Returns the set headers
	 * @return array the headers
	 */
	public function getHeaders() {
		return $this->headers;
	}


	/**
	 * Cache for an unlimited timespan
	 */
	public function cacheIndefinitely() {
		$this->addHeader('Pragma', 'cache');
		$this->addHeader('Cache-Control', 'cache');
	}


	/**
	 * Shortcut for cacheFor
	 */
	public function disableCaching() {
		$this->cacheFor(0);
	}


	/**
	 * Enable response caching by sending correct HTTP headers
	 *
	 * @param int $cacheTime time to cache the response in seconds
	 */
	public function cacheFor($deltaSeconds) {

		$this->addHeader('Pragma', 'public');

		if($deltaSeconds > 0) {
			$this->addHeader('Cache-Control', 'max-age=' . $deltaSeconds . ', must-revalidate');
		} else {
			$this->addHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
		}

	}


	/**
	 * Sets the Expire date of the content. If cache is set, this is ignored
	 * @param DateTime $expiresAt the date and time when it expires
	 */
	public function expiresAt(DateTime $expiresAt) {
		$this->addHeader('Expires', $expiresAt->format(\DateTime::RFC2822));
	}


	/**
	* Checks and set ETag header, when the request matches sends a
	* 'not modified' response
	* @param string $etag token to use for modification check
	*/
	public function setETagHeader($etag) {
		if(is_null($this->request)) {
			throw new \BadMethodCallException(
				__METHOD__
				. ' You have to pass a Request object to the constructor to use this method'
			);
		}
		if (empty($etag)) {
			return;
		}
		$etag = '"' . $etag . '"';
		if (isset($this->request->server['HTTP_IF_NONE_MATCH']) &&
		    trim($this->request->server['HTTP_IF_NONE_MATCH']) === $etag) {
			$this->setStatus(self::STATUS_NOT_MODIFIED);
			return;
		}
		$this->addHeader('ETag', $etag);
	}

	/**
	* Checks and set Last-Modified header, when the request matches sends a
	* 'not modified' response
	* @param int|DateTime $lastModified time when the reponse was last modified
	*/
	public function setLastModifiedHeader($lastModified) {
		if(is_null($this->request)) {
			throw new \BadMethodCallException(
				__METHOD__
				. ' You have to pass a Request object to the constructor to use this method'
			);
		}
		if (empty($lastModified)) {
			return;
		}
		if (is_int($lastModified)) {
			$lastModified = gmdate(\DateTime::RFC2822, $lastModified);
		}
		if ($lastModified instanceof \DateTime) {
			$lastModified = $lastModified->format(\DateTime::RFC2822);
		}
		if (isset($this->request->server['HTTP_IF_MODIFIED_SINCE']) &&
		    trim($this->request->server['HTTP_IF_MODIFIED_SINCE']) === $lastModified) {
			$this->setStatus(self::STATUS_NOT_MODIFIED);
			return;
		}
		$this->addHeader('Last-Modified', $lastModified);
	}

	/**
	 * Get response status
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	* Set response status
	*
	* @param int $status a HTTP status code, see also the STATUS constants
	*/
	public function setStatus($status) {
		$protocol = (isset($this->request) && isset($this->request->server['SERVER_PROTOCOL']))
			? $this->request->server['SERVER_PROTOCOL']
			: 'HTTP/1.1';
		switch($status) {
			case self::STATUS_NOT_MODIFIED:
				$status = $status . ' Not Modified';
				break;
			case self::STATUS_TEMPORARY_REDIRECT:
				if ($protocol == 'HTTP/1.1') {
					$status = $status . ' Temporary Redirect';
					break;
				} else {
					$status = self::STATUS_FOUND;
					// fallthrough
				}
			case self::STATUS_FOUND;
				$status = $status . ' Found';
				break;
			case self::STATUS_NOT_FOUND;
				$status = $status . ' Not Found';
				break;
			case self::STATUS_FORBIDDEN;
				$status = $status . ' Forbidden';
				break;
			default:
				return;
		}
		$this->status = $protocol.' '.$status;
	}

}
