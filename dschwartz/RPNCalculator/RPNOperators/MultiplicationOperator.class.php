<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 12/10/2016
 * Time: 9:55 AM
 */
namespace dschwartz\RPNCalculator\RPNOperators;

class MultiplicationOperator implements OperatorInterface
{
    private $_operandCount = 2;

    public function getOperandCount() {
        return $this->_operandCount;
    }

    public function operate($args) {
        return $args[0] * $args[1];
    }
}