<?php
/**
 * Photo fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Gallery;
use App\Entity\Enum\PhotoStatus;
use App\Entity\Tag;
use App\Entity\Photo;
//use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class PhotoFixtures.
 */
class PhotoFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullPropertyFetch
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(100, 'Photos', function (int $i) {
            $photo = new Photo();
            $photo->setTitle($this->faker->sentence);
            $photo->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $photo->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            /** @var Gallery $Gallery */
            $Gallery = $this->getRandomReference('categories');
            $photo->setGallery($Gallery);

            return $photo;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: GalleryFixtures::class}
     */
    public function getDependencies(): array
    {
        return [GalleryFixtures::class];
    }
}