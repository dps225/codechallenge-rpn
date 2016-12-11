# codechallenge-rpn
Coding Challenge - Creating a Reverse Polish Notation Calculator

This is a simple PHP implementation of an RPN Calculator. At this stage, the interface is simply on the command line. 
Standard and Interactive modes are offered.
 
This implementation requires PHP 7.1.*. May be compatible with previous versions, but not tested there.

Use composer install to install the required version of PHPUnit to execute tests and run the tests from the root
repository directory like so:

vendor/bin/phpunit --bootstrap autoload.php tests

Currently supported operations are the standard addition, subtraction, multiplication and division.