<?php

/**
 * @author AlexanderC
 */

namespace Pyha\Libs\ITest;

class AssertionFail
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $caseName;

    /**
     * @param string $message
     * @param string $caseName
     */
    public function __construct($message, $caseName)
    {
        $this->message = (string) $message;
        $this->caseName = (string) $caseName;
    }

    /**
     * print fail string
     *
     * @return string
     */
    public function __toString()
    {
        return "Assertion Failed on {$this->caseName}: {$this->message}\n";
    }
}
