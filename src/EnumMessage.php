<?php
namespace LinkCloud\Enum;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class EnumMessage
{
    /**
     * 枚举描述
     * @var string
     */
    public string $message = '';

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}