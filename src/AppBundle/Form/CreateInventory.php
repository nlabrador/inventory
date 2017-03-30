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
        $inventory = isset($options['data']['inventory']) ? $options['data']['inventory'] : null;
        $getters   = isset($options['data']['getters']) ? $options['data']['getters'] : null;

        foreach ($options['data']['fields'] as $key => $field) {
            $field = $field[0];
            $label = ucfirst($field->name);
            $value = '';

            if ($inventory && $getters) {
                $getter = isset($getters[$key]) ? $getters[$key]->name : null;

                if ($getter) {
                    $value = $inventory->$getter();
                }
            }

            if (!preg_match('/_id/', $field->name)) {
                $class = 'form-control input-md';

                if ($field->type == 'date') {
                    $label .= ' (yyyy-mm-dd)';
                    $class .= ' datefield';

                    if ($value) {
                        $value = $value->format('Y-m-d');
                    }
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
                            'placeholder' => $label,
                            'value'       => $value
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
