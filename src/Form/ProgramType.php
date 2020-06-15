<?php

namespace App\Form;

use App\Entity\Actor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Program;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'=>'Titre', TextType::class,
                'attr' => ['class' => 'form-title'],
            ])
            ->add('summary', TextType::class, ['label' => 'Résumé'])
            ->add('poster', TextType::class)
            ->add('category', TextType::class, ['choice_label'=>'name', 'label' =>'Catégorie'])
            ->add('actors', EntityType::class, [
                'class' => Actor::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'by_reference' => false,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
