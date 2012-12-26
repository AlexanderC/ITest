<?php

/**
 * @author AlexanderC
 */

namespace Pyha\Libs\ITest\TestCases;

use Pyha\Libs\ITest\Parser\DocblockExtractor;
use Pyha\Libs\ITest\AssertionFail;

class FunctionCase extends ICase
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
     * @param mixed $function
     */
    public function __construct($function)
    {
        $this->refl = new \ReflectionFunction($function);

        // manage some extra cases
        if($this->refl->isDisabled()) {
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

        // call method with provided params
        $methodResult = $this->refl->invokeArgs($data[DocblockExtractor::PARAMS]);

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
        return "{$this->refl->getName()}()";
    }

    /**
     * {@inherit}
     */
    public function getDocblockSection()
    {
        return $this->refl->getDocComment() ? : "";
    }
}
