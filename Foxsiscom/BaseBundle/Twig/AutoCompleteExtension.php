<?php
namespace Foxsiscom\BaseBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;

/**
* @DI\Service
* @DI\Tag("twig.extension")
*/
class AutoCompleteExtenxioin extends \Twig_Extension
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'auto_complete_extensions';
    }

    /**
     * @return mixed[]
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('auto_complete', array($this, 'createButton'), array('is_safe' => array('html')))
        );
    }

    public function createButton()
    {
        return 'aqui vai funfar';
    }
}
