<?php

/*
 * This file is part of the phpstan-magento package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Magento\Framework\DB;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class for SQL SELECT generation and results.
 *
 * @api
 * @method \Magento\Framework\DB\Select from($name, $cols = '*', $schema = null)
 * @method \Magento\Framework\DB\Select join($name, $cond, $cols = '*', $schema = null)
 * @method \Magento\Framework\DB\Select joinInner($name, $cond, $cols = '*', $schema = null)
 * @method \Magento\Framework\DB\Select joinLeft($name, $cond, $cols = '*', $schema = null)
 * @method \Magento\Framework\DB\Select joinNatural($name, $cond, $cols = '*', $schema = null)
 * @method \Magento\Framework\DB\Select joinFull($name, $cond, $cols = '*', $schema = null)
 * @method \Magento\Framework\DB\Select joinRight($name, $cond, $cols = '*', $schema = null)
 * @method \Magento\Framework\DB\Select joinCross($name, $cols = '*', $schema = null)
 * @method \Magento\Framework\DB\Select orWhere($cond, $value = null, $type = null)
 * @method \Magento\Framework\DB\Select group($spec)
 * @method \Magento\Framework\DB\Select order($spec)
 * @method \Magento\Framework\DB\Select limitPage($page, $rowCount)
 * @method \Magento\Framework\DB\Select forUpdate($flag = true)
 * @method \Magento\Framework\DB\Select distinct($flag = true)
 * @method \Magento\Framework\DB\Select reset($part = null)
 * @method \Magento\Framework\DB\Select columns($cols = '*', $correlationName = null)
 * @since 100.0.2
 */
class Select extends \Zend_Db_Select
{
    /**
     * Condition type
     */
    const TYPE_CONDITION = 'TYPE_CONDITION';

    /**
     * Straight join key
     */
    const STRAIGHT_JOIN = 'straightjoin';

    /**
     * Sql straight join
     */
    const SQL_STRAIGHT_JOIN = 'STRAIGHT_JOIN';

    /**
     * Class constructor
     * Add straight join support
     *
     * @param Adapter\Pdo\Mysql $adapter
     * @param Select\SelectRenderer $selectRenderer
     * @param array $parts
     */
    public function __construct(
        \Magento\Framework\DB\Adapter\Pdo\Mysql $adapter,
        \Magento\Framework\DB\Select\SelectRenderer $selectRenderer,
        $parts = []
    ) {
    }

    /**
     * Adds a WHERE condition to the query by AND.
     *
     * If a value is passed as the second param, it will be quoted
     * and replaced into the condition wherever a question-mark
     * appears. Array values are quoted and comma-separated.
     *
     * <code>
     * // simplest but non-secure
     * $select->where("id = $id");
     *
     * // secure (ID is quoted but matched anyway)
     * $select->where('id = ?', $id);
     *
     * // alternatively, with named binding
     * $select->where('id = :id');
     * </code>
     *
     * Note that it is more correct to use named bindings in your
     * queries for values other than strings. When you use named
     * bindings, don't forget to pass the values when actually
     * making a query:
     *
     * <code>
     * $db->fetchAll($select, array('id' => 5));
     * </code>
     *
     * @param string $cond The WHERE condition.
     * @param string|int|array|null $value OPTIONAL A single value to quote into the condition.
     * @param string|int|null $type OPTIONAL The type of the given value
     * @return \Magento\Framework\DB\Select
     */
    public function where($cond, $value = null, $type = null)
    {
    }

    /**
     * Reset unused LEFT JOIN(s)
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function resetJoinLeft()
    {
    }

    /**
     * Validate LEFT joins, and remove it if not exists
     *
     * @return $this
     */
    protected function _resetJoinLeft()
    {
    }

    /**
     * Find table name in condition (where, column)
     *
     * @param string $table
     * @param string $cond
     * @return bool
     */
    protected function _findTableInCond($table, $cond)
    {
    }

