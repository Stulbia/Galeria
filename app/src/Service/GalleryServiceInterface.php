<?php
/**
 * Gallery service interface.
 */

namespace App\Service;

use App\Entity\Gallery;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface GalleryServiceInterface.
 */
interface GalleryServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;
    public function save(Gallery $gallery): void;

    public function delete(Gallery $gallery): void;

    /**
     * Can Gallery be deleted?
     *
     * @param Gallery $gallery Gallery entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Gallery $gallery): bool;

}
