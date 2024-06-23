<?php
/**
 * User Manager.
 */

namespace App\Service;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserManager.
 */ class UserManager implements UserManagerInterface
{
/**
 * @param UserPasswordHasherInterface $passwordHasher Password hasher
 * @param PaginatorInterface          $paginator      Paginator
 * @param UserRepository              $userRepository UserRepository
 *
 */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly PaginatorInterface $paginator, private readonly UserRepository $userRepository)
    {
    }
    /**
     * Class UserManager.
     *
     *@param string $password password
     *
     *@param string $email    email
     *
     */
    public function register($password, $email):void
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([UserRole::ROLE_USER->value]);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $password
            )
        );
        $this->userRepository->save($user);
    }

    /**
     * Updates email
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
}
