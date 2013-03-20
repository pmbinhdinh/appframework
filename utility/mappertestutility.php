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


namespace OCA\AppFramework\Utility;

use OCA\AppFramework\Core\Api;


/**
 * Simple utility class for testing mappers
 */
abstract class MapperTestUtility extends \PHPUnit_Framework_TestCase {

	protected $api;

	/**
	 * Run this function before the actual test to either set or initialize the
	 * api. After this the api can be accessed by using $this->api
	 * @param \OCA\AppFramework\Core\API $api the api mock, if not set it will 
	 * initialized automatically with the basic methods
	 */
	protected function beforeEach($api=null){
		if($api === null){
			$this->api = $this->getMock('\OCA\AppFramework\Core\API', 
				array('prepareQuery'),
				array('a'));
		} else {
			$this->api = $api;
		}
	}


	/**
	 * Create mocks and set expected results for database queries
	 * @param string $sql the sql query that you expect to receive
	 * @param array $arguments the expected arguments for the prepare query
	 * method
	 * @param array $returnRows the rows that should be returned for the result
	 * of the database query
	 */
	protected function setMapperResult($sql, $arguments=array(), 
										$returnRows=array(),
										$returnSecondFetch=null){
		$pdoResult = $this->getMock('Result', 
			array('fetchRow'));

		if($returnSecondFetch === null){
			$pdoResult->expects($this->once())
				->method('fetchRow')
				->will($this->returnValue($returnRows));
		} else {
			$pdoResult->expects($this->at(0))
				->method('fetchRow')
				->will($this->returnValue($returnRows));
			$pdoResult->expects($this->at(1))
				->method('fetchRow')
				->will($this->returnValue($returnRows));
		}

		$query = $this->getMock('Query', 
			array('execute'));
		$query->expects($this->once())
			->method('execute')
			->with($this->equalTo($arguments))
			->will($this->returnValue($pdoResult));

		$this->api->expects($this->once())
			->method('prepareQuery')
			->with($this->equalTo($sql))
			->will(($this->returnValue($query)));

	}


}


