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


namespace OCA\AppFramework\Db;

use OCA\AppFramework\Core\API;
use OCA\AppFramework\Utility\MapperTestUtility;


require_once(__DIR__ . "/../classloader.php");


class MapperTestEntity extends Entity {
	public $name;
	public $email;
};


class ExampleMapper extends Mapper {
	public function __construct(API $api){ parent::__construct($api, 'table'); }
	public function find($table, $id){ return $this->findQuery($table, $id); }
	public function findAll($table){ return $this->findAllQuery($table); }
	public function pDeleteQuery($table, $id){ $this->deleteQuery($table, $id); }
}


class MapperTest extends MapperTestUtility {

	private $mapper;

	public function setUp(){
		$this->beforeEach();
		$this->mapper = new ExampleMapper($this->api);
	}



	private function find($doesNotExist=false){
		$sql = 'SELECT * FROM `hihi` WHERE `id` = ?';
		$params = array(1);

		$cursor = $this->getMock('cursor', array('fetchRow'));
		$cursor->expects($this->at(0))
				->method('fetchRow')
				->will($this->returnValue(!$doesNotExist));

		if($doesNotExist){
			$this->setExpectedException('\OCA\AppFramework\Db\DoesNotExistException');
		} else {
			$cursor->expects($this->at(1))
				->method('fetchRow')
				->will($this->returnValue(false));
		}

		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
				->method('execute')
				->with($this->equalTo($params))
				->will($this->returnValue($cursor));

		$this->api->expects($this->once())
				->method('prepareQuery')
				->with($this->equalTo($sql))
				->will($this->returnValue($query));


		$result = $this->mapper->find('hihi', $params[0]);

		if($doesNotExist){
			$this->assertFalse($result);
		} else {
			$this->assertTrue($result);
		}

	}


	public function testFindThrowsExceptionWhenMoreThanOneResult(){
		$sql = 'SELECT * FROM `hihi` WHERE `id` = ?';
		$params = array(1);

		$cursor = $this->getMock('cursor', array('fetchRow'));
		$cursor->expects($this->at(0))
				->method('fetchRow')
				->will($this->returnValue(true));
		$cursor->expects($this->at(1))
				->method('fetchRow')
				->will($this->returnValue(true));

		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
				->method('execute')
				->with($this->equalTo($params))
				->will($this->returnValue($cursor));

		$this->api->expects($this->once())
				->method('prepareQuery')
				->with($this->equalTo($sql))
				->will($this->returnValue($query));

		$this->setExpectedException('\OCA\AppFramework\Db\MultipleObjectsReturnedException');

		$result = $this->mapper->find('hihi', $params[0]);

	}


	public function testFind(){
		$this->find();
	}


	public function testFindDoesNotExist(){
		$this->find(true);
	}


	private function query($method, $sql, $params=array()){

		$query = $this->getMock('query', array('execute'));

		$query->expects($this->once())
				->method('execute')
				->with($this->equalTo($params));

		$this->api->expects($this->once())
				->method('prepareQuery')
				->with($this->equalTo($sql))
				->will($this->returnValue($query));

		if(count($params) > 0){
			$this->mapper->$method('hihi', $params[0]);
		} else {
			$this->mapper->$method('hihi');
		}
	}


	public function testFindAll(){
		$this->query('findAll', 'SELECT * FROM `hihi`');
	}


	public function testDeleteQuery(){
		$this->query('pDeleteQuery', 'DELETE FROM `hihi` WHERE `id` = ?', array(1));
	}

	public function testMapperShouldSetTableName(){
		$this->assertEquals('*dbprefix*table', $this->mapper->getTableName());
	}


	public function testDelete(){
		$sql = 'DELETE FROM `*dbprefix*table` WHERE `id` = ?';
		$params = array(2);

		$this->setMapperResult($sql, $params);
		$entity = new MapperTestEntity();
		$entity->setId($params[0]);

		$this->mapper->delete($entity);
	}


}