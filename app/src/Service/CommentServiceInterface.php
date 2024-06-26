<?php
/**
 * Comment service interface.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface CommentServiceInterface.
 */
interface CommentServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;
    public function save(Comment $comment, User $user, Photo $photo): void;

    public function delete(Comment $comment): void;

    public function findByPhoto(Photo $photo, int $page): PaginationInterface;
    public function findByUser(User $user, int $page): PaginationInterface;

}
