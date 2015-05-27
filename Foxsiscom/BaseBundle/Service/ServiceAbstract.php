<?php

namespace Foxsiscom\BaseBundle\Service;

use Irtun\CoreBundle\ServiceLayer\ServiceValidationException;
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

    public $entity;

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

    public function find($id)
    {
        return $this->getEntityManager()->getRepository($this->entity)->find($id);
    }

    public function findByCriteria(ServiceData $sd)
    {
        return $this->getEntityManager()->getRepository($this->entity)->findByCriteria($sd);
    }

    public function getNewEntity()
    {
        return new $this->entity();
    }

    public function loadFromArray($entity, $array)
    {
		$array = $this->filterEmpty($array);
        $cmf = $this->getEntityManager()->getMetadataFactory();
        $entityName = get_class($entity);
        $fieldNames = $cmf->getMetadataFor($entityName)->getFieldNames();
        $associationNames = $cmf->getMetadataFor($entityName)->getAssociationNames();

        foreach ($array as $key => $value) {
            $set = 'set'.ucfirst($key);

            if (in_array($key, $fieldNames) && in_array($cmf->getMetadataFor($entityName)->getTypeOfField($key), array('date', 'datetime'))) {
                $value = \DateTime::createFromFormat('d/m/Y', $value);
                $entity->$set($value);
            } elseif (in_array($key, $fieldNames)) {
                $entity->$set($value);
            } elseif (in_array($key, $associationNames)) {
                $class = $cmf->getMetadataFor($entityName)->getAssociationTargetClass($key);
                $value = $this->getEntityManager()->getRepository($class)->find($value);
                $entity->$set($value);
            } else {
                continue;
                //                 throw new \Exception("attr {$key} desconhecido");
            }
        }
        return $entity;
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
     * @param Entity $entity
     * @return Entity $entity
     */
    public function add(ServiceData $sd)
    {
        $entity = $sd->get('object');
        $this->validate($entity, array(
            'registration'
        ));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     *
     * @param Entity $entity
     * @return Entity $entity
     */
    public function modify(ServiceData $sd)
    {
        $entity = $sd->get('object');
        $this->validate($entity, array(
            'edition'
        ));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     *
     * @param Entity $entity
     * @return Entity $entity
     */
    public function remove(ServiceData $sd)
    {
        $entity = $sd->get('object');
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }
}