<?php

/**
 * ownCloud - App Framework
 *
 * @author Morris Jobke
 * @copyright 2012 Morris Jobke morris.jobke@gmail.com
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


namespace OCA\AppFramework\Middleware\Http;


require_once(__DIR__ . "/../../classloader.php");


class HttpMiddlewareTest extends \PHPUnit_Framework_TestCase {

	private function getAPI(){
		return $this->getMock(
			'OCA\AppFramework\Core\API',
			array('login'),
			array('test')
		);
	}

	private function getRequest(){
		return $this->getMock(
			'OCA\AppFramework\Http\Request',
			array()
		);
	}

	private function checkLogin(array $logindata, $isLoginCalled=false) {
		$api = $this->getAPI();
		$request = $this->getRequest();

		if($isLoginCalled)
			$api->expects($this->once())->method('login');
		else
			$api->expects($this->never())->method('login');

		$request->expects($this->any())
				->method('__get')
				->with($this->equalTo('server'))
				->will($this->returnValue($logindata));

		$middleware = new HttpMiddleware($api, $request);
		$middleware->beforeController('\OCA\AppFramework\Middleware\Http\HttpMiddlewareTest', 'testLogin');
	}

	public function testLogin(){
		$this->checkLogin(array(
			'PHP_AUTH_USER' => 'user',
			'PHP_AUTH_PW' => 'pw'
		), true);
	}

	public function testNoLogin(){
		$this->checkLogin(array(
			'PHP_AUTH_USER' => 'user'
		));
		$this->checkLogin(array(
			'PHP_AUTH_PW' => 'pw'
		));
		$this->checkLogin(array());
	}
}
