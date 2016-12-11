<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 12/9/2016
 * Time: 2:22 PM
 */

namespace dschwartz\RPNCalculator;
use dschwartz\RPNCalculator\RPNExceptions\StackEmptyException;
use dschwartz\RPNCalculator\RPNExceptions\TooManyOperandsException;
use dschwartz\RPNCalculator\RPNExceptions\UnknownOperatorException;
use dschwartz\RPNCalculator\RPNOperators\OperatorInterface;

class RPNCalculator
{
    /**
     * @var RPNStack
     */
    private $_stack;

    private $_operators = [
        '/\+/' => RPNOperators\AdditionOperator::class,
        '/\-/' => RPNOperators\SubtractionOperator::class,
        '/\*/' => RPNOperators\MultiplicationOperator::class,
        '/\//' => RPNOperators\DivisionOperator::class,
    ];

    /**
     * RPNCalculator constructor.
     * @param RPNStack $stack
     */
    public function __construct(RPNStack $stack) {
        $this->_stack = $stack;
        $this->_stack->init();
    }

    public function getStack() {
        return $this->_stack;
    }

    /**
     * @param string $exp space-delimited string of tokens to process for calculation
     * @return num the numeric result
     */
    public function processString($exp) {
        $tokens = explode(" ", $exp);
        foreach ($tokens as $token) {
            $this->processInput($token);
        }

        return $this->processResult();
    }

    /**
     * Process a token from the input:
     *   - operands are pushed onto the stack
     *   - operators are executed with the proper number of operands from the stack and the result is pushed on
     * @param string $value The token to process
     */
    public function processInput($value) {
        if ($this->isOperand($value)) {
            $this->processOperand($value);
        } else {
            $this->processOperator($value);
        }
    }

    /**
     * Return the final value when the processing is completed. Must be only one value left on the stack
     *
     * @return mixed
     * @throws TooManyOperandsException when the stack is not empty at the end
     */
    public function processResult() {
        $result = $this->_stack->pop();

        if (!$this->_stack->isEmpty()) {
            throw new TooManyOperandsException();
        }
        return $result;
    }

    /**
     * Any numeric value is considered an operand
     *
     * @param string $value Token value to check
     * @return bool True if the value is numeric
     */
    protected function isOperand($value) {
        return is_numeric($value);
    }

    /**
     * Push the operand onto the stack for future processing
     *
     * @param string $value numeric value to push onto the stack
     */
    protected function processOperand($value) {
        $this->_stack->push($value);
    }

    /**
     * Identify the operator being requested
     * Pop the desired number of operands off the stack to pass to the operator
     * Execute the operation with the operands
     * Push the result of the operation back onto the stack
     *
     * @param string $value Token value which will identify an operator
     *
     * @throws UnknownOperatorException if the operator can not be found
     * @throws StackEmptyException if there are not enough operands on the stack for the desired operation
     */
    protected function processOperator($value) {
        $operator = $this->getOperator($value);
        $operands = [];

        $opCount = $operator->getOperandCount();
        for ($i = 0; $i < $opCount; $i++) {
            $operands[] = $this->_stack->pop();
        }

        $result = $operator->operate($operands);

        $this->_stack->push($result);
    }

    /**
     * @param string $value the input string used to identify an operator
     * @return OperatorInterface
     * @throws UnknownOperatorException if an operator cannot be identified by the token value
     */
    protected function getOperator($value) {
        foreach ($this->_operators as $opExp => $opClass) {

            if (preg_match($opExp, $value)) {
                return new $opClass();
            }
        }
        throw new UnknownOperatorException();
    }
}