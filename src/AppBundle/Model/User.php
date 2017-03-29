<?php

namespace AppBundle\Model;

class User extends Common
{
    public function __construct($doctrine) {
        parent::__construct($doctrine, $doctrine->getRepository('AppBundle:Users'));
    }

    public function findOneByEmailAddress($email_address) {
        return $this->getRepository()->findOneByEmailAddress($email_address);
    }
}
