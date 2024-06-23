<?php
/**
 * Photo service interface.
 */

namespace App\Service;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface PhotoServiceInterface.
 */
interface PhotoServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;
    public function findByGallery(Gallery $gallery, int $page): PaginationInterface;

    public function getPaginatedUserList(int $page, User $author): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Photo $photo Photo entity
     */
    public function save(Photo $photo): void;

    /**
     * Delete entity.
     *
     * @param Photo $photo Photo entity
     */
    public function delete(Photo $photo): void;
}