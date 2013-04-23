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


namespace OCA\AppFramework\Http\Cache;


/**
 * Baseclass for different caches
 */
abstract class Cache {

	private $headers = array();
	private $ETag;
	private $lastModified;

	/**
	 * @param DateTime $lastModified time when the reponse was last modified
	 * @param string $ETag token to use for modification check
	 */
	public function __construct($ETag=null, \DateTime $lastModified=null) {
		$this->ETag = $ETag;
		$this->lastModified = $lastModified;
		
		if(!is_null($ETag)) {
			$this->addHeader('ETag', '"' . $ETag . '"');
		}

		if(!is_null($lastModified)) {
			$this->lastModified = $lastModified->format(\DateTime::RFC2822);
			$this->addHeader('Last-Modified', $this->lastModified);
		}
	}


	/**
	 * @return array the headers array
	 */
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
	 * @return string the etag
	 */
	public function getETag() {
		return $this->ETag;
	}


	/**
	 * @return string RFC2822 formatted last modified date
	 */
	public function getLastModified() {
		return $this->lastModified;
	}


}