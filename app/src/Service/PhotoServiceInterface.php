<?php
/**
 * Photo service interface.
 */

namespace App\Service;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    public function save(Photo $photo,UploadedFile $uploadedFile,User $user): void;

    /**
     * Delete entity.
     *
     * @param Photo $photo Photo entity
     */
    public function delete(Photo $photo): void;


    /**
     * Find Photos by Tag Name
     *
     * @param Tag[] $tagName Tag Name
     *
     * @return Photo[]
     */
    public function findByTags(array $tagName): array;
}