<?php

/**
* ownCloud - News
*
* @author Alessandro Cosentino
* @author Bernhard Posselt
* @copyright 2012 Alessandro Cosentino cosenal@gmail.com
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

require_once(__DIR__ . "/../classloader.php");


class APIResultTest extends \PHPUnit_Framework_TestCase {


	public function testGetStatusCode() {
		$result = new APIResult(null, APIResult::SERVER_ERROR);
		$this->assertEquals(996, $result->getStatusCode());
	}

	public function testGetData() {
		$result = new APIResult('hi');
		$this->assertEquals('hi', $result->getData());
		$this->assertEquals(100, $result->getStatusCode());
	}


	public function testGetMessage() {
		$result = new APIResult(null, null, 'heho');
		$this->assertEquals('heho', $result->getMessage());
	}


	public function testUnauthorized() {
		$result = new APIResult(null, APIResult::UNAUTHORISED_ERROR);
		$this->assertEquals(997, $result->getStatusCode());	
	}


	public function testNotFound() {
		$result = new APIResult(null, APIResult::NOT_FOUND_ERROR);
		$this->assertEquals(998, $result->getStatusCode());	
	}


	public function testUnknown() {
		$result = new APIResult(null, APIResult::UNKNOWN_ERROR);
		$this->assertEquals(999, $result->getStatusCode());	
	}	
}
