<?php
namespace Foxsiscom\BaseBundle\Service;

use Foxsiscom\BaseBundle\Service\Exception\ServiceValidationException;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\SecurityContext;

abstract class ServiceAbstract
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    protected $validator;

    protected $securityContext;

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @DI\InjectParams({"entityManager" = @DI\Inject("doctrine.orm.entity_manager")})
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
        return $this->getSecurityContext()
            ->getToken()
            ->getUser();
    }

    /**
     * @DI\InjectParams({"validator" = @DI\Inject("respect.validator")})
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     *
     * @return \Symfony\Component\Validator\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     *
     * @param string $entityName
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository($entityName)
    {
        return $this->getEntityManager()->getRepository($entityName);
    }

    /**
     * @DI\InjectParams({"securityContext" = @DI\Inject("security.context")})
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
        return $this;
    }

    /**
     *
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
     * @param ServiceData $sd
     * @return Entity $entity
     */
    public function add(ServiceData $sd)
    {
        $entity = $sd->get('entity');
        $this->validate($entity, array(
            'registration'
        ));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     *
     * @param ServiceData $sd
     * @return Entity $entity
     */
    public function modify(ServiceData $sd)
    {
        $entity = $sd->get('entity');
        $this->validate($entity, array(
            'edition'
        ));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     *
     * @param ServiceData $sd
     * @return Entity $entity
     */
    public function remove(ServiceData $sd)
    {
        $entity = $sd->get('entity');
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }
}