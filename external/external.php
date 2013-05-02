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

class External {


	/**
	 * Simple main function for API calls
         * @param string $controllerName the name of the API Controller
         * @param string $methodName the method to call on the API Controller
         * @param array $urlParams the url parameters
         * @param \Pimple $container the dependency injection container
         * @return \OC_OCS_Result the result for the api
	 */
	public static function main($controllerName, $methodName, array $urlParams=array(),
	                            \Pimple $container) {
		$container['urlParams'] = $urlParams;
		$response = $container[$controllerName]->$methodName();
		return new \OC_OCS_Result(
			$response->getData(), 
			$response->getStatusCode(),
			$response->getMessage());
	}


}
