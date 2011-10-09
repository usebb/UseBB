<?php

namespace UseBB\System\Database;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * %Query building instance.
 * 
 * To build database queries in an object oriented manner.
 * 
 * This class wraps around Doctrine DBAL's query builder to integrate the
 * table prefix and more. See 
 * <a href="http://www.doctrine-project.org/docs/dbal/2.1/en/reference/query-builder.html">DBAL SQL Query Builder</a> 
 * for more info and documentation.
 * 
 * \li Methods implemented here take the same parameters as their originals.
 * \li Where Doctrine would return a QueryBuilder instance, the system returns
 * the current Query instance instead.
 * 
 * \attention You should not instantiate this class yourself, but use
 * Connection::newQuery().
 * 
 * \author Dietrich Moerman
 */
class Query {
	private $builder;
	private $prefix;
	
	/**
	 * Constructor.
	 * 
	 * \param $builder Doctrine %Query Builder instance
	 * \param $prefix Table prefix
	 */
	public function __construct(QueryBuilder $builder, 
		$prefix) {
		$this->builder = $builder;
		$this->prefix = $prefix;
	}
	
	/**
	 * Turns the query being built into a bulk delete query that ranges over
	 * a certain table.
	 * 
	 * \param $delete The table whose rows are subject to the deletion
	 * \param $alias The table alias used in the constructed query
	 * \returns This instance
	 */
	public function delete($delete = NULL, $alias = NULL) {
		if ($delete === NULL) {
			return $this;
		}
		
		$this->builder->delete($this->prefix . $delete, $alias);
		
		return $this;
	}
	
	/**
	 * Turns the query being built into a bulk update query that ranges over
	 * a certain table.
	 * 
	 * \param $update The table whose rows are subject to the update
	 * \param $alias The table alias used in the constructed query
	 * \returns This QueryBuilder instance
	 */
	public function update($update = NULL, $alias = NULL) {
		if ($update === NULL) {
			return $this;
		}
		
		$this->builder->update($this->prefix . $update, $alias);
		
		return $this;
	}
	
	/**
	 * Create and add a query root corresponding to the table identified by the
	 * given alias, forming a cartesian product with any existing query roots.
	 * 
	 * \param $from The table
	 * \param $alias The alias of the table
	 * \returns This instance
	 */
	public function from($from, $alias) {
		$this->builder->from($this->prefix . $from, $alias);
		
		return $this;
	}
	
	/**
	 * Creates and adds a join to the query.
	 * 
	 * \param $fromAlias The alias that points to a from clause
	 * \param $join The table name to join
	 * \param $alias The alias of the join table
	 * \param $condition The condition for the join
	 * \returns This instance
	 */
	public function join($fromAlias, $join, $alias, $condition = NULL) {
		$this->builder->join($fromAlias, $this->prefix . $join, 
			$alias, $condition);
		
		return $this;
	}
	
	/**
	 * Creates and adds a join to the query.
	 * 
	 * \param $fromAlias The alias that points to a from clause
	 * \param $join The table name to join
	 * \param $alias The alias of the join table
	 * \param $condition The condition for the join
	 * \returns This instance
	 */
	public function innerJoin($fromAlias, $join, $alias, $condition = NULL) {
		$this->builder->innerJoin($fromAlias, $this->prefix . $join, 
			$alias, $condition);
		
		return $this;
	}
	
	/**
	 * Creates and adds a left join to the query.
	 * 
	 * \param $fromAlias The alias that points to a from clause
	 * \param $join The table name to join
	 * \param $alias The alias of the join table
	 * \param $condition The condition for the join
	 * \returns This instance
	 */
	public function leftJoin($fromAlias, $join, $alias, $condition = NULL) {
		$this->builder->leftJoin($fromAlias, $this->prefix . $join, 
			$alias, $condition);
		
		return $this;
	}
	
	/**
	 * Creates and adds a right join to the query.
	 * 
	 * \param $fromAlias The alias that points to a from clause
	 * \param $join The table name to join
	 * \param $alias The alias of the join table
	 * \param $condition The condition for the join
	 * \returns This instance
	 */
	public function rightJoin($fromAlias, $join, $alias, $condition = NULL) {
		$this->builder->rightJoin($fromAlias, $this->prefix . $join, 
			$alias, $condition);
		
		return $this;
	}
	
	/**
	 * Intercept other method calls.
	 */
	public function __call($name, $args) {
		$result = call_user_func_array(array($this->builder, $name), $args);
		
		return $result instanceof QueryBuilder 
			? $this
			: $result;
	}
}
