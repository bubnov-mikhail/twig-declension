<?php

namespace BubnovKelnik\TwigDeclensionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterDeclensionFormType extends AbstractType
{
    const FORM_NAME = 'bubnovkelnik_twigdeclensionbundle_find';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('infinitive', 'text', [
                'label' => 'twig-declension.forms.inf',
                'required' => false,
            ])
            ->add('needs_work', 'checkbox', [
                'label' => 'twig-declension.needs_work',
                'data' => false,
                'required' => false,
            ])
            ->setMethod('POST')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return self::FORM_NAME;
    }
}
