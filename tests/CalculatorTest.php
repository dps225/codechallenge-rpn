<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 12/9/2016
 * Time: 11:13 AM
 */
namespace tests;

use dschwartz\RPNCalculator\RPNExceptions\StackEmptyException;
use dschwartz\RPNCalculator\RPNExceptions\UnknownOperatorException;
use dschwartz\RPNCalculator\RPNExceptions\TooManyOperandsException;
use dschwartz\RPNCalculator\RPNStack;
use dschwartz\RPNCalculator\Testable\OperatorTestable;
use PHPUnit\Framework\TestCase;
use dschwartz\RPNCalculator\RPNCalculator;

class CalculatorTest extends TestCase
{
    /**
     * @var RPNStack
     */
    protected $stack;

    /**
     * Prepare a mock class of the stack for use in all tests
     */
    public function setUp() {
        $this->stack = $this->getStackMock();
    }

    public function testStackIsEmptyOnInit()
    {
        $this->stack->expects($this->once())
            ->method('init');

        $calc = new RPNCalculator($this->stack);
    }

    public function testProcessInputPushesNumberOnStack() {
        $number = 6;

        $this->stack->expects($this->once())
            ->method('push')
            ->with($number);

        $calc = $this->getCalculatorMock();
        $calc->processInput($number);
    }

    /**
     * Provide some sample operators with a number of desired operands
     * @return array
     */
    public function provideOperands() {
        return [
            [1, '!'],
            [2, '+'],
            [2, '*'],
        ];
    }

    /**
     * @param int $operandCount The number of operands required for the given operator
     * @param string $op The token identifying an operator
     * @dataProvider provideOperands
     */
    public function testProcessInputPopsCorrectNumberOfValuesOffStackForOperator($operandCount, $op) {
        $stackValues = [5, 10, 15];
        $popCount = 0;

        $operator = $this->getOperatorMock();
        $operator->expects($this->once())
            ->method('getOperandCount')
            ->will($this->returnValue($operandCount));

        $mockPop = function() use ($stackValues, &$popCount) {
            return $stackValues[$popCount++];
        };
        $this->stack->expects($this->any())
            ->method('pop')
            ->will($this->returnCallback($mockPop));

        $calc = $this->getCalculatorMock(['getOperator']);
        $calc->expects($this->once())
            ->method('getOperator')
            ->will($this->returnValue($operator));

        $calc->processInput($op);

        $this->assertEquals($operandCount, $popCount, "Expected {$operandCount} operands popped off the stack");
    }

    /**
     * @param int $operandCount The number of operands required for the given operator
     * @param string $op The token identifying an operator
     * @dataProvider provideOperands
     */
    public function testProcessInputPassesTopOperandsToOperate($operandCount, $op) {
        $stackValues = [5, 10, 15];
        $popCount = 0;

        $expectedOperands = [];
        for ($i = 0; $i < $operandCount; $i++) {
            $expectedOperands[] = $stackValues[$i];
        }

        $operator = $this->getOperatorMock();
        $operator->expects($this->any())
            ->method('getOperandCount')
            ->will($this->returnValue($operandCount));
        $operator->expects($this->once())
            ->method('operate')
            ->with($expectedOperands);

        $mockPop = function() use ($stackValues, &$popCount) {
            return $stackValues[$popCount++];
        };
        $this->stack->expects($this->any())
            ->method('pop')
            ->will($this->returnCallback($mockPop));

        $calc = $this->getCalculatorMock(['getOperator']);
        $calc->expects($this->once())
            ->method('getOperator')
            ->will($this->returnValue($operator));

        $calc->processInput($op);
    }

    public function testProcessInputPushesResultBackOnToStack() {
        $result = 100;

        $operator = $this->getOperatorMock();
        $operator->expects($this->any())
            ->method('operate')
            ->will($this->returnValue($result));

        $this->stack->expects($this->once())
            ->method('push')
            ->with($result);

        $calc = $this->getCalculatorMock(['getOperator']);
        $calc->expects($this->once())
            ->method('getOperator')
            ->will($this->returnValue($operator));

        $calc->processInput('someOp');
    }

