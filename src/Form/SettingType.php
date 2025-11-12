<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle\Form;

use Ibexa\AdminUi\Form\Type\UniversalDiscoveryWidget\UniversalDiscoveryWidgetType;
use Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $keyFieldOptions = ['label' => 'Clé', 'required' => true, 'setter' => $this->generateSlug(...)];
        if (!$options['can_edit_keys']) {
            $keyFieldOptions['disabled'] = true;
            $keyFieldOptions['attr'] = ['readonly' => true];
            unset($keyFieldOptions['required']);
        }

        $valueFieldOptions = ['label' => 'Valeur', 'attr' => ['rows' => 1,'style'=>'height:48px' ], 'required' => true ];
        if (!$options['can_edit_values']) {
            $valueFieldOptions['disabled'] = true;
            $valueFieldOptions['attr']['readonly'] = true;
            unset($valueFieldOptions['required']);
        }

        $builder
            ->add('key', TextType::class, $keyFieldOptions)
            ->add('value', TextareaType::class, $valueFieldOptions)
            ->add('location', UniversalDiscoveryWidgetType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => LocationSetting::class,
                'can_edit_keys' => true,
                'can_edit_values' => true,
            ]);
    }

    public function generateSlug(LocationSetting $locationSetting, ?string $key, FormInterface $form): void
    {
        $locationSetting->setKey($key);
    }
}
