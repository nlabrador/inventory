<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class CreateInventory extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $now = new \DateTime();

        foreach ($options['data']['fields'] as $field) {
            $field = $field[0];
            $label = ucfirst($field->name);

            if (!preg_match('/_id/', $field->name)) {
                $class = 'form-control input-md';

                if ($field->type == 'date') {
                    $label .= ' (yyyy-mm-dd)';
                    $class .= ' datefield';
                }
                elseif ($field->type == 'decimal') {
                    $label .= ' (100.00)';
                }
                elseif ($field->type == 'integer') {
                    $label .= ' (20)';
                }
                else {
                    $label .= ' (Text input)';
                }

                $builder
                    ->add($field->name, TextType::class, [
                        'attr'    => [
                            'class'       => $class,
                            'placeholder' => $label
                        ]
                ]);
            }
        }

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $event->stopPropagation();
        }, 900);
    }

    public function getName()
    {
        return 'create_inventory';
    }
}
