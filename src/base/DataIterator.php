<?php


namespace yii2\orm_dcache\base;


use yii\base\BaseObject;

/**
 * Class DataIterator
 * @package common\models\NewTicketCenter\base
 * 迭代器调用关系
 * 1.rewind
 * 2.valid
 * 3.current
 * 4.key
 * 5.next
 * 6.__destruct
 *
 * 1->2->3->4->5
 * 2->3->4->5
 * ......
 * 6
 */
class DataIterator extends BaseObject implements \Iterator {

    private $_batch = null;
    private $_value = null;
    private $_key = null;
    /**
     * @var ActiveQuery
     */
    public $query;

    public $batchSize = 100;

    private $lastVal = 0;

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current() {
        return $this->_value[$this->_key];
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next() {
        if ($this->_batch === null ||  next($this->_batch) === false) {
            $this->_batch = $this->fetchData();
            reset($this->_batch);
            $this->_value = $this->_batch;
            $this->_key = null;
        }

        $this->_key = $this->_key === null ? 0 : $this->_key + 1;
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key() {
        return $this->_key;
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid() {
        return !empty($this->_batch);
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind() {
        $this->reset();
        $this->next();
    }

    private function fetchData() {
        $data = $this->query->request($this->lastVal, $this->lastVal + $this->batchSize);
        $this->lastVal += count($data);
        return $data;
    }

    private function reset() {
        $this->_batch = $this->_value = $this->_key = null;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->reset();
    }
}