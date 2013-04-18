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
 * Class for Http Constants
 */

class HttpFactory {

	public function get($server)
		if($server['SERVER_PROTOCOL'] === 'HTTP/1.0') {
			return new Http10($server);
		} else {
			return new Http11($server);
		}
	}

}

abstract class Http {
	const STATUS_FOUND = 304;
	const STATUS_NOT_MODIFIED = 304;
	const STATUS_TEMPORARY_REDIRECT = 307;
	const STATUS_FORBIDDEN = 403;
	const STATUS_NOT_FOUND = 404;

	private $server;

	protected $headers = array(
		self::STATUS_FOUND => self::STATUS_FOUND . ' Found',
		self::STATUS_NOT_MODIFIED => self::STATUS_NOT_MODIFIED . 'Not Modified',
		self::STATUS_TEMPORARY_REDIRECT => $headers[self::STATUS_FOUND],
		self::STATUS_FORBIDDEN => STATUS_FORBIDDEN . ' Not Found',
		self::STATUS_NOT_FOUND => STATUS_NOT_FOUND . ' Forbidden',
	)

	public function __construct($server) {
		$this->server = $server;
	}

	public function getHttpStatusHeader($status, $ETag, $lastModified) {
		if ((isset($this->server['HTTP_IF_NONE_MATCH']) &&
		    trim($this->server['HTTP_IF_NONE_MATCH']) === $ETag) 
		    ||
			(isset($this->server['HTTP_IF_MODIFIED_SINCE']) &&
		    trim($this->server['HTTP_IF_MODIFIED_SINCE']) === $lastModified)) {

			return $this->headers[Http::STATUS_NOT_MODIFIED];
		}
		
		return $this->headers[$status];
	}
}


/**
 * Http 1.0 protocol class
 */
class Http10 extends Http { 

	public function __construct($server) {
		parent::__construct($server);
	}
}

/**
 * Http 1.1 protocol class
 */
class Http11 extends Http {

	public function __construct() {
		parent::__construct($server);
		$this->headers[self::STATUS_TEMPORARY_REDIRECT] =
			self::STATUS_TEMPORARY_REDIRECT . ' Temporary Redirect';
	}

}