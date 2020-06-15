<?php

namespace App\Form;

use App\Entity\Actor;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormTypeInterface;

class ActorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class )
            ->add('programs', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Actor::class,
        ]);
    }
}
