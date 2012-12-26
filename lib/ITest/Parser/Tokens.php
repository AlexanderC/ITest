<?php

/**
 * @author AlexanderC
 */

namespace Pyha\Libs\ITest\Parser;

class Tokens
{
    const T_ASSERT_BLOCK = '/(.*\n\s*\*)?\s*@assert\s*\{(.+)}\s*/sui';
    const T_PARAMS = '/\n\s*@params\s*\{(.+)\}\s*\n\s*@assertions\s*\{.+/sui';
    const T_ASSERTIONS = '/\n\s*@assertions\s*\{(.+)\}\s*\n/sui';
    const T_RESULT = '/\$result/i';
}
