<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Yaml\Yaml;

use Google_Client;
use Google_Service_Oauth2;
use Google_Service_Directory;

use AppBundle\Model\User;
use AppBundle\Model\Permission;

use AppBundle\Entity\Users;
use AppBundle\Entity\Permissions;
use AppBundle\Entity\UserPermissions;
use AppBundle\Util\Authenticate;
use AppBundle\Util\Config;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
        if ($this->get('session')->get('email')) {
            return $this->redirectToRoute("inventory");
        }
        else {
            return $this->render('default/index.html.twig');
        }
    }

    /**
     * @return RedirectResponse|Response
     *
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        if ($request->headers->get('referer')) {
            return $this->redirectToRoute("oauth2callback");
        }
        else {
            return $this->redirectToRoute("index");
        }
    }

    /** 
     * @Route("/oauth2callback", name="oauth2callback")
     * Google Auth callback function to Authenticate User
     */
    public function oauth2callbackAction(Request $request) {
        $client = new Google_Client();
        $client->setAuthConfigFile('/etc/hiredevs_client_secrets.json');
        $client->setScopes(array(
            Google_Service_Oauth2::USERINFO_PROFILE,
            Google_Service_Oauth2::USERINFO_EMAIL,
        )); 

        if (! isset($_GET['code'])) {
            $client_url = $client->createAuthUrl();
            return $this->redirect($client->createAuthUrl());
        } else {
            if ($request->getHttpHost() == $this->container->getParameter('domain')) {
                //TODO create google console for this and create new /etc/inventory_client_secrets.json

                $referer_domain = preg_replace('/^.*:\/\//', '', $request->headers->get('referer'));
                $referer_domain = preg_replace('/\/$/', '', $referer_domain);

                $uri = preg_replace("/{$request->getHttpHost()}/", $referer_domain, $request->getUri()); 

                return $this->redirect($uri);
            }
            
            try {
                $client->authenticate($_GET['code']);
            } catch (\Exception $e) {
                return $this->redirectToRoute("index");
            }   

            $user_profile = new Google_Service_Oauth2($client);
            $user = $user_profile->userinfo->get();

            $email = $user->getEmail();
            if (preg_match($this->container->getParameter('pregmatch_valid_emails'), $email)) {
                if ($this->validateCreate($email, $user->getName())) {
                    $this->get('session')->set('access_token', $client->getAccessToken());
                    $this->get('session')->set('user', $user->getName());
                    $this->get('session')->set('email', $email);
            
                    $config = new Config($this->container->get('kernel')->getRootDir() . '/config/parameters.yml');
                    $parameters = $config->parseConfig();

                    $inventories = $parameters['parameters']['inventories'];

                    if (count($inventories) == 0) {
                        return $this->redirectToRoute("setup");
                    }
                    
                    $this->get('session')->set('inventories', $inventories);
    
                    if (count($inventories) > 1) {
                        return $this->redirectToRoute("inventorychoose");
                    }
                    else {
                        $this->get('session')->set('inventory', key($inventories));
                        
                        return $this->redirectToRoute("index");
                    }
                }
                else {
                    return $this->redirectToRoute("index");
                }
            }
            else {
                return $this->redirectToRoute("index");
            }
        }
    }

    /**
     * Validate and create into our users table.
     */
    public function validateCreate($email, $name) {
        $user_model = new User($this->getDoctrine());
        $user       = $user_model->findOneByEmailAddress($email);

        if ($user) {
            $auth = new Authenticate($user, $this->getDoctrine());

            if ($auth->checkPermission('can_view_applicants')) {
                return true;
            }
            else {
                return $this->redirectToRoute("index");
            }
        }
        else {
            //Save user to users table
            $user = new Users();
            $permission_model = new Permission($this->getDoctrine());

            $user->setEmailAddress($email);
            $user->setName($name);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $user_permission = new UserPermissions();
            $permission = $permission_model->findOneByPermission('can_view');

            $user_permission->setUser($user);
            $user_permission->setPermission($permission);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user_permission);
            $em->flush();

            if (preg_match($this->container->getParameter('pregmatch_default_admins'), $email)) {
                $user_permission = new UserPermissions();
                $permission = $permission_model->findOneByPermission('can_add');

                $user_permission->setUser($user);
                $user_permission->setPermission($permission);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user_permission);
                $em->flush();

                $user_permission = new UserPermissions();
                $permission = $permission_model->findOneByPermission('can_manage');

                $user_permission->setUser($user);
                $user_permission->setPermission($permission);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user_permission);
                $em->flush();
            }

            return true;
        }
    }

    /**
     * @return RedirectResponse|Response
     *
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        $this->get('session')->set('access_token', null);
        $this->get('session')->set('user', null);
        $this->get('session')->set('email', null);
        $this->get('session')->set('inventories', null);
        $this->get('session')->set('inventory', null);

        return $this->redirectToRoute("index");
    }
}
