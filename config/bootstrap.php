<?php declare(strict_types=1);

namespace Chiphpmunk;

use AlanVdb\Dispatcher\Definition\DispatcherFactoryInterface;
use AlanVdb\Server\Definition\ServerEnvironmentServiceInterface;
use AlanVdb\Server\Definition\DotEnvParserFactoryInterface;

$root = dirname(__DIR__);
require $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

(new App([
    // Factories
    DispatcherFactoryInterface::class => \AlanVdb\Dispatcher\Factory\DispatcherFactory::class,
    ServerEnvironmentServiceInterface::class => \AlanVdb\Server\ServerEnvironmentService::class,
]))->run($root);
