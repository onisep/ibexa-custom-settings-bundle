<?php

namespace Onisep\IbexaCustomSettingsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class LocationSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('settings', CollectionType::class, [
                'label' => 'Paramètre',
                'entry_type' => SettingType::class,
                'entry_options' => [
                    'can_edit_keys' => $options['data']['can_edit_keys'] ?? true,
                    'can_edit_values' => $options['data']['can_edit_values'] ?? true,
                ],
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ], )
            ->add('update', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }
}
