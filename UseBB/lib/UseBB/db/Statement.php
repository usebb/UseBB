<?php

/*
	This file is part of UseBB.

	UseBB is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	UseBB is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with UseBB.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Database statement.
 *
 * This class implements SPL's Iterator in order to use the statement in foreach.
 *
 * @package UseBB
 * @subpackage db
 * @copyright Copyright (C) 2003-2008 Contributors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version $Rev$
 *
 * @author Dietrich Moerman <dietrich@usebb.net>
 */
abstract class UseBB_Statement implements Iterator
{
	protected $PDO;
	protected $statement;
	
	private $next;
	private $key;
	private $columns;

	/**
	 * Class constructor.
	 *
	 * @param PDO $PDO PDO instance
	 * @param PDOStatement $statement Statement instance
	 */
	public function __construct(PDO $PDO, PDOStatement $statement)
	{
		$this->PDO = $PDO;
		$this->statement = $statement;
		$this->columns = $statement->columnCount();
	}
	
	/**
	 * Implementation of Iterator::current().
	 *
	 * @returns mixed Current row
	 */
	public function current()
	{
		return $this->next;
	}
	
	/**
	 * Implementation of Iterator::next().
	 */
	public function next()
	{
		// If there's only one column, fetch it instead of an array.
		$this->next = $this->columns === 1 ? $this->statement->fetchColumn() : $this->statement->fetch(PDO::FETCH_ASSOC);
	}
	
	/**
	 * Implementation of Iterator::key().
	 *
	 * @returns int Key
	 */
	public function key()
	{
		return $this->key++;
	}
	
	/**
	 * Implementation of Iterator::valid().
	 *
	 * @returns bool Is valid
	 */
	public function valid()
	{
		return $this->next !== FALSE;
	}
	
	/**
	 * Implementation of Iterator::rewind().
	 */
	public function rewind()
	{
		$this->next();
	}
}
