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

class DeltaCache extends Cache {


	/**
	 * Cache for a certain amount of seconds
	 * @param int $cacheSeconds time to cache the response in seconds
	 * @param DateTime $lastModified time when the reponse was last modified
	 * @param string $ETag token to use for modification check
	 */
	public function __construct($cacheSeconds, $ETag=null, 
	                            \DateTime $lastModified=null) {
		parent::__construct($ETag, $lastModified);		

		if($cacheSeconds > 0) {
			$this->addHeader('Cache-Control', 'max-age=' . $cacheSeconds . ', must-revalidate');
		} else {
			$this->addHeader('Cache-Control', 'must-revalidate');
		}
		
	}


}
