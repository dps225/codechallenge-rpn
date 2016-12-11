<?php
require_once "autoload.php";

use dschwartz\RPNCalculator\RPNCalculator;
use dschwartz\RPNCalculator\RPNStack;
use dschwartz\RPNCalculator\RPNExceptions\StackEmptyException;
use dschwartz\RPNCalculator\RPNExceptions\TooManyOperandsException;
use dschwartz\RPNCalculator\RPNExceptions\UnknownOperatorException;

$shortopts = "ih";
$longopts = ["interactive", "help"];

$options = getopt($shortopts, $longopts);

$usageStatement = "
PHP Reverse Polish Notation (RPN) Calculator
Provide a space-separated list of operands and operators on the command 
line to perform a calculation.

Use \"-i\" or \"--interactive\" to enter terms in interactive mode.

Supported operators are:
 - \"+\" (Addition)
 - \"-\" (Subtraction)
 - \"*\" (Multiplication)
 - \"/\" (Division)
";

$isStandardMode = false;
$isInteractiveMode = false;

if (isset($options["i"]) || isset($options["interactive"])) {
    $isInteractiveMode = true;
} else if ($argc > 1) {
    $isStandardMode = true;
}

if (isset($options["h"]) || isset($options["help"]) || (!$isStandardMode && !$isInteractiveMode)) {
    echo $usageStatement;
    exit(0);
}

$stack = new RPNStack();
$calc = new RPNCalculator($stack);

// standard mode
if ($isStandardMode) {
    array_shift($argv); // ignore script name
    $inputString = implode(" ", $argv);

    try {
        $result = $calc->processString($inputString);
        echo $result;
    } catch (StackEmptyException $e) {
        echo "Invalid expression - too few operands\n";
        exit(1);
    } catch (TooManyOperandsException $e) {
        echo "Invalid expression - too many operands\n";
        exit(1);
    } catch (UnknownOperatorException $e) {
        echo "Invalid expression - unknown operator\n";
        exit(1);
    }
}
if ($isInteractiveMode) {
    $prompt = "\nEnter an operand or operator: ";
    echo $prompt;

    $input = trim(fgets(STDIN));
    while (trim($input) !== "") {
        try {
            $calc->processInput($input);
        } catch (StackEmptyException $e) {
            echo "Invalid expression - too few operands\n";
            exit(1);
        } catch (TooManyOperandsException $e) {
            echo "Invalid expression - too many operands\n";
            exit(1);
        } catch (UnknownOperatorException $e) {
            echo "Invalid expression - unknown operator\n";
            exit(1);
        }

        echo $calc->getStack();
        echo $prompt;

        $input = trim(fgets(STDIN));
    }

    // we've reached the end of the input
    try {
        echo "\n" . $calc->processResult();
    } catch (StackEmptyException $e) {
        echo "Invalid expression - too few operands\n";
        exit(1);
    } catch (TooManyOperandsException $e) {
        echo "Invalid expression - too many operands\n";
        exit(1);
    } catch (UnknownOperatorException $e) {
        echo "Invalid expression - unknown operator\n";
        exit(1);
    }
}
