<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 12/9/2016
 * Time: 3:29 PM
 */
namespace tests;

use dschwartz\RPNCalculator\RPNExceptions\StackEmptyException;
use PHPUnit\Framework\TestCase;
use dschwartz\RPNCalculator\Testable\RPNStackTestable;

class StackTest extends TestCase
{
    protected $stack;

    public function setUp() {
        $this->stack = new RPNStackTestable();
        $this->stack->init();
    }

    public function testStackIsEmptyOnInit() {
        $stackArr = $this->stack->toArray();
        $this->assertEquals([], $stackArr, 'Stack is empty on init');
    }

    public function testIsEmptyIsTrueWhenStackIsEmpty() {
        $this->stack->setStack([]);
        $this->assertTrue($this->stack->isEmpty(), "Expect stack to be empty");
    }

    public function testIsEmptyIsFalseWhenStackIsNotEmpty() {
        $this->stack->setStack(['someValue']);
        $this->assertFalse($this->stack->isEmpty(), "Expect stack to not be empty");
    }

    public function testPushAddsValueToTopOfStack() {
        $value = 1;
        $this->stack->push($value);

        $stackArr = $this->stack->toArray();
        $this->assertEquals([$value], $stackArr, "Pushing $value on to stack adds it to beginning of array");
    }

    public function testPopRemovesValueFromTopOfStackAndReturnsIt() {
        $value = 2;
        $setupStack = [$value];
        $this->stack->setStack($setupStack);

        $popped = $this->stack->pop();
        $this->assertEquals($value, $popped, "Popped $value off the top of stack");

        $this->assertEquals([], $this->stack->toArray(), "Stack is empty after pop");
    }

    public function testPushingSeveralValuesAddsEachToTopOfStack() {
        $value1 = "a";
        $value2 = "2";
        $value3 = "x";

        $this->stack->push($value1);
        $this->stack->push($value2);
        $this->stack->push($value3);

        $expectedArr = [$value1, $value2, $value3];

        $stackArr = $this->stack->toArray();
        $this->assertEquals($expectedArr, $stackArr, "Each value pushed is added to the top of the stack");
    }

    public function testPopAfterPushingSeveralValuesReturnsMostRecentlyPushedValue()
    {
        $value1 = "9";
        $value2 = "z";
        $value3 = "third";

        $setupStack = [$value1, $value2, $value3];
        $this->stack->setStack($setupStack);

        $popped = $this->stack->pop();
        $this->assertEquals($value3, $popped, "Last value added ($value3) is popped first");

        $this->assertEquals(2, count($this->stack->toArray()), "Stack now has only 2 values left");

        $popped = $this->stack->pop();
        $this->assertEquals($value2, $popped, "Second value added ($value2) is popped next");

        $this->assertEquals(1, count($this->stack->toArray()), "Stack now has only 1 value left");

        $popped = $this->stack->pop();
        $this->assertEquals($value1, $popped, "First value added ($value1) is popped last");

        $this->assertEquals(0, count($this->stack->toArray()), "Stack is not empty");
    }

    public function testPopOnEmptyArrayThrowsException() {
        $this->expectException(StackEmptyException::class);
        $this->stack->pop();
    }
}