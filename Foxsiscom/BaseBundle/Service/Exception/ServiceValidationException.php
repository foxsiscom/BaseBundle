<?php

namespace Foxsiscom\BaseBundle\Service\Exception;

class ServiceValidationException extends \Exception
{
    private $validations = array();

    public function getValidations(){
        return $this->validations;
    }

    public function setValidations ($validations) {
        $this->validations = $validations;
        return $this;
    }
}