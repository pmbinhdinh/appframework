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

namespace OCA\AppFramework;

class Autoloader {


	/**
	 * Use this method to register the autoloader of the App Framework for your
	 * class
	 */
	public static function register() {
		ini_set('unserialize_callback_func', 'spl_autoload_call');
		spl_autoload_register(array(new self, 'autoload'));
	}


	public static function autoload($className) {
		if (strpos($className, 'OCA\\AppFramework\\') === 0) {

			$classPath = strtolower(
				str_replace('\\', '/', substr($className, 16)
			) . '.php');

			$fullPath = __DIR__ . $classPath;
			if(file_exists($fullPath)){
				require_once $fullPath;
			}
		}
	}


}