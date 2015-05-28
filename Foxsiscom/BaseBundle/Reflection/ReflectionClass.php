<?php
namespace Foxsiscom\BaseBundle\Reflection;

class ReflectionClass extends \ReflectionClass
{

    public function getFirstParameterClass(\ReflectionMethod $method)
    {
        $parameter = $method->getParameters();
        return $parameter[0]->getClass()->getName();
    }
}