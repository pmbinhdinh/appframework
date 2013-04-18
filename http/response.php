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

	/**
	 * @var array
	 */
	private $headers = array();

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var Request;
	 */
	protected $request;


	/**
	 * @var Http
	 */
	protected $protocol;

	public function __construct(Http $protocol) {
		$this->protocol;
	}


	public function setCachePolicy(Cache $cache) {
		foreach($cache->getHeaders as $key => $value) {
			$this->addHeader($key, $value);
		}
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
	 * Returns the set headers
	 * @return array the headers
	 */
	public function getHeaders() {
		return $this->headers;
	}


	/**
	 * By default renders no output
	 * @return null
	 */
	public function render() {
		return null;
	}


	/**
	* Set response status
	*
	* @param int $status a HTTP status code, see also the STATUS constants
	*/
	public function setStatus($status) {
		$this->status = $protocol->getHttpStatusHeader($status);
	}


	/**
	 * Get response status
	 */
	public function getStatus() {
		return $this->status;
	}

}
