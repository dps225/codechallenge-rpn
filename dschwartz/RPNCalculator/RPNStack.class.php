<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 12/9/2016
 * Time: 3:26 PM
 */

namespace dschwartz\RPNCalculator;


use dschwartz\RPNCalculator\RPNExceptions\StackEmptyException;

class RPNStack
{
    /**
     * @var array
     */
    protected $stack;

    /**
     * RPNStack constructor.
     */
    public function __construct() {}

    /**
     * Initialize stack to an empty array
     */
    public function init() {
        $this->stack = [];
    }

    public function isEmpty() {
        return count($this->stack) === 0;
    }

    /**
     * Push a new value on to the stack
     *
     * @param mixed $val Value to push to stack
     */
    public function push($val) {
        array_push($this->stack, $val);
    }

    /**
     * Pop a value off the stack
     *
     * @return mixed
     * @throws StackEmptyException
     */
    public function pop() {
        if (count($this->stack) === 0) {
            throw new StackEmptyException('Stack is empty');
        }
        return array_pop($this->stack);
    }

    /**
     * Get a string representation of the stack
     * @return string
     */
    public function __toString() {
        return implode("\n", $this->stack);
    }

    /**
     * Get an array representation of the stack
     * @return array
     */
    public function toArray() {
        return $this->stack;
    }
}