<?php
/**
 * Gallery service.
 */

namespace App\Service;

use App\Entity\Gallery;
//use App\Form\Type\GalleryType;
use App\Repository\GalleryRepository;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class GalleryService.
 */
class GalleryService implements GalleryServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param GalleryRepository $galleryRepository Gallery repository
     * @param PaginatorInterface $paginator      Paginator
     * @param PhotoRepository $photoRepository Photo repository
     */
    public function __construct(private readonly GalleryRepository $galleryRepository,
                                private readonly PaginatorInterface $paginator,
                                private readonly PhotoRepository $photoRepository)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->galleryRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }


    /**
     * Save entity.
     *
     * @param Gallery $gallery Gallery entity
     *
     */
    /**
     * Save entity.
     *
     * @param Gallery $gallery Gallery entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Gallery $gallery): void
    {
//        if (null === $gallery->getId()) {
//            $gallery->setCreatedAt(new \DateTimeImmutable());
//        }
//        $gallery->setUpdatedAt(new \DateTimeImmutable());

        $this->galleryRepository->save($gallery);


    }


    public function delete(Gallery $gallery): void
    {
        $this->galleryRepository->delete($gallery);

    }

    /**
     * Can Gallery be deleted?
     *
     * @param Gallery $gallery Gallery entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Gallery $gallery): bool
    {
        try {
            $result = $this-> photoRepository ->countByGallery($gallery);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }





}
