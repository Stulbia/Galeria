<?php
/**
 * \App\Entity\User type.
 */
namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserUpdateType.
 */
class UserUpdateType extends AbstractType
{
/**
* Constructor.
*
 * @param FormBuilderInterface $builder The form builder*
 * @param array<string, mixed> $options Form options
 *
*/
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
        ->add('email', EmailType::class, [
            'constraints' => [
            new NotBlank([
                'message' => 'New email',
            ]),
            ],
        ]);
//        ->add('roles', ChoiceType::class, [
//            'choices' => [
//            'User' => 'ROLE_USER',
//            'Admin' => 'ROLE_ADMIN',
//            ],
//            'multiple' => true,
//            'expanded' => true,
//            'label' => 'Roles',
////        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
