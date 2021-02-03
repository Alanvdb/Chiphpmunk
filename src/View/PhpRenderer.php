<?php

namespace Chiphpmunk\View;

use InvalidArgumentException;

class PhpRenderer implements RendererInterface
{
    /**
     * @var string[] $namespaces View directories associated with namespaces
     */
    private $namespaces;

    /**
     * Adds view namespace
     * 
     * @param string $namespace Namespace use to access view directory
     * @param string $directory Directory the namespace points to
     * 
     * @throws InvalidArgumentException
     * If namespace is empty or contain "@" character.
     * If provided directory does not exists.
     * 
     * @return self
     */
    public function setNamespace(string $namespace, string $directory) : RendererInterface
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException('Provided directory "' . $directory . '" does not exists.');
        }
        if ($namespace === '') {
            throw new InvalidArgumentException('Provided namespace cannot be empty.');
        }
        if (strpos($namespace, '@') !== false) {
            throw new InvalidArgumentException('Namespace cannot contain "@".');
        }
        $this->namespaces[$namespace] = $directory;
        return $this;
    }

    /**
     * Returns generated content
     * 
     * @param string  $view Template identifier
     * For example, if the template "home.php" is contained in the namespace named "default", specify "home@default".
     * @param mixed[] $vars Associative array of vars (keys must have a valid PHP variable name).
     *
     * @throws InvalidArgumentException On any error with arguments.
     *
     * @return string The generated view
     */
    public function render(string $view, array $vars = []) : string
    {
        if (($atPos = strpos($view, '@')) === false) {
            throw new InvalidArgumentException(
                '$view argument must contain filename without extension and namespace separated with "@" character.'
            );
        }

        $file = substr($view, 0, $atPos);
        $namespace = substr($view, $atPos + 1);

        if (!array_key_exists($namespace, $this->namespaces)) {
            throw new InvalidArgumentException('Namespace "' . $namespace . '" does not exists.');
        }

        $file = $this->namespaces[$namespace] . DIRECTORY_SEPARATOR . $file . '.php';

        if (!is_file($file)) {
            throw new InvalidArgumentException('Cannot find view file: "' . $file . '".');
        }

        foreach (array_keys($vars) as $varName) {
            if (!preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $varName)) {
                throw new InvalidArgumentException(
                    'Invalid $vars array key: "' . $varName . '" cannot be extracted as PHP var name.'
                );
            }
        }
        ob_start();
        extract($vars);
        require $file;
        return ob_get_clean();
    }
}
