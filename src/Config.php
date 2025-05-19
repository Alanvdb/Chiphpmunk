<?php declare(strict_types=1);

namespace Chiphpmunk;

// Interfaces
use ArrayAccess;
use AlanVdb\Dispatcher\Definition\DispatcherFactoryInterface;
use AlanVdb\Server\Definition\ServerEnvironmentFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

// Exceptions
use Chiphpmunk\Exception\InvalidConfigurationProvided;

class Config implements ArrayAccess
{
    public const FACTORIES = [
        DispatcherFactoryInterface::class,
        ServerRequestFactoryInterface::class,
        ServerEnvironmentFactoryInterface::class,
    ];

    protected array $components = [];

    public function __construct(array $components)
    {
        foreach (self::FACTORIES as $interface) {
            if (!array_key_exists($interface, $components)) {
                throw new InvalidConfigurationProvided("Missing factory in provided configuration : $interface");
            }
            if (!in_array($interface, class_implements($components[$interface]))) {
                throw new InvalidConfigurationProvided("Provided class `$components[$interface]` must implement `$interface`.");
            }
            $this->components[$interface] = $components[$interface];
        }
    }

    public function offsetExists($offset) : bool
    {
        return array_key_exists($offset, $this->components);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->components[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        
    }

    public function offsetUnset(mixed $offset): void
    {
        if ($this->offsetExists($offset)) {
            unset($this->components[$offset]);
        }
    }
}