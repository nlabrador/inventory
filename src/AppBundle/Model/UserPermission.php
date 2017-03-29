<?php

namespace AppBundle\Model;

class UserPermission extends Common
{
    public function __construct($doctrine) {
        parent::__construct($doctrine, $doctrine->getRepository('AppBundle:UserPermissions'));
    }

    public function findByUser($user) {
        return $this->getRepository()->findByUser($user);
    }
}
