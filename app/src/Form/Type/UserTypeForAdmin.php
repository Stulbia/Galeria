<?php

/**
 * \App\Entity\User type.
 */

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserType.
 */
class UserTypeForAdmin extends AbstractType
{
/**
* Constructor.
*
 * @param FormBuilderInterface $builder The form builder*
 * @param array<string, mixed> $options Form options
 *
*/
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'label.email',
            'constraints' => [
            new NotBlank([
                'message' => 'message.email.not_blank',
            ]),
            ],
        ])
        ->add('name', TextType::class, [
            'required' => true,
            'label' => 'label.name',
            'attr' => ['max_length' => 60],
            'constraints' => [
                new NotBlank([
                    'message' => 'message.name.not_blank',
                ]),
            ],
        ])
        ->add('roles', ChoiceType::class, [
            'label' => 'label.roles',
            'choices' => [
            'User' => 'ROLE_USER',
            'Admin' => 'ROLE_ADMIN',
            'Banned' => 'ROLE_BANNED',
            ],
            'multiple' => true,
            'expanded' => true,
        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
