<?php
namespace LinkCloud\Enum\Tests;

use LinkCloud\Enum\AbstractEnum;
use LinkCloud\Enum\EnumMessage;

/**
 * @method static TestEnum INTEGER()
 * @method static TestEnum STRING()
 */
class TestEnum extends AbstractEnum
{
    #[EnumMessage('整数')]
    public const INTEGER = 1;

    #[EnumMessage('字符串')]
    public const STRING = 'string';
}