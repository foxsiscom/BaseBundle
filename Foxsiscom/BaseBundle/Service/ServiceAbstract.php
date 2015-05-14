<?php

namespace Foxsiscom\BaseBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\SecurityContext;

abstract class ServiceAbstract
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    protected $validator;

    protected $securityContext;

    public $rootEntity;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @DI\InjectParams({
     *     "entityManager" = @DI\Inject("doctrine.orm.entity_manager")
     * })
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    public function getUser()
    {
        return $this->getSecurityContext()->getToken()->getUser();
    }

    /**
     * @DI\InjectParams({
     *     "validator" = @DI\Inject("validator")
     * })
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * @return \Symfony\Component\Validator\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @DI\InjectParams({
     *     "securityContext" = @DI\Inject("security.context")
     * })
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
        return $this;
    }

    public function getManager()
    {
        $manager = $this->getDocumentManager();
        if (empty($manager)) {
            $manager = $this->getEntityManager();
        }
        return $manager;
    }

    /**
     * @param mixed $object
     * @param string[] $groups
     * @throws ServiceValidationException
     */
    protected function validate($object, $groups = array())
    {

        $validations = $this->getValidator()->validate($object, $groups);

        if (count($validations)) {

            $messages = array();
            foreach ($validations as $validation) {
                $messages[] = $validation->getPropertyPath() . ': ' . $validation->getMessage();
            }

            $message = implode(' ', $messages);

            $exp = new ServiceValidationException($message);
            $exp->setValidations($validations);

            throw $exp;
        }
    }

    /**
     *
     * @param array $arrayData
     * @return array
     */
    protected function filterEmpty($arrayData) {
        return array_filter(
            $arrayData,
            function ($var) {
                return !($var === '');
            }
        );
    }

    /**
     *
     * @param ServiceData $sd
     * @return \Fox\CmsBundle\Service\StaticPageService
     */
    public function add($params)
    {
        $object = $sd->get('object');
        $this->validate($object, array(
            'registration'
        ));
        $manager = $this->getManager();
        $manager->persist($object);
        $manager->flush();

        return $this;
    }

    /**
     *
     * @param ServiceData $sd
     * @return \Fox\CmsBundle\Service\StaticPageService
     */
    public function modify(ServiceData $sd)
    {
        $object = $sd->get('object');
        $this->validate($object, array(
            'edition'
        ));
        $manager = $this->getManager();
        $manager->persist($object);
        $manager->flush();

        return $this;
    }

    /**
     *
     * @param ServiceData $sd
     * @return \Fox\CmsBundle\Service\StaticPageService
     */
    public function remove(ServiceData $sd)
    {
        $document = $sd->get('object');
        $manager = $this->getManager();
        $manager->remove($document);
        $manager->flush();

        return $this;
    }

    /**
     *
     * @param array $criteria
     * @return QueryBuilder
     */
    public function findByCriteria($criteria = array())
    {
        $criteria = $this->filterEmpty($criteria);
        $repository = $this->getEntityManager()->getRepository($this->rootEntity);
        return $repository->findByCriteria($criteria);
    }

    public function getFormData()
    {
        return array();
    }
}