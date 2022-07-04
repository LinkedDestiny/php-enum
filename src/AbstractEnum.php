<?php

namespace LinkCloud\Enum;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionException;
use RuntimeException;
use Stringable;

abstract class AbstractEnum implements Stringable
{
    /**
     * 值
     * @var mixed
     */
    protected mixed $value;

    /**
     * name
     * @var string
     */
    protected string $name;

    /**
     * 消息
     * @var string
     */
    protected string $message;

    /**
     * @var array<class-string<AbstractEnum>, array<mixed, >>
     */
    private static array $valueContainer = [];

    /**
     * A List of available enumerator names by enumeration class
     *
     * @var array<class-string<AbstractEnum>, string[]>
     */
    private static array $nameContainer = [];

    /**
     * @param mixed $value
     * @param string $name
     * @param string $message
     */
    public function __construct(mixed $value, string $name, string $message)
    {
        $this->value = $value;
        $this->name = $name;
        $this->message = $message;
    }

    /**
     * get value of enum
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * get name of enum
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the name of the enumerator
     *
     * @return string
     * @see getName()
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    public function __clone(): void
    {
        throw new RuntimeException();
    }

    public function __serialize(): array
    {
        return ['value' => $this->value];
    }

    public function __unserialize(array $data): void
    {
        if (!array_key_exists('value', $data)) {
            throw new RuntimeException('Missing array key "value"');
        }

        $value = $data['value'];
        $instance = static::byValue($value);

        $this->name = $instance->name;
        $this->message = $instance->message;
        $this->value = $instance->value;
    }

    /**
     * @param mixed $value
     * @return static
     */
    public static function byValue(mixed $value): static
    {
        $class = get_called_class();
        static::analyze();

        $instance = self::$valueContainer[$class][$value] ?? null;
        if (empty($instance)) {
            throw new InvalidArgumentException("{$value} not defined in {$class}");
        }
        return $instance;
    }

    /**
     * @param string $name
     * @return static
     */
    public static function byName(string $name): static
    {
        $class = get_called_class();
        static::analyze();

        $instance = self::$nameContainer[$class][$name] ?? null;
        if (empty($instance)) {
            throw new InvalidArgumentException("{$name} not defined in {$class}");
        }
        return $instance;
    }

    /**
     * @return array
     */
    public static function getValues(): array
    {
        $class = get_called_class();
        static::analyze();
        return array_keys(self::$valueContainer[$class] ?? []);
    }

    /**
     * @return array
     */
    public static function getNames(): array
    {
        $class = get_called_class();
        static::analyze();
        return array_keys(self::$nameContainer[$class] ?? []);
    }

    /**
     * @return array
     */
    public static function all(): array
    {
        $class = get_called_class();
        static::analyze();
        return array_values(self::$nameContainer[$class] ?? []);
    }

    public static function hasValue(mixed $value): bool
    {
        $class = get_called_class();
        static::analyze();

        return isset(self::$valueContainer[$class][$value]);
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function hasName(string $name): bool
    {
        $class = get_called_class();
        static::analyze();

        return isset(self::$nameContainer[$class][$name]);
    }

    /**
     * @return void
     */
    public static function analyze(): void
    {
        $class = get_called_class();
        if (isset(self::$nameContainer[$class]) && !empty(self::$nameContainer[$class])) {
            return;
        }

        try {
            $reflection = new ReflectionClass($class);
            $constants = $reflection->getReflectionConstants(ReflectionClassConstant::IS_PUBLIC);

            foreach ($constants as $constant) {
                $name = $constant->getName();
                $value = $constant->getValue();

                $attributes = $constant->getAttributes(EnumMessage::class);
                if (empty($attributes)) {
                    $message = $name;
                } else {
                    $message = $attributes[0]->newInstance()->message;
                }

                $instance = new static($value, $name, $message);

                self::$nameContainer[$class][$name] = $instance;
                self::$valueContainer[$class][$value] = $instance;
            }
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException("{$class} analyze failed: " . $e->getMessage());
        }
    }

    /**
     * @param string $method
     * @param array $args
     * @return static
     */
    public static function __callStatic(string $method, array $args)
    {
        return static::byName($method);
    }
}