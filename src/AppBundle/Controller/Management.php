<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;

use AppBundle\Entity\Users;
use AppBundle\Entity\UserPermissions;
use AppBundle\Util\Authenticate;
use AppBundle\Form\UserManagement;

use AppBundle\Model\User;

use Google_Client;
use Google_Service_Oauth2;
use Google_Service_Directory;

class Management extends Controller
{
    /**
     * @Route("/management", name="management")
     */
    public function managementAction()
    {
        $user_model = new User($this->getDoctrine());
        $login_user = $user_model->findOneByEmailAddress($this->get('session')->get('email'));

        $auth = new Authenticate($login_user, $this->getDoctrine());

        if ($auth->checkPermission('can_manage')) {
            $em    = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT u FROM AppBundle:Users u"); 
            $users = $query->getResult();

            $user_permissions = [];

            foreach ($users as $user) {
                $permissions = $this->getDoctrine()
                                    ->getRepository('AppBundle:UserPermissions')
                                    ->findBy(['user' => $user]);

                $user_permissions[$user->getUserId()] = [
                    'user'  => $user,
                    'perms' => $permissions
                ];
            }

            return $this->render('inventory/management/index.html.twig', array(
                'perms'       => $user_permissions,
                'user'        => $login_user,
                'auth'        => $auth
            ));
        }
        else {
            return $this->redirectToRoute("homepage");
        }
    }

    /**
     * @Route("/management/user/{id}", name="user")
     */
    public function userAction($id, Request $request)
    {
        $user_model = new User($this->getDoctrine());
        $user       = $user_model->findOneByEmailAddress($this->get('session')->get('email'));

        $auth = new Authenticate($user, $this->getDoctrine());

        if ($auth->checkPermission('can_manage')) {
            $edit_user = $this->getDoctrine()
                            ->getRepository('AppBundle:Users')
                            ->find($id);
            
            $permissions = $this->getDoctrine()
                            ->getRepository('AppBundle:UserPermissions')
                            ->findBy(['user' => $edit_user]);

            if ($edit_user) {
                $form = $this->createForm(UserManagement::class);

                $form->handleRequest($request);
                
                if ($form->isValid()) {
                    $form_data = $form->getData();

                    foreach ($form_data as $key => $value) {
                        $permission = $this->getDoctrine()
                                            ->getRepository('AppBundle:Permissions')
                                            ->findOneByPermission($key);

                        $user_permission = $this->getDoctrine()
                                                ->getRepository('AppBundle:UserPermissions')
                                                ->findOneBy([
                                                    'user'       => $edit_user,
                                                    'permission' => $permission
                                                ]);

                        if ($form_data[$key]) {
                            //If checked and user permission is not yet created, We create it. 
                            if (!$user_permission) {
                                $new_user_permission = new UserPermissions();

                                $new_user_permission->setUser($edit_user);
                                $new_user_permission->setPermission($permission);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($new_user_permission);
                                $em->flush();
                            }
                        }
                        else {
                            //If unchecked and user permission exists, We remove it. 
                            if ($user_permission) {
                                $em = $this->getDoctrine()->getManager();
                                $em->remove($user_permission);
                                $em->flush();
                            }
                        }
                    }
            
                    $this->addFlash(
                        'notice',
                        'Item successfully added.'
                    );

                    return $this->redirectToRoute("management");
                }

                return $this->render('inventory/management/user.html.twig', array(
                    'edit_user'   => $edit_user,
                    'permissions' => $permissions,
                    'user'        => $user,
                    'form'        => $form->createView(),
                    'man_active_nav' => 'active',
                    'auth'         => $auth
                ));
            }
            else {
                return $this->redirectToRoute("homepage");
            }
        }
        else {
            return $this->redirectToRoute("homepage");
        }
    }
}
