<?php

namespace Onisep\IbexaCustomSettingsBundle\Form;

use EzSystems\EzPlatformAdminUi\Form\Type\UniversalDiscoveryWidget\UniversalDiscoveryWidgetType;
use Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $keyFieldOptions = ['label' => 'Clé', 'required' => true, 'setter' => [$this, 'generateSlug']];
        if (!$options['can_edit_keys']) {
            $keyFieldOptions['disabled'] = true;
            $keyFieldOptions['attr'] = ['readonly' => true];
            unset($keyFieldOptions['required']);
        }

        $valueFieldOptions = ['label' => 'Valeur', 'attr' => ['rows' => 1], 'required' => true];
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

    public function generateSlug(LocationSetting $setting, ?string $key, FormInterface $form)
    {
        $setting->setKey($key);
    }
}
