<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;

use AppBundle\Entity\Users;
use AppBundle\Entity\UserPermissions;
use AppBundle\Util\Authenticate;
use AppBundle\Util\SetupTable;
use AppBundle\Util\Config;
use AppBundle\Form\UserManagement;

use AppBundle\Model\User;

use Google_Client;
use Google_Service_Oauth2;
use Google_Service_Directory;

class Setup extends Controller
{
    /**
     * @Route("/setup", name="setup")
     */
    public function setupAction()
    {
        $user_model = new User($this->getDoctrine());
        $login_user = $user_model->findOneByEmailAddress($this->get('session')->get('email'));

        $auth = new Authenticate($login_user, $this->getDoctrine());

        if ($auth->checkPermission('can_manage')) {
            return $this->render('inventory/setup/index.html.twig', array(
                'user'        => $login_user,
                'auth'        => $auth
            ));
        }
        else {
            return $this->redirectToRoute("index");
        }
    }

    /**
     * @Route("/setup/save", name="setupsave")
     */
    public function saveAction(Request $request)
    {
        $user_model  = new User($this->getDoctrine());
        $user        = $user_model->findOneByEmailAddress($this->get('session')->get('email'));

        $auth = new Authenticate($user, $this->getDoctrine());

        if ($auth->checkPermission('can_manage') && $request->request->get('table') && $request->request->get('fields')) {
            $config = new Config($this->container->get('kernel')->getRootDir() . '/config/parameters.yml');

            $parameters = $config->parseConfig();
            $parameters = $parameters['parameters'];
            $table_name = preg_replace('/\s+$/', '', $request->request->get('table'));
            $table_name = preg_replace('/^\s+/', '', $table_name);
            $table_name = preg_replace('/\s/', '', strtolower($table_name));

            if (isset($parameters['inventories'][$table_name]) || preg_match('/^permissions|users$/', $table_name)) {
                return new JsonResponse(['error' => 1]);
            }
            else {
                $fields = [];
                foreach ($request->request->get('fields') as $field) {
                    $field_data = explode(':', $field);

                    $fields[] = [
                        'field_name' => $field_data[0],
                        'field_type' => $field_data[1]
                    ];
                }


                $setup = new SetupTable($fields, $table_name);
                $setup->createTable($this->getDoctrine());
                $setup->createEntity($this->container->get('kernel')->getRootDir() . '/../');

                $inventories = $parameters['inventories'];
                $inventories[$table_name] = $request->request->get('table');

                $config->updateConfig('inventories', $inventories);

                $this->addFlash(
                    'notice',
                    'Succcess'
                 );
                
                $session_inventories = $this->get('session')->get('inventories');
                if ($session_inventories) {
                    $session_inventories[$table_name] = $request->request->get('table');
                }
                else {
                    $session_inventories = [
                        $table_name => $request->request->get('table')
                    ];
                }

                $this->get('session')->set('inventories', $session_inventories);

                return new JsonResponse('success');
            }
        }
        else {
            return new JsonResponse();
        }   
    }
}
