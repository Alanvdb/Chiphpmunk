<?php declare(strict_types=1);

namespace Chiphpmunk;

// use Psr\Http\Message\ServerRequestFactoryInterface;
use AlanVdb\Dispatcher\Definition\DispatcherFactoryInterface;
use AlanVdb\Server\Definition\ServerEnvironmentServiceInterface;
use Chiphpmunk\Exception\InvalidConfigurationProvided;

class App
{
    protected array $factories = [
        DispatcherFactoryInterface::class => '',
        // ServerRequestFactoryInterface::class => '',
        ServerEnvironmentServiceInterface::class => ''
    ];

    public function __construct(array $factories)
    {
        $interfaces = array_keys($this->factories);

        foreach ($interfaces as $interface) {
            if (!array_key_exists($interface, $factories)) {
                throw new InvalidConfigurationProvided(sprintf("Missing component in provided configuration: '%s'.", $interface));
            }
            if (!in_array($interface, class_implements($factories[$interface]))) {
                throw new InvalidConfigurationProvided(sprintf("Invalid component provided in configuration: class '%s' must implements '%s'.", $factories[$interface], $interface));
            }
            $this->factories[$interface] = $factories[$interface];
        }
    }

    public function run(string $root) : void
    {
        $env = (new $this->factories[ServerEnvironmentServiceInterface::class]())->create($root);
        var_dump($env);
    }
}
