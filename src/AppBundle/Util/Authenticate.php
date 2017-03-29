<?php

namespace AppBundle\Util;

use Google_Client;
use Google_Service_Oauth2;
use Google_Service_Directory;

class Authenticate
{
    private $user;
    private $doctrine;

    /**
     * Constructor method
     * @param $user    - Entity\Users object of the login user
     * @param $session - Symfony session object
     */
    public function __construct($user, $doctrine)
    {
        $this->user     = $user;
        $this->doctrine = $doctrine;
    }

    /**
     * Authenticate login user
     * @param $permission - Page permission string (Ex. can_manage)
     */
    public function authenticate($permission) {
        if ($this->checkPermission($permission)) {
            return true;
        }
        else {
            return false;
        }
    }

    public function checkPermission($perm) {
        $user_permissions = $this->doctrine
                                ->getRepository('AppBundle:UserPermissions')
                                ->findByUser($this->user);

        $permissions = [];
        foreach ($user_permissions as $permission) {
            $permissions[$permission->getPermission()->getPermission()] = true;
        }

        if (isset($permissions[$perm])) {
            return $permissions[$perm];
        }
        else {
            return false;
        }
    }
}
