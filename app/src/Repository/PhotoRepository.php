<?php
/**
 * Photo repository.
 */

namespace App\Repository;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PhotoRepository.
 *
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Photo>
 */
class PhotoRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial photo.{id, createdAt, updatedAt, title, filename}',
                'partial gallery.{id, title}'
            )
            ->join('photo.gallery', 'gallery')
            ->orderBy('photo.updatedAt', 'DESC');
    }

    /**
     * Count photos by gallery.
     *
     * @param Gallery $gallery Gallery
     *
     * @return int Number of photos in gallery
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByGallery(Gallery $gallery): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('photo.id'))
            ->where('photo.gallery = :gallery')
            ->setParameter(':gallery', $gallery)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Select photos from gallery.
     *
     * @param Gallery $gallery Gallery
     *
     * @return QueryBuilder Query builder
     *
     * @throws NoResultException
     */
    public function findByGallery($gallery):QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        $queryBuilder->andWhere('photo.gallery = :gallery')
            ->setParameter('gallery', $gallery);

        return $queryBuilder;
    }

    // ...
    /**
     * Query tasks by author.
     *
     * @param User $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(User $user): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        $queryBuilder->andWhere('photo.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }
//    /**
//     * Select photos by Tags.
//     *
//     * @param Gallery $gallery Gallery
//     *
//     * @return QueryBuilder Query builder
//     *
//     * @throws NoResultException
//     */
//    public function findByTag($tag):QueryBuilder
//    {
//        return $this->createQueryBuilder('photo')
//            ->select('partial photo.{id, createdAt, updatedAt, title}')
//            ->join('photo.tags', 'tag')
//            ->where('tag.id = :tag')
//            ->setParameter('tag', $tag);
//    }

    /**
     * Save entity.
     *
     * @param Photo $photo Photo entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Photo $photo): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($photo);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Photo $photo Photo entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Photo $photo): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($photo);
        $this->_em->flush();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('photo');
    }

}
