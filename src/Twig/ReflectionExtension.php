<?php

namespace App\Twig;

use ReflectionClass;
use RuntimeException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

class ReflectionExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('call_static', $this->callStaticMethod(...)),
            new TwigFunction('get_static', $this->getStaticProperty(...)),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('instanceof', $this->isInstanceof(...)),
        ];
    }

    public function isInstanceof($var, $instance): bool
    {
        return $var instanceof $instance;
    }

    public function callStaticMethod($class, $method, array $args = [])
    {
        $reflectionClass = new ReflectionClass($class);

        // Check that method is static AND public
        if ($reflectionClass->hasMethod($method) && $reflectionClass->getMethod($method)->isStatic() && $reflectionClass->getMethod($method)->isPublic()) {
            return \call_user_func_array($class.'::'.$method, $args);
        }

        throw new RuntimeException(sprintf('Invalid static method call for class %s and method %s', $class, $method));
    }

    public function getStaticProperty($class, $property)
    {
        $reflectionClass = new ReflectionClass($class);

        // Check that property is static AND public
        if ($reflectionClass->hasProperty($property) && $reflectionClass->getProperty($property)->isStatic() && $reflectionClass->getProperty($property)->isPublic()) {
            return $reflectionClass->getProperty($property)->getValue();
        }

        throw new RuntimeException(sprintf('Invalid static property get for class %s and property %s', $class, $property));
    }
}
