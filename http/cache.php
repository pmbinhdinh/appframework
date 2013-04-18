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
class Cache {


	private $request;
	private $headers;

	public function __construct(Request $request) {
		$this->request = $request;
		$this->headers = array();
	}


	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * Adds a new header to the response that will be called before the render
	 * function
	 * @param string $name The name of the HTTP header
	 * @param string $value The value
	 */
	protected function addHeader($name, $value) {
		$this->headers[$name] = $value;
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
	public function expiresAt(\DateTime $expiresAt) {
		$this->addHeader('Expires', $expiresAt->format(\DateTime::RFC2822));
	}


	/**
	* Checks and set ETag header, when the request matches sends a
	* 'not modified' response
	* @param string $ETag token to use for modification check
	*/
	public function setETagHeader($ETag) {
		$ETag = '"' . $ETag . '"';
		if (isset($this->request->server['HTTP_IF_NONE_MATCH']) &&
		    trim($this->request->server['HTTP_IF_NONE_MATCH']) === $ETag) {
			$this->setStatus(Http::STATUS_NOT_MODIFIED);
			return;
		}
		$this->addHeader('ETag', $ETag);
	}


	/**
	* Checks and set Last-Modified header, when the request matches sends a
	* 'not modified' response
	* @param int|DateTime $lastModified time when the reponse was last modified
	*/
	public function setLastModifiedHeader(\DateTime $lastModified) {
		$lastModified->format(\DateTime::RFC2822)

		if (isset($this->request->server['HTTP_IF_MODIFIED_SINCE']) &&
		    trim($this->request->server['HTTP_IF_MODIFIED_SINCE']) === $lastModified) {
			$this->setStatus(Http::STATUS_NOT_MODIFIED);
			return;
		}
		$this->addHeader('Last-Modified', $lastModified);
	}


}
