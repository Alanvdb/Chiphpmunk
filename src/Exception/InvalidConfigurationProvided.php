<?php

namespace Chiphpmunk\Exception;

use Chiphpmunk\Exception\Definition\ConfigurationExceptionInterface;
use InvalidArgumentException;

class InvalidConfigurationProvided
    extends InvalidArgumentException
    implements ConfigurationExceptionInterface
{}