    /**
     * Populate the {@link $_parts} 'join' key
     *
     * Does the dirty work of populating the join key.
     *
     * The $name and $cols parameters follow the same logic
     * as described in the from() method.
     *
     * @param  null|string $type Type of join; inner, left, and null are currently supported
     * @param  array|string|\Zend_Db_Expr $name Table name
     * @param  string $cond Join on this condition
     * @param  array|string $cols The columns to select from the joined table
     * @param  string $schema The database name to specify, if any.
     * @return \Magento\Framework\DB\Select This \Magento\Framework\DB\Select object
     * @throws \Zend_Db_Select_Exception
     */
    protected function _join($type, $name, $cond, $cols, $schema = null)
    {
    }

    /**
     * Sets a limit count and offset to the query.
     *
     * @param int $count OPTIONAL The number of rows to return.
     * @param int $offset OPTIONAL Start returning after this many rows.
     * @return $this
     */
    public function limit($count = null, $offset = null)
    {
    }

    /**
     * Cross Table Update From Current select
     *
     * @param string|array $table
     * @return string
     */
    public function crossUpdateFromSelect($table)
    {
    }

    /**
     * Insert to table from current select
     *
     * @param string $tableName
     * @param array $fields
     * @param bool $onDuplicate
     * @return string
     */
    public function insertFromSelect($tableName, $fields = [], $onDuplicate = true)
    {
    }

    /**
     * Generate INSERT IGNORE query to the table from current select
     *
     * @param string $tableName
     * @param array $fields
     * @return string
     */
    public function insertIgnoreFromSelect($tableName, $fields = [])
    {
    }

    /**
     * Retrieve DELETE query from select
     *
     * @param string $table The table name or alias
     * @return string
     */
    public function deleteFromSelect($table)
    {
    }

    /**
     * Modify (hack) part of the structured information for the current query
     *
     * @param string $part
     * @param mixed $value
     * @return $this
     * @throws \Zend_Db_Select_Exception
     */
    public function setPart($part, $value)
    {
    }

    /**
     * Use a STRAIGHT_JOIN for the SQL Select
     *
     * @param bool $flag Whether or not the SELECT use STRAIGHT_JOIN (default true).
     * @return $this
     */
    public function useStraightJoin($flag = true)
    {
    }

    /**
     * Render STRAIGHT_JOIN clause
     *
     * @param string   $sql SQL query
     * @return string
     */
    protected function _renderStraightjoin($sql)
    {
    }

    /**
     * Adds to the internal table-to-column mapping array.
     *
     * @param  string $correlationName The table/join the columns come from.
     * @param  array|string $cols The list of columns; preferably as an array,
     *     but possibly as a string containing one column.
     * @param  bool|string $afterCorrelationName True if it should be prepended,
     *     a correlation name if it should be inserted
     * @return void
     */
    protected function _tableCols($correlationName, $cols, $afterCorrelationName = null)
    {
    }

    /**
     * Adds the random order to query
     *
     * @param string $field     integer field name
     * @return $this
     */
    public function orderRand($field = null)
    {
    }

    /**
     * Render FOR UPDATE clause
     *
     * @param string   $sql SQL query
     * @return string
     */
    protected function _renderForupdate($sql)
    {
    }

    /**
     * Add EXISTS clause
     *
     * @param Select $select
     * @param string $joinCondition
     * @param bool $isExists
     * @return $this
     */
    public function exists($select, $joinCondition, $isExists = true)
    {
    }

    /**
     * Get adapter
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {
    }

    /**
     * Converts this object to an SQL SELECT string.
     *
     * @return string|null This object as a SELECT string. (or null if a string cannot be produced.)
     * @since 100.1.0
     */
    public function assemble()
    {
    }

    /**
     * Sleep magic method.
     *
     * @return string[]
     * @since 100.0.11
     */
    public function __sleep()
    {
    }

    /**
     * Init not serializable fields
     *
     * @return void
     * @since 100.0.11
     */
    public function __wakeup()
    {
    }
}
