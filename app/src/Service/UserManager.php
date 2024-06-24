<?php
/**
 * User Manager.
 */

namespace App\Service;

use App\Entity\Avatar;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\AvatarRepository;
use App\Repository\PhotoRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserManager.
 */ class UserManager implements UserManagerInterface
{
/**
 * constructor.
 *
 * @param PaginatorInterface $paginator        Paginator
 * @param UserRepository     $userRepository   UserRepository
 * @param PhotoRepository    $photoRepository  UserRepository
 * @param AvatarRepository   $avatarRepository UserRepository
 *
 */
    public function __construct(private readonly PaginatorInterface $paginator, private readonly UserRepository $userRepository, private readonly PhotoRepository $photoRepository, private readonly AvatarRepository $avatarRepository)
    {
    }

    /**
     * saves user
     *
     * @param User $user user
     *
     */
    public function save(User $user): void
    {
            $this->userRepository->save($user);
    }
    /**
     * Items per page.
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;
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
            $this->userRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }
    /**
     *Can User be deleted?
     *
     * @param User $user User entity
     *
     * @return bool Result
     */
    public function canBeDeleted(User $user): bool
    {
        try {
            $result = $this-> photoRepository ->countByUser($user);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
    /**
     * Delete entity.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void
    {
        $avatar = $user->getAvatar();
        if ($avatar) {
            $this->avatarRepository->delete($avatar);
        }
        $this->userRepository->delete($user);
    }
}
