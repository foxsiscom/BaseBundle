<?php

namespace Foxsiscom\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

abstract class ControllerCrudAbstract extends ControllerAbstract
{

    /**
     * Lists all entities.
     *
     * @Route("/")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new ComunidadeType());
        if ($request->query->get($form->getName())) {
            $form->submit($request);
        }
        $criteria = $request->query->get($form->getName(), array());
        $service = $this->get('cnccncbundle.comunidade_service');
        $sd = ServiceData::build(
            array(
                'criteria' => $criteria,
                'page' => $request->get('page', 1)
            )
        );
        $query = $service->findByCriteria($sd);

        return array(
            'entities' => $this->get("knp_paginator")->paginate($query, $request->get('page', 1)),
            'form' => $form->createView()
        );
    }

    public function create($entity = null)
    {
        $entity = $entity != null ? $entity : $this->getService()->getNewEntity();
        $this->view->assign('formData', $this->getService()
            ->getFormData());
        $this->view->assign('entity', $entity);
    }
}