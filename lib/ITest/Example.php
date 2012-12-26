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
