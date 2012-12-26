<?php

/**
 * This is an class test case
 * by default are tested:
 * - for the class the __construct
 * - also all messages if isset such flag
 *
 * @author AlexanderC
 * @note the $result var will be an instance of the class provided
 */

namespace Pyha\Libs\ITest\TestCases;

use Pyha\Libs\ITest\Parser\DocblockExtractor;
use Pyha\Libs\ITest\AssertionFail;

class ClassCase extends ICase
{
    /**
     * @var \ReflectionClass
     */
    private $refl;

    /**
     * @var bool
     */
    private $implicitOk = false;

    /**
     * @var bool
     */
    private $testMethods;

    /**
     * @param mixed $class
     * @param bool $testMethods
     */
    public function __construct($class, $testMethods = true)
    {
        // case already an reflection
        if($class instanceof \ReflectionClass) {
            $this->refl = $class;
        } else {
            $this->refl = new \ReflectionClass($class);
        }

        // set methods test flag
        $this->testMethods = (bool) $testMethods;

        if(!$this->refl->isInstantiable()) {
            $this->implicitOk = true;
        }
    }

    /**
     * {@inherit}
     */
    public function run()
    {
        $fails = [];

        // if can not be tested- do not test
        if($this->implicitOk) {
            return $fails;
        }

        $extractor = new DocblockExtractor($this->getDocblockSection());

        // validate docblock
        if(!$extractor->validate()) {
            return $fails;
        }

        // extract test data
        $data = $extractor->extract();

        // case empty anything we need for the test
        if(!$this->_validateExtractedData($data)) {
            return $fails;
        }

        // case no constructor
        if(NULL === ($constructor = $this->refl->getConstructor())) {
            return $fails;
        }

        // get class instance
        $instance = $this->refl->newInstanceWithoutConstructor();

        // call constructor with provided params
        $constructor->invokeArgs($instance, $data[DocblockExtractor::PARAMS]);

        // iterate and assert the result
        foreach($data[DocblockExtractor::ASSERTIONS] as $debugInfoKey => $assertion) {
            try {
                $result = $assertion($instance);

                if(!$result) {
                    $fails[] = new AssertionFail(
                        sprintf(self::FAILED_VALIDATING, "'object'") .
                            " {$data[DocblockExtractor::DEBUG_INFO][$debugInfoKey]}",
                        $this->getName());
                }
            } catch(\Exception $e) {
                $fails[] = new AssertionFail(
                    self::EXCEPTION_DURING_ASSERTION . " [{$e->getMessage()}] {$data[DocblockExtractor::DEBUG_INFO][$debugInfoKey]}",
                    $this->getName());
            }

            $this->_incrementAssertionsCount();
        }

        // case also required to test messages
        if($this->testMethods) {
            $methods = $this->refl->getMethods();
            $cases = [];

            // create test cases for all methods
            foreach($methods as $method) {
                // create new test case for the method
                $methodCase = new MethodCase($this->refl->getName(), $method->getName());
                // set the object we've tested already
                $methodCase->setImplicitInstance($instance);
                $cases[] = $methodCase;
            }
            unset($methods);

            // run test cases
            foreach($cases as $case) {
                try {
                    $fails = array_merge($fails, $case->run());
                    $this->assertionsCount += $case->getAssertionsCount();
                } catch(\Exception $e) {
                    $fails[] = new AssertionFail(
                        self::EXCEPTION_DURING_ASSERTION . " [{$e->getMessage()}]",
                        $case->getName());
                }
            }
        }

        return $fails;
    }

    /**
     * {@inherit}
     */
    public function getName()
    {
        return $this->refl->getName() . "::#instance";
    }

    /**
     * {@inherit}
     */
    public function getDocblockSection()
    {
        return $this->refl->getDocComment() ? : "";
    }
}
