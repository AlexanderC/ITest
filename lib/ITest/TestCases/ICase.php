<?php

/**
 * @author AlexanderC
 */

namespace Pyha\Libs\ITest\TestCases;

use Pyha\Libs\ITest\Parser\DocblockExtractor;

abstract class ICase
{
    const FAILED_VALIDATING = '(Failed validating %s)';
    const EXCEPTION_DURING_ASSERTION = '(Exception during assertion)';

    /**
     * @var int
     */
    protected $assertionsCount = 0;

    /**
     * get assertion things to be compared with
     * the results
     *
     * @return array All Fails
     */
    abstract public function run();

    /**
     * Get docblock section from the given test case
     *
     * @return string
     */
    abstract public function getDocblockSection();

    /**
     * get test case name
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Increment total assersions count
     */
    protected function _incrementAssertionsCount()
    {
        ++$this->assertionsCount;
    }

    /**
     * @return int
     */
    public function getAssertionsCount()
    {
        return $this->assertionsCount;
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function _validateExtractedData(array $data)
    {
        return array_key_exists(DocblockExtractor::PARAMS, $data)
                && isset($data[DocblockExtractor::ASSERTIONS], $data[DocblockExtractor::DEBUG_INFO])
                && !empty($data[DocblockExtractor::ASSERTIONS])
                && !empty($data[DocblockExtractor::DEBUG_INFO]);
    }
}
