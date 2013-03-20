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


/**
 * Simple parent class for inheriting your data access layer from. This class
 * may be subject to change in the future
 */
abstract class Mapper {

	private $tableName;

	/**
	 * @param API $api Instance of the API abstraction layer
	 * @param string $tableName the name of the table. set this to allow entity 
	 * queries without using sql
	 */
	public function __construct(API $api, $tableName=null){
		$this->api = $api;
		$this->tableName = '*dbprefix*' . $tableName;
	}


	/**
	 * @return string the table name
	 */
	public function getTableName(){
		return $this->tableName;
	}


	/**
	 * Deletes an entity from the table
	 * @param Entity $enttiy the entity that should be deleted
	 */
	public function delete(Entity $entity){
		$this->deleteQuery($this->tableName, $entity->getId());
	}


	/**
	 * Creates a new entry in the db from an entity
	 * @param Entity $enttiy the entity that should be created
	 * @return the saved entity with the set id
	 */
	public function insert(Entity $entity){
		// get updated fields to save, fields have to be set using a setter to
		// be saved
		$properties = $entity->getUpdatedFields();
		$values = '';
		$columns = '';
		$params = array();

		// build the fields
		$i = 0;
		foreach($properties as $property => $updated) {
			$column = $entity->propertyToColumn($property);
			$getter = 'get' . ucfirst($property);
			
			$columns .= '`' . $column . '`';
			$values .= '?';

			// only append column if there are more entries
			if($i < count($properties)-1){
				$columns .= ',';
				$values .= ',';
			}

			array_push($params, $entity->$getter());
			$i++;
		}

		$sql = 'INSERT INTO `' . $this->tableName . '`(' .
				$columns . ') VALUES(' . $values . ')';
		
		$this->execute($sql, $params);
		//$entity->setId($api->getInsertId($this->tableName));
		return $entity;
	}


	/**
	 * Returns an db result by id
	 * @param string $tableName the name of the table to query
	 * @param int $id the id of the item
	 * @throws DoesNotExistException if the item does not exist
	 * @throws MultipleObjectsReturnedException if more than one item exist
	 * @return array the result as row
	 */
	protected function findQuery($tableName, $id){
		$sql = 'SELECT * FROM `' . $tableName . '` WHERE `id` = ?';
		$params = array($id);

		$result = $this->execute($sql, $params);
		$row = $result->fetchRow();

		if($row === false){
			throw new DoesNotExistException('Item with id ' . $id . ' does not exist!');
		} elseif($result->fetchRow() !== false) {
			throw new MultipleObjectsReturnedException('More than one result for Item with id ' . $id . '!');
		} else {
			return $row;
		}
	}


	/**
	 * Returns all entries of a table
	 * @param string $tableName the name of the table to query
	 * @return \PDOStatement the result
	 */
	protected function findAllQuery($tableName){
		$sql = 'SELECT * FROM `' . $tableName . '`';
		return $this->execute($sql);
	}


	/**
	 * Deletes a row in a table by id
	 * @param string $tableName the name of the table to query
	 * @param int $id the id of the item
	 */
	protected function deleteQuery($tableName, $id){
		$sql = 'DELETE FROM `' . $tableName . '` WHERE `id` = ?';
		$params = array($id);
		$this->execute($sql, $params);
	}


	/**
	 * Runs an sql query
	 * @param string $sql the prepare string
	 * @param array $params the params which should replace the ? in the sql query
	 * @param int $limit the maximum number of rows
	 * @param int $offset from which row we want to start
	 * @return \PDOStatement the database query result
	 */
	protected function execute($sql, array $params=array(), $limit=null, $offset=null){
		$query = $this->api->prepareQuery($sql);
		return $query->execute($params);
	}

}