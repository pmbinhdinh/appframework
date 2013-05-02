<?php

/**
* ownCloud - App Framework 
*
* @author Bernhard Posselt
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

namespace OCA\AppFramework\External;

class APIResult {

	const OK = 100;
	const SERVER_ERROR = 996;
	const UNAUTHORISED_ERROR = 997;
	const NOT_FOUND_ERROR = 998;
	const UNKNOWN_ERROR = 999;

	private $data;
	private $statusCode;
	private $message;

	public function __construct($data, $statusCode=APIResult::OK, $message=null) {
		$this->data = $data;
		$this->statusCode = $statusCode;
		$this->message = $message;
	}


	public function getData() {
		return $this->data;
	}


	public function getStatusCode() {
		return $this->statusCode;
	}


	public function getMessage() {
		return $this->message;
	}

}
