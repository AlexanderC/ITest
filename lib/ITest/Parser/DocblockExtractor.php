<?php

/**
 * @author AlexanderC
 */

namespace Pyha\Libs\ITest\Parser;

class DocblockExtractor
{
    const PARAMS = 'params';
    const ASSERTIONS = 'assertions';
    const DEBUG_INFO = 'debugInfo';

    /**
     * @var string
     */
    private $blockText;

    /**
     * @var array
     */
    private $extractionDump;

    /**
     * assertions debug info
     *
     * @var array
     */
    private $debugInfo = [];

    /**
     * @param string $blockText
     */
    public function __construct($blockText)
    {
        $this->blockText = $this->_prepareBlockText($blockText);
    }

    /**
     * strip comment lines from docblock
     *
     * @param string $blockText
     * @return string
     */
    protected function _prepareBlockText($blockText)
    {
        $blockText = trim($blockText);

        // strip start
        $blockText =  preg_replace("/^\s*\/\*\*/u", "", $blockText);

        // strip end
        $blockText =  preg_replace("/\n\s*\*\/$/u", "", $blockText);

        // delete '*' from each new line of comment
        $blockText =  preg_replace("/\n\s*\*/u", "\n", $blockText);

        return trim($blockText);
    }

    /**
     * @return string
     */
    public function getBlockText()
    {
        return $this->blockText;
    }

    /**
     * validate assertion docblock text
     *
     * @return bool
     */
    public function validate()
    {
        return (bool) preg_match(Tokens::T_ASSERT_BLOCK, $this->blockText, $this->extractionDump) // check if assertion block
                && is_array($this->extractionDump)
                && count($this->extractionDump) == 3 // check for source matching
                /* just hook */&& $this->extractionDump = $this->extractionDump[2] // store in the dump only
        ;
    }

    /**
     * Extract docblock assert section
     *
     * @return array
     * @throws \RuntimeException
     */
    public function extract()
    {
        // case was not validated
        if(!$this->extractionDump) {
            throw new \RuntimeException("You may validate docblock before extracting test data.");
        }

        return  [
            self::PARAMS => $this->_prepareParams(),
            self::ASSERTIONS => $this->_prepareAssertions(),
            self::DEBUG_INFO => $this->debugInfo
        ];
    }

    /**
     * prepare params to be delivered
     * to the end test point
     *
     *  @return array
     */
    protected function _prepareParams()
    {
        // case we do not have any params
        if(!(bool) preg_match(Tokens::T_PARAMS, $this->extractionDump, $params)
            || count($params) != 2) {
            return NULL;
        }

        return array_map(function($param)
        {
            // yeap, this is safer than eval
            $param = call_user_func(create_function("", "return ({$param});"));

            /**if($param instanceof \Closure) {
                return function() use ($param) {
                   return $param;
                };
            }*/

            return $param;
        }, explode("\n", trim($params[1])));
    }

    /**
     * prepare assertions to be delivered
     * to the end test point
     *
     *  @return array
     */
    protected function _prepareAssertions()
    {
        // case we do not have any params
        if(!(bool) preg_match(Tokens::T_ASSERTIONS, $this->extractionDump, $assertions)
            || count($assertions) != 2) {
            return NULL;
        }

        // delete empty assertions
        $assertions = array_filter(explode("\n", trim($assertions[1])), function($assertion)
        {
            return (bool) preg_match(Tokens::T_RESULT, $assertion);
        });

        // store debug info
        $this->debugInfo = array_map(function($assertion)
        {
            return trim($assertion);
        }, $assertions);

        // generate assertion callbacks
        return array_map(function($assertion)
        {
            return create_function('$result', "return ({$assertion});");
        }, $assertions);
    }
}
