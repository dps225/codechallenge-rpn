<?php

namespace dschwartz\RPNCalculator\Testable;

use dschwartz\RPNCalculator\RPNStack;

class RPNStackTestable extends RPNStack
{
    public function setStack($stack) {
        $this->stack = $stack;
    }
}