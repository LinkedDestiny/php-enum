<?php
namespace LinkCloud\Enum\Tests;

use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function testEnum()
    {
        $value = TestEnum::byValue(1);
        self::assertEquals(1, $value->getValue());
        self::assertEquals('INTEGER', $value->getName());
        self::assertEquals('整数', $value->getMessage());

        $value = TestEnum::byName('STRING');
        self::assertEquals('string', $value->getValue());
        self::assertEquals('STRING', $value->getName());
        self::assertEquals('字符串', $value->getMessage());

        self::assertTrue(TestEnum::hasValue('string'));
        self::assertTrue(TestEnum::hasName('STRING'));

        $value = TestEnum::INTEGER();
        self::assertEquals(1, $value->getValue());
        self::assertEquals('INTEGER', $value->__toString());
        self::assertEquals('整数', $value->getMessage());
    }
}