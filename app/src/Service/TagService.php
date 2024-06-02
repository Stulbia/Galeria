<?php
/**
 * Tag service.
 */

namespace App\Service;

use App\Entity\Tag;
//use App\Form\Type\TagType;
use App\Repository\TagRepository;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class TagService.
 */
class TagService implements TagServiceInterface
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
     * @param TagRepository $tagRepository Tag repository
     * @param PaginatorInterface $paginator  Paginator
     * @param PhotoRepository $photoRepository Photo repository
     */
    public function __construct(private readonly TagRepository $tagRepository,
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
            $this->tagRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }


    /**
     * Save entity.
     *
     * @param Tag $tag Tag entity
     *
     */
    /**
     * Save entity.
     *
     * @param Tag $tag Tag entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Tag $tag): void
    {
//        if (null === $tag->getId()) {
//            $tag->setCreatedAt(new \DateTimeImmutable());
//        }
//        $tag->setUpdatedAt(new \DateTimeImmutable());

        $this->tagRepository->save($tag);


    }


    public function delete(Tag $tag): void
    {
        $this->tagRepository->delete($tag);

    }

    /**
     * Can Tag be deleted?
     *
     * @param Tag $tag Tag entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Tag $tag): bool
    {
        try {
            $result = $this-> photoRepository ->countByTag($tag);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }





}
