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
use AppBundle\Form\CreateInventory;
use AppBundle\Model\DyObject;

use AppBundle\Model\User;

class Inventory extends Controller
{
    /**
     * @Route("/inventory", name="inventory")
     */
    public function inventoryAction(Request $request)
    {
        $user_model = new User($this->getDoctrine());
        $user       = $user_model->findOneByEmailAddress($this->get('session')->get('email'));

        $auth = new Authenticate($user, $this->getDoctrine());

        if ($auth->checkPermission('can_view')) {
            $class = ucfirst($this->get('session')->get('inventory'));
           
            if ($class) {
                $inventories = $this->getDoctrine()
                                ->getRepository('AppBundle:'.$class)
                                ->findAll();

                $result = [];
                $dyobject = new DyObject($this->getDoctrine());

                foreach ($inventories as $inventory) {
                    $getters = [];
                    foreach ($dyobject->displayGetters($inventory) as $getter) {
                        $value = $inventory->$getter();

                        if ($value instanceof \DateTime) {
                            $getters[] = $value->format('Y-m-d');
                        }
                        else {
                            $getters[] = $value;
                        }
                    }

                    $get_id = $dyobject->getId($inventory);
                    $result[$inventory->$get_id()] = $getters;
                }
            
                $inventory_class = 'AppBundle\Entity\\'.$class;
                $inventory       = new $inventory_class();
                $headers         = $dyobject->displayHeaders($inventory);

                return $this->render('inventory/index.html.twig', [
                    'user' => $user,
                    'auth' => $auth,
                    'inventories' => $result,
                    'headers'     => $headers,
                    'nosort'      => count($headers)
                ]);
            }
            else {
                return $this->redirectToRoute("setup");
            }
        }
        else {
            return $this->redirectToRoute("index");
        }   
    }

    /**
     * @Route("/inventory/switch", name="inventoryswitch")
     */
    public function inventoryswitchAction(Request $request)
    {
        $user_model = new User($this->getDoctrine());
        $user       = $user_model->findOneByEmailAddress($this->get('session')->get('email'));

        $auth = new Authenticate($user, $this->getDoctrine());

        if ($auth->checkPermission('can_view')) {
            $this->get('session')->set('inventory', $request->query->get('id'));
        }
        
        return $this->redirectToRoute("index");
    }

    /**
     * @Route("/inventory/create", name="inventorycreate")
     */
    public function inventorycreateAction(Request $request)
    {
        $user_model = new User($this->getDoctrine());
        $user       = $user_model->findOneByEmailAddress($this->get('session')->get('email'));

        $auth = new Authenticate($user, $this->getDoctrine());

        if ($auth->checkPermission('can_add')) {
            $class     = 'AppBundle\Entity\\'.ucfirst($this->get('session')->get('inventory'));
            $dyobject  = new DyObject($this->getDoctrine());
            $inventory = new $class(); 

            $fields = $dyobject->getFields($inventory);

            $form = $this->createForm(CreateInventory::class, ['fields' => $fields]);
            $errors = [];

            $form->handleRequest($request);

            if ($form->isValid()) {
                $setters = $dyobject->setters($inventory);
                $params  = $request->request->keys();

                array_pop($params);

                foreach ($params as $index => $key) {
                    $setter = $setters[$index];
                    $value  = $request->request->get($key);
                    $field  = $fields[$index][0];

                    if ($value && $field->type == 'date') {
                        $value = date_create_from_format('Y-m-d', $value);
                    }

                    $inventory->$setter($value);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($inventory);
                $em->flush();

                $this->addFlash(
                    'notice',
                    'Item successfully added.'
                );

                return $this->redirectToRoute("index");
            }

            return $this->render('inventory/create.html.twig', [
                'user'   => $user,
                'form'   => $form->createView(),
                'errors' => $errors,
                'auth'   => $auth,
                'action' => $this->generateUrl('inventorycreate')
            ]);
        }
        else {
            return $this->redirectToRoute("index");
        }   
    }

    /**
     * @return RedirectResponse|Response
     *
     * @Route("/inventory/delete/{id}", name="deleteitem")
     */
    public function deleteitemAction($id, Request $request)
    {
        $user_model = new User($this->getDoctrine());
        $user       = $user_model->findOneByEmailAddress($this->get('session')->get('email'));

        $auth = new Authenticate($user, $this->getDoctrine());

        if ($auth->checkPermission('can_add')) {
            $class = ucfirst($this->get('session')->get('inventory'));
            $inventory = $this->getDoctrine()
                                ->getRepository('AppBundle:'.$class)
                                ->find($id);

            $em = $this->getDoctrine()->getManager();
            $em->remove($inventory);
            $em->flush();
            
            
            $this->addFlash(
                'notice',
                'Item successfully deleted.'
            );

            return $this->redirectToRoute("index");
        }
        else {
            return $this->redirectToRoute("index");
        }
    }

    /**
     * @return RedirectResponse|Response
     *
     * @Route("/inventory/update/{id}", name="updateitem")
     */
    public function updateitemAction($id, Request $request)
    {
        $user_model = new User($this->getDoctrine());
        $user       = $user_model->findOneByEmailAddress($this->get('session')->get('email'));

        $auth = new Authenticate($user, $this->getDoctrine());

        if ($auth->checkPermission('can_add')) {
            $class = ucfirst($this->get('session')->get('inventory'));
            $inventory = $this->getDoctrine()
                                ->getRepository('AppBundle:'.$class)
                                ->find($id);
                                //TODO here.....

            $em = $this->getDoctrine()->getManager();
            $em->persist($inventory);
            $em->flush();
            
            
            $this->addFlash(
                'notice',
                'Item successfully updated.'
            );

            return $this->redirectToRoute("index");
        }
        else {
            return $this->redirectToRoute("index");
        }
    }
}
