<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace src\thrift\base;


interface TActiveQueryInterface
{
    public function all($service = null);

    public function one($service = null);

    public function count($q = '*', $service = null);

    public function exists($service = null);

    public function where(array $condition);

    public function andWhere(array $condition);

    public function orWhere(array $condition);

    public function filterWhere(array $condition);

    public function andFilterWhere(array $condition);

    public function orFilterWhere(array $condition);

    public function orderBy(array $columns);

    public function addOrderBy(array $columns);

    public function limit($limit);

    public function offset($offset);

    /**
     * Sets the [[asArray]] property.
     * @param bool $value whether to return the query results in terms of arrays instead of Active Records.
     * @return $this the query object itself
     */
    public function asArray($value = true);

    /**
     * Sets the [[indexBy]] property.
     * @param string|callable $column the name of the column by which the query results should be indexed by.
     * This can also be a callable (e.g. anonymous function) that returns the index value based on the given
     * row or model data. The signature of the callable should be:
     *
     * ```php
     * // $model is an AR instance when `asArray` is false,
     * // or an array of column values when `asArray` is true.
     * function ($model)
     * {
     *     // return the index value corresponding to $model
     * }
     * ```
     *
     * @return $this the query object itself
     */
    public function indexBy($column);

}
