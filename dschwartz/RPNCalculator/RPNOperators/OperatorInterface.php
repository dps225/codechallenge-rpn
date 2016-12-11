<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 12/10/2016
 * Time: 10:22 AM
 */
namespace dschwartz\RPNCalculator\RPNOperators;

interface OperatorInterface
{
    public function getOperandCount();
    public function operate($args);
}