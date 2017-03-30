<?php

namespace AppBundle\Model;

use Doctrine\Common\Annotations\AnnotationReader;

class DyObject
{
    /**
     * Doctrine\Bundle\DoctrineBundle\Registry object
     */
    private $doctrine;

    /**
     * Constructor method
     *
     * @param $doctrine  - Doctrine\Bundle\DoctrineBundle\Registry object
     */
    public function __construct($doctrine)
    {
        $this->doctrine   = $doctrine;
    }

    public function getFields($entity) {
        $mapped_fields = [];

        $fields = $this->doctrine->getManager()->getClassMetadata(get_class($entity))->getFieldNames();

        foreach ($fields as $field) {
            $docReader = new AnnotationReader();
            $reflect   = new \ReflectionClass($entity);

            $field_info = $docReader->getPropertyAnnotations($reflect->getProperty($field));

            $mapped_fields[] = $field_info;
        }

        return $mapped_fields;
    }

    public function getters($entity, $skipid = false) {
        $object = new \ReflectionObject($entity);
        
        $getters = [];

        foreach ($object->getMethods() as $method) {
            if ($skipid) {
                if (preg_match('/Id/', $method->name)) {
                    continue;
                }
            }

            if (preg_match('/^get/', $method->name)) {
                $getters[] = $method;
            }
        }

        return $getters;
    }

    public function displayHeaders($entity) {
        $headers = [];

        foreach ($this->getters($entity) as $getter) {
            if (!preg_match('/Id/', $getter->name)) {
                $headers[] = preg_replace('/get/', '', $getter->name);
            }
        }

        return $headers;
    }

    public function displayGetters($entity) {
        $getters = [];

        foreach ($this->getters($entity) as $getter) {
            if (!preg_match('/Id/', $getter->name)) {
                $getters[] = $getter->name;
            }
        }

        return $getters;
    }

    public function getId($entity) {
        foreach ($this->getters($entity) as $getter) {
            if (preg_match('/Id/', $getter->name)) {
                return $getter->name;
            }
        }
    }

    public function setters($entity) {
        $object = new \ReflectionObject($entity);
        
        $setters = [];

        foreach ($object->getMethods() as $method) {
            if (preg_match('/^set/', $method->name)) {
                $setters[] = $method->name;
            }
        }

        return $setters;
    }
}
