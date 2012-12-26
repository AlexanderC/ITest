<?php

/**
 * @author AlexanderC
 */

namespace Pyha\Libs\ITest;

class Test
{
    /**
     * @var array
     */
    private $cases = [];

    /**
     * assertions count
     *
     * @var int
     */
    private $assertions = 0;

    /**
     * runned test fails
     *
     * @var array
     */
    private $fails = [];

    /**
     * add class test case
     *
     * @param mixed $class
     * @param bool $testMethods
     * @return TestCases\ClassCase
     */
    public function addClass($class, $testMethods = true)
    {
        $case = new TestCases\ClassCase($class, $testMethods);
        $this->cases[] = $case;
        return $case;
    }

    /**
     * add method test case
     *
     * @param mixed $class
     * @param string $name
     * @return TestCases\MethodCase
     */
    public function addMethod($class, $name)
    {
        $case = new TestCases\MethodCase($class, $name);
        $this->cases[] = $case;
        return $case;
    }

    /**
     * add function test case
     *
     * @param mixed $function
     * @return TestCases\FunctionCase
     */
    public function addFunction($function)
    {
        $case = new TestCases\FunctionCase($function);
        $this->cases[] = $case;
        return $case;
    }

    /**
     * Run the test
     *
     * @return void
     */
    public function run()
    {
        foreach($this->cases as $case) {
            try {
                $this->fails = array_merge($this->fails, $case->run());
                $this->assertions += $case->getAssertionsCount();
            } catch(\Exception $e) {
                $this->fails[] = new AssertionFail(
                    TestCases\ICase::EXCEPTION_DURING_ASSERTION . " [{$e->getMessage()}]",
                    $case->getName());
            }
        }

        return $this;
    }

    /**
     * get all fails occured during the test
     *
     * @return array
     */
    public function getFails()
    {
        return $this->fails;
    }

    /**
     * get all registered test cases objects
     *
     * @return array
     */
    public function getCases()
    {
        return $this->cases;
    }

    /**
     * print fails info
     *
     * @return string
     */
    public function __toString()
    {
        $output = "";

        foreach($this->fails as $fail) {
            $output .= "- {$fail}";
        }

        return $output . "\n~ " .
                count($this->fails) . " fail[s] occurred during the test(" .
                count($this->cases) . " case[s], {$this->assertions} assertion[s]).";
    }
}
