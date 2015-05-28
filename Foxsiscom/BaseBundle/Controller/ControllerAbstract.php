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

    /**
     *
     * @param array $arrayData
     * @return array
     */
    protected function filterEmpty($arrayData)
    {
        return array_filter($arrayData, function ($var) {
            return ! ($var === '');
        });
    }

    /**
     *
     * @param Entity $entity
     * @param array $array
     * @throws \Exception
     * @return Entity $entity
     */
    public function bindEntity($entity, array $array)
    {
        $array = $this->filterEmpty($array);
        $reflectionClass = new \Foxsiscom\BaseBundle\Reflection\ReflectionClass($entity);
        $doctrineMetadata = $this->getDoctrine()->getEntityManager()->getMetadataFactory()->getMetadataFor($reflectionClass->getName());
        $fieldNames = $doctrineMetadata->getFieldNames();

        foreach ($array as $key => $value) {
            $set = 'set' . ucfirst($key);
            $add = 'add' . ucfirst($key);
            if (in_array($key, $fieldNames)) {
                if (in_array($doctrineMetadata->getTypeOfField($key), array(
                    'date',
                    'datetime'
                ))) {
                    $value = \DateTime::createFromFormat('d/m/Y', $value);
                }
                $entity->$set($value);
            } elseif ($reflectionClass->hasMethod($set)) {
                $class = $reflectionClass->getFirstParameterClass($reflectionClass->getMethod($set));
                $object = $this->getDoctrine()->getEntityManager()->getRepository($class)->find($value);
                $entity->$set($object);
            } elseif ($reflectionClass->hasMethod($add)) {
                foreach ($value as $id) {
                    $class = $reflectionClass->getFirstParameterClass($reflectionClass->getMethod($add));
                    $object = $this->getDoctrine()->getEntityManager()->getRepository($class)->find($id);
                    $entity->$add($object);
                }
            } else {
                throw new \Exception("Classe " . get_class($entity) . " não possui método " . $set . ", nem método " . $add . " para o campo " . $key . ".");
            }
        }
        return $entity;
    }

    public function getRepository($entityName)
    {
        return $this->getDoctrine()->getManager()->getRepository($entityName);
    }
}