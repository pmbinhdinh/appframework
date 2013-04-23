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


namespace OCA\AppFramework\Http\Cache;


require_once(__DIR__ . "/../../classloader.php");



class DeltaCacheTest extends \PHPUnit_Framework_TestCase {

	private $cache;
	private $time;
	private $etag;
	private $seconds;

	protected function setUp(){
		$this->time = new \DateTime(null, new \DateTimeZone('GMT'));
		$this->time->setTimestamp(0);
		$this->etag = 'hi';
		$this->seconds = 33;

		$this->cache = new DeltaCache($this->seconds, 
			$this->etag, $this->time);
	}


	public function testCacheSecondsZero() {
		$this->cache = new DeltaCache(0, 
			$this->etag, $this->time);
		
		$headers = $this->cache->getHeaders();
		$this->assertEquals('must-revalidate', $headers['Cache-Control']);	
	}


	public function testCacheSeconds() {
		
		$headers = $this->cache->getHeaders();
		$this->assertEquals('max-age=33, must-revalidate', 
			$headers['Cache-Control']);	
	}

	
}