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
 * Used to cache for a certain time
 */
class TimespanCachePolicy extends TimeCachePolicy {
		
	/**
	 * Enable response caching by sending correct HTTP headers
	 *
	 * @param int $cacheTime time to cache the response in seconds
	 */
	public function __construct($deltaSeconds) {

		$this->addHeader('Pragma', 'public');

		if($deltaSeconds > 0) {
			$this->addHeader('Cache-Control', 'max-age=' . $deltaSeconds . ', must-revalidate');
		} else {
			$this->addHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
		}

	}


}