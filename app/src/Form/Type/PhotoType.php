<?php
/**
 * Photo type.
 */

namespace App\Form\Type;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Form\DataTransformer\TagsDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PhotoType.
 */
class PhotoType extends AbstractType
{
    /**
     * Constructor.
     *
     * @param TagsDataTransformer $tagsDataTransformer Tags data transformer
     */
    public function __construct(private readonly TagsDataTransformer $tagsDataTransformer)
    {
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'label.title',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]);
        $builder->add(
            'gallery',
            EntityType::class,
            [
                'class' => Gallery::class,
                'choice_label' => function ($gallery): string {
                    return $gallery->getTitle();
                },
                'label' => 'label.gallery',
                'placeholder' => 'label.none',
                'required' => true,
            ]
        );
        $builder->add(
            'tags',
            TextType::class,
            [
                'label' => 'label.tags',
                'required' => false,
                'attr' => ['max_length' => 128],
            ]
        );

        $builder->get('tags')->addModelTransformer(
            $this->tagsDataTransformer
        );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Photo::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'photo';
    }




}
