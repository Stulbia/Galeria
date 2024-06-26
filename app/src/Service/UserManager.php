<?php

/**
 * User Manager.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\AvatarRepository;
use App\Repository\PhotoRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserManager.
 */ class UserManager implements UserManagerInterface
{
/**
 * constructor.
 *
 * @param UserPasswordHasherInterface $passwordHasher   PasswordHasher
 * @param PaginatorInterface          $paginator        Paginator
 * @param UserRepository              $userRepository   UserRepository
 * @param PhotoRepository             $photoRepository  UserRepository
 * @param AvatarRepository            $avatarRepository UserRepository
 *
 */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly PaginatorInterface $paginator, private readonly UserRepository $userRepository, private readonly PhotoRepository $photoRepository, private readonly AvatarRepository $avatarRepository)
    {
    }
    /**
     * saves a new user
     *
     * @param UserInterface $user user
     *
     */
    public function register(UserInterface $user): void
    {
        $password = $user->getPassword();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $this->save($user);
    }

    /**
    /**
     * saves user data changes
     *
     * @param UserInterface $user user
     *
     */
    public function save(UserInterface $user): void
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
        return $this->paginator->paginate($this->userRepository->queryAll(), $page, self::PAGINATOR_ITEMS_PER_PAGE);
    }
    /**
     * Change Password.
     *
     * @param User   $user             User entity
     * @param string $newPlainPassword New Plain Password
     */
    public function upgradePassword(UserInterface $user, string $newPlainPassword): void
    {

        $newHashedPassword = $this->passwordHasher->hashPassword($user, $newPlainPassword);
        $this->userRepository->upgradePassword($user, $newHashedPassword);
    }

    /**
     * Verify Password.
     *
     * @param User   $user          User entity
     * @param string $plainPassword Plain Password
     *
     * @return bool
     */
    public function verifyPassword(UserInterface $user, string $plainPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }
}
