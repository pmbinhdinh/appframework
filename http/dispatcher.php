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

use \OCA\AppFramework\Controller\Controller;
use \OCA\AppFramework\Middleware\MiddlewareDispatcher;


/**
 * Class to dispatch the request to the middleware disptacher
 */
class Dispatcher {

	private $middlewareDispatcher;
	private $protocol;

	public function __construct(Http $protocol,
	                            MiddlewareDispatcher $middlewareDispatcher) {
		$this->protocol = $protocol;
		$this->middlewareDispatcher = $middlewareDispatcher;
	}


	/**
	 * Handles a request and calls the dispatcher on the controller
	 */
	public function dispatch(Controller $controller, $methodName) {
		$return = array(null, array(), null);

		// create response and run middleware that receives the response
		// if an exception appears, the middleware is checked to handle the
		// exception and to create a response. If no response is created, it is
		// assumed that theres no middleware who can handle it and the error is 
		// thrown again
		try {

			$this->middlewareDispatcher->beforeController($controller, 
				$methodName);
			$response = $controller->$methodName();

		} catch(\Exception $exception){

			$response = $this->middlewareDispatcher->afterException(
				$controller, $methodName, $exception);

			if($response === null){
				throw $exception;
			}
		}

		$response = $this->middlewareDispatcher->afterController(
			$controller, $methodName, $response);

		// get the output which should be printed and run the after output
		// middleware to modify the response
		$output = $response->render();
		$return[2] = $this->middlewareDispatcher->beforeOutput(
			$controller, $methodName, $output);

		$return[0] = $this->protocol->getHeader($response->getStatus(), 
			$response->getCache());
		$return[1] = $response->getHeaders();

		return $return;
	}


}