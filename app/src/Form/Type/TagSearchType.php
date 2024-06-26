<?php

namespace App\Form\Type;

use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('tags', EntityType::class, [
        'class' => Tag::class,
        'choice_label' => 'title',
        'multiple' => true,
        'expanded' => true,
        ])
        ->add('search', SubmitType::class, [
        'label' => 'Search',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
// Konfiguracja formularza
        ]);
    }
}
