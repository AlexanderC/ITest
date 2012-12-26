<?php

/**
 * @author AlexanderC
 */

namespace Pyha\Libs\ITest\TestCases;

use Pyha\Libs\ITest\Parser\DocblockExtractor;
use Pyha\Libs\ITest\AssertionFail;

class MethodCase extends ICase
{
    /**
     * @var \ReflectionMethod
     */
    private $refl;

    /**
     * @var bool
     */
    private $implicitOk = false;

    /**
     * @var NULL|object
     */
    private $implicitInstance;

    /**
     * @var array
     */
    private $defaults;

    /**
     * @param mixed $class
     * @param string|NULL $name
     */
    public function __construct($class, $name)
    {
        $this->refl = new \ReflectionMethod($class, $name);
        $this->defaults = [
            'class' => $class,
            'name' => $name
        ];

        // manage some extra cases
        if($this->refl->isConstructor()
            || $this->refl->isAbstract()) {
            $this->implicitOk = true;
        } else {
            // assure that is accessibble
            $this->refl->setAccessible(true);
        }
    }

    /**
     * @param object $instance
     * @return MethodCase
     * @throws \RuntimeException
     */
    public function setImplicitInstance($instance)
    {
        if(!is_object($instance)) {
            throw new \RuntimeException(
                "Implicit methodCase test class instance should be an object, " . gettype($instance) . " given.");
        }

        $this->implicitInstance = $instance;
        return $this;
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

        if(is_object($this->implicitInstance)) {
            $instance = $this->implicitInstance;
        } else {
            $instance = $this->refl->getDeclaringClass()->newInstanceWithoutConstructor();
        }

        // call method with provided params
        $methodResult = $this->refl->invokeArgs($instance, $data[DocblockExtractor::PARAMS]);

        $debugResult = preg_replace("/(\s|\n)+/su", " ", var_export($methodResult, true));
        $debugResultType = gettype($methodResult);

        // iterate and assert the result
        foreach($data[DocblockExtractor::ASSERTIONS] as $debugInfoKey => $assertion) {
            try {
                $result = $assertion($methodResult);

                if(!$result) {
                    $fails[] = new AssertionFail(
                        sprintf(self::FAILED_VALIDATING, "'{$debugResultType} {$debugResult}'") .
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

        return $fails;
    }

    /**
     * {@inherit}
     */
    public function getName()
    {
        return "{$this->defaults['class']}::{$this->defaults['name']}()";
    }

    /**
     * {@inherit}
     */
    public function getDocblockSection()
    {
        return $this->refl->getDocComment() ? : "";
    }
}
