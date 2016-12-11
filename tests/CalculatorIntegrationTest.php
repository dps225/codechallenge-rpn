<?php

namespace tests;

use dschwartz\RPNCalculator\RPNCalculator;
use dschwartz\RPNCalculator\RPNExceptions\StackEmptyException;
use dschwartz\RPNCalculator\RPNExceptions\TooManyOperandsException;
use dschwartz\RPNCalculator\RPNStack;
use PHPUnit\Framework\TestCase;

class CalculatorIntegrationTest extends TestCase
{
    /**
     * @var RPNStack
     */
    protected $stack;

    /**
     * Always use our custom RPNStack implementation for the stack in these integration tests
     */
    public function setUp() {
        $this->stack = new RPNStack();
    }

    /**
     * Provide some addition calculations with expected results
     * @return array
     */
    public function provideAddition() {
        return [
            [5, 10, 15],
            [12, 22, 34],
            [75, 75, 150],
        ];
    }

    /**
     * @param $op1
     * @param $op2
     * @param $expectedResult
     * @dataProvider provideAddition
     */
    public function testAddition($op1, $op2, $expectedResult) {
        $calc = new RPNCalculator($this->stack);
        $calc->processInput($op1);
        $calc->processInput($op2);
        $calc->processInput('+');

        $result = $calc->processResult();
        $this->assertEquals($expectedResult, $result, "Expected sum of {$expectedResult} when adding {$op1} and {$op2}");
    }

    /**
     * Provide some subtraction calculations with expected results
     * @return array
     */
    public function provideSubtraction() {
        return [
            [10, 5, 5],
            [27, 19, 8],
            [12, 20, -8],
        ];
    }

    /**
     * @param $op1
     * @param $op2
     * @param $expectedResult
     * @dataProvider provideSubtraction
     */
    public function testSubtraction($op1, $op2, $expectedResult) {
        $calc = new RPNCalculator($this->stack);
        $calc->processInput($op1);
        $calc->processInput($op2);
        $calc->processInput('-');

        $result = $calc->processResult();
        $this->assertEquals($expectedResult, $result, "Expected difference of {$expectedResult} when subtracting {$op2} from {$op1}");
    }

    /**
     * Provide some multiplication calculations with expected results
     * @return array
     */
    public function provideMultiplication() {
        return [
            [6, 4, 24],
            [9, 10, 90],
            [7, 0.5, 3.5],
        ];
    }

    /**
     * @param $op1
     * @param $op2
     * @param $expectedResult
     * @dataProvider provideMultiplication
     */
    public function testMultiplication($op1, $op2, $expectedResult) {
        $calc = new RPNCalculator($this->stack);
        $calc->processInput($op1);
        $calc->processInput($op2);
        $calc->processInput('*');

        $result = $calc->processResult();
        $this->assertEquals($expectedResult, $result, "Expected product of {$expectedResult} when multiplying {$op1} and {$op2}");
    }

    /**
     * Provide some division calculations with expected results
     * @return array
     */
    public function provideDivision() {
        return [
            [21, 7, 3],
            [56, 8, 7],
            [20, 0.5, 40],
        ];
    }

    /**
     * @param $op1
     * @param $op2
     * @param $expectedResult
     * @dataProvider provideDivision
     */
    public function testDivision($op1, $op2, $expectedResult) {
        $calc = new RPNCalculator($this->stack);
        $calc->processInput($op1);
        $calc->processInput($op2);
        $calc->processInput('/');

        $result = $calc->processResult();
        $this->assertEquals($expectedResult, $result, "Expected result of {$expectedResult} when dividing {$op1} by {$op2}");
    }

    /**
     * Ensure that an exception occurs when an operator is requested with too few operands on the stack
     */
    public function testProcessInputThrowsExceptionOnInsufficientInput() {
        $calc = new RPNCalculator($this->stack);
        $calc->processInput(1);

        $this->expectException(StackEmptyException::class);
        $calc->processInput('+');
    }

    /**
     * Ensure an exception occurs when input is exhausted by there are still operands to process
     */
    public function testProcessResultThrowsExceptionOnTooMuchInput() {
        $calc = new RPNCalculator($this->stack);
        $calc->processInput(1);
        $calc->processInput(2);
        $calc->processInput(3);
        $calc->processInput('+');

        $this->expectException(TooManyOperandsException::class);
        $calc->processResult();
    }

    /**
     * Provide some full RPN sentences to process with expected results
     * @return array
     */
    public function provideFullStrings() {
        return [
            ["9 6 +", 15],
            ["22 15 -", 7],
            ["12 5 *", 60],
            ["19 4 /", 4.75],
            ["2 5 4 + *", 18],
            ["5 1 2 + 4 * + 3 -", 14],
        ];
    }

    /**
     * @param $inputString
     * @param $expectedResult
     * @dataProvider provideFullStrings
     */
    public function testProcessStringCalculatesAllInput($inputString, $expectedResult) {
        $calc = new RPNCalculator($this->stack);
        $result = $calc->processString($inputString);

        $this->assertEquals($expectedResult, $result, "Expected result of {$expectedResult} for {$inputString}");
    }
}