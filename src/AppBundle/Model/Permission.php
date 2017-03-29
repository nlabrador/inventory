<?php

namespace AppBundle\Model;

class Permission extends Common
{
    public function __construct($doctrine) {
        parent::__construct($doctrine, $doctrine->getRepository('AppBundle:Permissions'));
    }

    public function findOneByPermission($perm) {
        return $this->getRepository()->findOneByPermission($perm);
    }
}
