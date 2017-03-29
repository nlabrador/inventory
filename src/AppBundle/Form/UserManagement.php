<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserManagement extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('can_view', CheckboxType::class, array(
                'attr'    => array(
                    'class' => 'mdl-switch__input',
                ),
                'label'   => 'Can View Inventory',
                'required' => false 
            ))
            ->add('can_add', CheckboxType::class, array(
                'attr'    => array(
                    'class' => 'flat-red mdl-switch__input',
                ),
                'label'   => 'Can Add/Edit/Delete Inventory',
                'required' => false 
            ))
            ->add('can_manage', CheckboxType::class, array(
                'attr'    => array(
                    'class' => 'flat-red mdl-switch__input',
                ),
                'label'   => 'Can Manage',
                'required' => false 
            ))
        ;
    }

    public function getName()
    {
        return 'usermanagement';
    }
}