    public function testProcessInputThrowsExceptionForUnknownOperator() {
        $calc = $this->getCalculatorMock(['getOperator']);
        $calc->expects($this->once())
            ->method('getOperator')
            ->will($this->throwException(new UnknownOperatorException()));

        $this->expectException(UnknownOperatorException::class);
        $calc->processInput('badOp');
    }

    public function testProcessInputThrowsExceptionWhenNotEnoughOperands() {
        $operator = $this->getOperatorMock();
        $operator->expects($this->any())
            ->method('getOperandCount')
            ->will($this->returnValue(1));

        $this->stack->expects($this->any())
            ->method('pop')
            ->will($this->throwException(new StackEmptyException()));

        $calc = $this->getCalculatorMock(['getOperator']);

        $calc->expects($this->once())
            ->method('getOperator')
            ->will($this->returnValue($operator));

        $this->expectException(StackEmptyException::class);
        $calc->processInput('someOp');
    }

    public function testProcessResultReturnsLastItemInStack() {
        $expectedResult = 15;

        $this->stack->expects($this->once())
            ->method('pop')
            ->will($this->returnValue($expectedResult));
        $this->stack->expects($this->any())
            ->method('isEmpty')
            ->will($this->returnValue(true));

        $calc = $this->getCalculatorMock();
        $actualResult = $calc->processResult();

        $this->assertEquals($expectedResult, $actualResult, "Expected result is the last item in stack");
    }

    public function testProcessResultThrowsExceptionIfStackNotEmpty() {
        $expectedResult = 25;

        $this->stack->expects($this->once())
            ->method('pop')
            ->will($this->returnValue($expectedResult));
        $this->stack->expects($this->any())
            ->method('isEmpty')
            ->will($this->returnValue(false));

        $calc = $this->getCalculatorMock();

        $this->expectException(TooManyOperandsException::class);
        $actualResult = $calc->processResult();
    }

    public function testProcessResultThrowsExceptionIfNoResultOnStack() {
        $this->stack->expects($this->once())
            ->method('pop')
            ->will($this->throwException(new StackEmptyException()));

        $calc = $this->getCalculatorMock();

        $this->expectException(StackEmptyException::class);
        $actualResult = $calc->processResult();
    }

    public function testProcessStringCallsProcessInputOnEachToken() {
        $inputString = "7 8 + 12 -";
        $numTokens = count(explode(" ", $inputString));

        $calc = $this->getCalculatorMock(['processInput', 'processResult']);

        $calc->expects($this->exactly($numTokens))
            ->method('processInput');

        $calc->processString($inputString);
    }

    public function testProcessStringReturnsResultFromProcessResultAfterAllInputProcessed() {
        $expectedResult = 45;

        $calc = $this->getCalculatorMock(['processInput', 'processResult']);
        $calc->expects($this->once())
            ->method('processResult')
            ->will($this->returnValue($expectedResult));

        $result = $calc->processString('some string with tokens');
        $this->assertEquals($expectedResult, $result, "Expected result from processResult returned from processString");
    }

    /**
     * Prepare a mock Stack object
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getStackMock() {
        return $this->getMockBuilder(RPNStack::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Prepare a mock object of the calculator class
     *
     * @param array $methods array of methods of mock
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCalculatorMock($methods = null) {
        $calc = $this->getMockBuilder(RPNCalculator::class)
            ->setConstructorArgs([$this->stack])
            ->setMethods($methods)
            ->getMock();
        return $calc;
    }

    /**
     * Prepare a mock Operator object
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getOperatorMock() {
        $operator = $this->getMockBuilder(OperatorTestable::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $operator;
    }
}
