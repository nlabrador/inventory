<?php

namespace AppBundle\Model;

class Common
{
    /**
     * $repository  object - Entity
     */
    private $repository;

    /**
     * Doctrine\Bundle\DoctrineBundle\Registry object
     */
    private $doctrine;

    /**
     * Constructor method
     *
     * @param $doctrine  - Doctrine\Bundle\DoctrineBundle\Registry object
     * @param $repository - Repository object
     */
    public function __construct($doctrine, $repository)
    {
        $this->doctrine   = $doctrine;
        $this->repository = $repository;
    }

    /**
     * Retrieve all data in a table
     *
     * @return - Array of Entity object
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }
    
    /**
     * Retrieve a single Entity object
     *
     * @param $id - Primary Id
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Save new entry to table
     *
     * @param $object - Entity object
     */
    public function upsert($object)
    {
         $em = $this->doctrine->getManager();

         $em->persist($object);
         $em->flush();

         return $object;
    }

    public function getDoctrine() {
        return $this->doctrine;
    }

    public function getRepository() {
        return $this->repository;
    }

    /**
     * Retrieve a single Entity object
     *
     * @param $fields - key/value fields
     */
    public function findOneBy($fields = [])
    {
        return $this->repository->findOneBy($fields);
    }

    /**
     * Retrieve list of data with limit and order
     * @param $params - Same format with Doctrine findBy query
     */
    public function findBy($params, $order = [])
    {
        return $this->repository->findBy($params, $order);        
    }

    /**
     * Remove entry from table
     *
     * @param $object - Entity object
     */
    public function remove($object)
    {
         $em = $this->doctrine->getManager();

         $em->remove($object);
         $em->flush();
    }
}
