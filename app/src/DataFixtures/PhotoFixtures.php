<?php

/**
 * Photo fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Enum\PhotoStatus;
use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class PhotoFixtures.
 *
 * @psalm-suppress MissingConstructor
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

        $this->createMany(20, 'photos', function (int $i) {
            $photo = new Photo();
            $photo->setTitle($this->faker->sentence);
            $photo->setFilename(sprintf('%d.jpg', $i % 20));
            $photo->setDescription($this->faker->sentence);
            $photo->setStatus(PhotoStatus::PUBLIC);
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
            /** @var Gallery $gallery */
            $gallery = $this->getRandomReference('galleries');
            $photo->setGallery($gallery);

            /** @var array<array-key, Tag> $tags */
            $tags = $this->getRandomReferences(
                'tags',
                $this->faker->numberBetween(0, 5)
            );
            foreach ($tags as $tag) {
                $photo->addTag($tag);
            }

            // $photo->setStatus(PhotoStatus::from($this->faker->numberBetween(1, 2)));

            /** @var User $author */
            $author = $this->getRandomReference('users');
            $photo->setAuthor($author);

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
