Inline testing- Pyha Framework Component
========================================

Inline testing is a kick start for implementing TDD in your team(or for you if you are coding alone).

You may understand all the thing by analyzing the following example:

    <?php

    /**
     * @author AlexanderC
     */

    namespace Pyha\Libs\ITest;

    /**
     * This is an test example to test the
     * inline testing lib o_O
     *
     * @assert {
     *      @params {
     *          'i like strings'
     *      }
     *      @assertions {
     *          $result->foo == 'i like strings'
     *          $result->foo->baz == 'hey, we do not have such property'
     *          !is_string($result->foo)
     *          $result instanceof Pyha\Libs\ITest\Example
     *      }
     * }
     */
    class Example
    {
        /**
         * @var mixed
         */
        public $foo;

        /**
         * @param Application $foo
         */
        public function __construct($foo)
        {
            $this->foo = $foo;
        }

        /**
         * Note that for the method testing is reused class
         * instance from the class test case if method cases
         * are added during the class test case(isset testMethods flag)
         *
         * @param mixed $a
         * @param callable $b
         * @param array $c
         *
         * @assert {
         *      @params {
         *          2
         *          function(array $arr) { return $arr[0]; }
         *          [2]
         *      }
         *      @assertions {
         *          $result == 4
         *          segswegw // this is messed thing, but don't worry about!!!
         *          is_int($result)
         *          !is_scalar($result)
         *      }
         * }
         */
        public function simpleTest($a, callable $b, array $c)
        {
            return call_user_func($b, $c) + $a;
        }
    }

    /**
     * @param int $a
     * @param int $b
     * @param array $c
     *
     * @assert {
     *      @params {
     *          2
     *          2
     *          [2]
     *      }
     *      @assertions {
     *          $result == 6
     *          segswegw // this is messed thing, but don't worry about!!!
     *          !is_int($result)
     *          is_scalar($result)
     *      }
     * }
     */
    function exampleTestFunction($a, $b, array $c)
    {
        return $a + $b + $c[0];
    }

How do i run my tests?
----------------------

The answer is in the code bellow:

    $tester = new Libs\ITest\Test();

    $classes = get_declared_classes();
    foreach($classes as $class) {
         $tester->addClass($class);
    }
    unset($classes);

    $functions = get_defined_functions()['user'];
    foreach($functions as $function) {
        $tester->addFunction($function);
    }
    unset($functions);

    $tester->run();

    if(count($tester->getFails()) > 0) {
        exit(nl2br("\n\n{$tester}"));
    }

This will run the tests for all your function and classes loaded in current session.
This should be placed in an shutdown function or something like this.
But of course you can add classes and function manually or even scan directories
recursively and add your "things" to be tested by yourself

Load library
------------

If you are not using Composer

Install via Composer
--------------------

To install it via composer just use following command:

    $ composer.phar install