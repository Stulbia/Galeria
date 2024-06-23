<?php
/**
 * Photo service.
 */

namespace App\Service;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\User;
use App\Repository\PhotoRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PhotoService.
 */
class PhotoService implements PhotoServiceInterface
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
     * @param PhotoRepository     $photoRepository Photo repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(private readonly PhotoRepository $photoRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedUserList(int $page, User $author): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->photoRepository->queryByAuthor($author),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->photoRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }


    /**
     * Save entity.
     *
     * @param Photo $photo Photo entity
     */
    public function save(Photo $photo): void
    {
        $this->photoRepository->save($photo);
    }

    /**
     * Delete entity.
     *
     * @param Photo $photo Photo entity
     */
    public function delete(Photo $photo): void
    {
        $this->photoRepository->delete($photo);
    }

    public function findByGallery(Gallery $gallery, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->photoRepository->findByGallery($gallery),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );

    }

}