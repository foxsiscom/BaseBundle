<?php
namespace Foxsiscom\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

abstract class ControllerAbstract extends Controller
{

    protected $service;

    public function getService()
    {
        return new $this->service();
    }

    /**
     *
     * @param string $message
     * @param string $type
     */
    public function addMessage($message, $type = 'default')
    {
        $this->get('session')
            ->getFlashBag()
            ->add($type, $message);
    }

    public function create($entity = null)
    {
        $entity = $entity != null ? $entity : $this->getService()->getNewEntity();
        $this->view->assign('formData', $this->getService()
            ->getFormData());
        $this->view->assign('entity', $entity);
    }
}